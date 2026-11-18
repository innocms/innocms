<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Audit;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InnoCMS\Common\Models\AuditLog;

/**
 * Audit log recorder (ported down from the platform package).
 *
 * Usage rules (red lines, must be checked on review):
 *  - Sensitive actions such as PII decrypted views, key viewing/replacement, role and permission changes must go through here
 *  - A failed write does not throw (an audit failure must not block business), it falls back to a Log::error alert
 */
class AuditLogger
{
    /**
     * Record one audit log entry.
     *
     * @param  string  $action  Dot-separated action name, e.g. pii.view / rbac.role.assign / credential.replace
     * @param  array|null  $before  Snapshot before the change (sensitive fields are masked only, never in clear text)
     * @param  array|null  $after  Snapshot after the change (same as above)
     */
    public static function log(
        string $action,
        string $resourceType,
        string|int|null $resourceId = null,
        ?array $before = null,
        ?array $after = null,
        ?string $actorType = null,
        ?int $actorId = null,
    ): void {
        try {
            $request = static::currentRequest();
            $model   = static::model();

            $model::query()->create([
                'actor_type'    => $actorType ?? static::detectActorType(),
                'actor_id'      => $actorId ?? static::detectActorId(),
                'action'        => $action,
                'resource_type' => $resourceType,
                'resource_id'   => (string) ($resourceId ?? ''),
                'before_json'   => $before,
                'after_json'    => $after,
                'ip'            => $request?->ip() ?? '',
                'user_agent'    => substr((string) $request?->userAgent(), 0, 500),
                'created_at'    => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('audit.write_failed', ['action' => $action, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Dedicated helper for viewing PII sensitive fields (action is fixed as pii.view, red line: a decrypted view must always leave a trace).
     *
     * @param  array  $fields  List of the viewed fields, e.g. ['legal_person_id_no']
     */
    public static function logPiiView(string $resourceType, string|int $resourceId, array $fields): void
    {
        static::log('pii.view', $resourceType, $resourceId, null, ['fields' => $fields]);
    }

    /**
     * Audit query (list / filter).
     *
     * @param  array{action?: string, resource_type?: string, resource_id?: string, actor_type?: string, actor_id?: int, date_from?: string, date_to?: string}  $filters
     * @return LengthAwarePaginator
     */
    public static function paginate(array $filters = [], int $perPage = 20)
    {
        $query = static::model()::query();

        foreach (['action', 'resource_type', 'resource_id', 'actor_type'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        if (! empty($filters['actor_id'])) {
            $query->where('actor_id', (int) $filters['actor_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from'].' 00:00:00');
        }

        if (! empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to'].' 23:59:59');
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    protected static function currentRequest(): ?Request
    {
        return app()->bound('request') ? request() : null;
    }

    /**
     * Audit model class (extension point): downstream products may inject a subclass carrying a tenant column (config('innocms.audit.model')).
     */
    protected static function model(): string
    {
        /** @var class-string<Model> $class */
        $class = (string) config('innocms.audit.model', AuditLog::class);

        return $class;
    }

    /**
     * Best-effort detection of the current actor. After authentication takes over, the caller may explicitly pass actor_type/actor_id to override it.
     */
    protected static function detectActorType(): string
    {
        try {
            if (function_exists('current_admin') && current_admin()) {
                return AuditLog::ACTOR_ADMIN;
            }
        } catch (\Throwable) {
            // guard not ready yet (CLI / installation period), fall back to system
        }

        return AuditLog::ACTOR_SYSTEM;
    }

    protected static function detectActorId(): ?int
    {
        try {
            $admin = function_exists('current_admin') ? current_admin() : null;
            if ($admin) {
                return (int) $admin->id;
            }
        } catch (\Throwable) {
            // same as above
        }

        return null;
    }
}
