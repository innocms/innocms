<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Auth;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use InnoCMS\Common\Audit\AuditLogger;
use InnoCMS\Common\Models\Admin;
use InnoCMS\Common\Models\AuditLog;
use InnoCMS\Common\Models\Notification;

/**
 * Abnormal login alerts (sunk down from the platform pack, tenant column removed).
 *
 * - Every successful login writes audit_logs (action=auth.login) — both a trace and the source of truth
 *   for "known IP/device"
 * - A first-seen IP / User-Agent additionally writes auth.login.new_ip / auth.login.new_device audit
 *   + notifications(system) alert rows
 * - Actual delivery (in-site/email/WeCom) is consumed from notifications by the downstream event bus
 */
class LoginAlerts
{
    public static function onLogin(Admin $admin, Request $request): void
    {
        $ip        = (string) $request->ip();
        $userAgent = substr((string) $request->userAgent(), 0, 500);

        // All determined against "historically known logins" (excluding the current one), then the current
        // login audit is written — otherwise the current auth.login row would pollute the new IP/new device judgement
        $knownDevice = static::hasHistory($admin->id, $ip, $userAgent);
        $knownIp     = static::hasKnownIp($admin->id, $ip);

        AuditLogger::log('auth.login', 'admin_user', $admin->id, null, [
            'username' => $admin->name,
        ]);

        if (! $knownDevice) {
            static::recordFirstLogin($admin->id, (string) $admin->name, $ip, $userAgent, $knownIp);
        }
    }

    public static function onLockout(string $username, Request $request): void
    {
        AuditLogger::log('auth.login.locked', 'admin_user', null, null, [
            'username' => $username,
            'reason'   => 'too_many_attempts',
        ]);
    }

    protected static function historyQuery(int $adminId): Builder
    {
        return AuditLog::query()
            ->where('action', 'auth.login')
            ->where('actor_id', $adminId)
            ->where('created_at', '>=', now()->subDays((int) config('panel.security.known_login_days', 180)));
    }

    protected static function hasKnownIp(int $adminId, string $ip): bool
    {
        return static::historyQuery($adminId)->where('ip', $ip)->exists();
    }

    protected static function hasHistory(int $adminId, string $ip, string $userAgent): bool
    {
        return static::historyQuery($adminId)
            ->where('ip', $ip)
            ->where('user_agent', $userAgent)
            ->exists();
    }

    protected static function recordFirstLogin(int $adminId, string $username, string $ip, string $userAgent, bool $knownIp): void
    {
        $isNewIp = ! $knownIp;

        $event = $isNewIp ? 'auth.login.new_ip' : 'auth.login.new_device';
        $title = $isNewIp ? '后台登录告警：新 IP' : '后台登录告警：新设备';

        AuditLogger::log($event, 'admin_user', $adminId, null, [
            'username' => $username,
        ]);

        $model = static::notificationModel();

        $model::query()->create([
            'admin_user_id' => $adminId,
            'channel'       => 'system',
            'event'         => $event,
            'title'         => $title,
            'content'       => sprintf(
                '账号 %s 于 %s 从新%s登录（IP: %s）。如非本人操作请立即修改密码并联系管理员。',
                $username,
                now()->format('Y-m-d H:i'),
                $isNewIp ? 'IP' : '设备',
                $ip
            ),
            'payload'    => ['ip' => $ip, 'user_agent' => $userAgent],
            'status'     => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Notification model class (extension point): downstream products may inject a subclass with a tenant column (config('innocms.notification.model')).
     *
     * @return class-string<Model>
     */
    protected static function notificationModel(): string
    {
        /** @var class-string<Model> $class */
        $class = (string) config('innocms.notification.model', Notification::class);

        return $class;
    }
}
