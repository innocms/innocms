<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use InnoCMS\Common\Audit\AuditLogger;
use InnoCMS\Common\Models\Admin;

/**
 * Re-confirmation for sensitive actions (sunk down from the platform pack, tenant column removed).
 *
 * Applicable actions (red lines, must be checked in review): secret viewing/replacement, refund approval,
 * role permission changes, PII decryption.
 * Mechanism: the request carries confirm_password (password) or confirm_code (TOTP dynamic code) → after validation
 * passes, the confirmation time is recorded in the session; subsequent sensitive actions within the TTL do not
 * require repeated input; every confirmation writes an audit log.
 *
 * Usage: attach the sensitive.confirm middleware to the route (RequireRecentConfirmation).
 */
class SensitiveAction
{
    public const SESSION_KEY = 'panel.sensitive_confirmed_at';

    /**
     * Whether the current session holds a valid confirmation (within the TTL).
     */
    public static function isConfirmed(): bool
    {
        $at = session(static::SESSION_KEY);

        return is_numeric($at)
            && (time() - (int) $at) <= (int) config('panel.security.sensitive_confirm_ttl', 600);
    }

    /**
     * Mark the session as confirmed (called after validation passes).
     */
    public static function markConfirmed(): void
    {
        session([static::SESSION_KEY => time()]);
    }

    public static function forget(): void
    {
        session()->forget(static::SESSION_KEY);
    }

    /**
     * Verify the re-confirmation credential carried by the request.
     *
     * @return bool true when either the password or the dynamic code validates
     */
    public static function verifyRequestCredential(Admin $admin, Request $request): bool
    {
        $password = (string) $request->input('confirm_password', '');
        if ($password !== '' && Hash::check($password, $admin->password)) {
            return true;
        }

        $code = (string) $request->input('confirm_code', '');
        if ($code !== '') {
            $secret = TotpManager::secretOf($admin);
            if ($secret !== null && TotpManager::verify($secret, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Confirmation passed and recorded.
     */
    public static function confirmAndAudit(Admin $admin, string $via, Request $request): void
    {
        static::markConfirmed();

        AuditLogger::log('security.sensitive_confirm', 'admin_user', $admin->id, null, [
            'via' => $via,
        ]);
    }
}
