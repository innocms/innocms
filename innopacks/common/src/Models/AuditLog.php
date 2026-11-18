<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Audit log (ported down from the platform package). Append only: the model disables update (audit records must not be tampered with).
 *
 * @property int $id
 * @property string $actor_type
 * @property int|null $actor_id
 * @property string $action
 * @property string $resource_type
 * @property string $resource_id
 * @property array|null $before_json
 * @property array|null $after_json
 * @property string $ip
 * @property string $user_agent
 * @property Carbon|null $created_at
 */
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    public const ACTOR_ADMIN = 'admin';

    public const ACTOR_USER = 'user';

    public const ACTOR_WORKER = 'worker';

    public const ACTOR_SYSTEM = 'system';

    public const ACTOR_PLATFORM = 'platform';

    protected $table = 'audit_logs';

    protected $fillable = [
        'actor_type', 'actor_id', 'action',
        'resource_type', 'resource_id', 'before_json', 'after_json',
        'ip', 'user_agent', 'created_at',
    ];

    protected $casts = [
        'before_json' => 'array',
        'after_json'  => 'array',
        'created_at'  => 'datetime',
    ];

    /**
     * Audit records must not be updated.
     */
    public function save(array $options = []): bool
    {
        if ($this->exists) {
            throw new \RuntimeException('audit_logs 是只增表，禁止更新（id='.$this->id.'）');
        }

        return parent::save($options);
    }
}
