<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Middleware;

use Closure;
use Illuminate\Http\Request;
use InnoCMS\Common\Models\Admin;
use InnoCMS\Panel\Auth\SensitiveAction;

/**
 * Re-confirmation middleware for sensitive actions (alias sensitive.confirm, sunk down from the platform pack).
 *
 * Pass conditions (either one):
 *  - The request carries a valid confirm_password / confirm_code (the session records a TTL after validation passes)
 *  - The session already holds a confirmation record within the TTL
 * Failure: AJAX returns 403 JSON; a normal request redirects back with 302 and an error.
 */
class RequireRecentConfirmation
{
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var Admin|null $admin */
        $admin = current_admin();

        if (! $admin) {
            return static::reject($request, '登录状态失效，请重新登录');
        }

        if (SensitiveAction::isConfirmed()) {
            return $next($request);
        }

        $via = null;
        if ((string) $request->input('confirm_password', '') !== '') {
            $via = 'password';
        } elseif ((string) $request->input('confirm_code', '') !== '') {
            $via = 'totp';
        }

        if ($via !== null) {
            if (SensitiveAction::verifyRequestCredential($admin, $request)) {
                SensitiveAction::confirmAndAudit($admin, $via, $request);

                return $next($request);
            }

            // Invalid credential: clear the possibly expired timestamp and force re-confirmation
            SensitiveAction::forget();

            return static::reject($request, '二次确认凭据无效，请重试');
        }

        return static::reject($request, '该操作为敏感操作，需要二次确认（输入密码或动态码）');
    }

    protected static function reject(Request $request, string $message): mixed
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], 403);
        }

        return redirect()->back()->with(['error' => $message]);
    }
}
