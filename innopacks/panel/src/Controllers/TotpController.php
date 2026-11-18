<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use Illuminate\Http\Request;
use InnoCMS\Panel\Auth\LoginAlerts;
use InnoCMS\Panel\Auth\LoginRateLimiter;
use InnoCMS\Panel\Auth\TotpManager;

/**
 * Admin panel 2FA (TOTP): challenge verification + first-time setup wizard.
 */
class TotpController extends BaseController
{
    public function challenge(): mixed
    {
        if (! static::pendingAdmin()) {
            return redirect(panel_route('login.index'));
        }

        return view('panel::totp_challenge');
    }

    public function verify(Request $request): mixed
    {
        $pending = static::pendingAdmin();
        if (! $pending) {
            return redirect(panel_route('login.index'));
        }

        [$admin] = $pending;
        $ip      = (string) $request->ip();

        if (LoginRateLimiter::tooManyCodeAttempts((int) $admin->id, $ip)) {
            return redirect(panel_route('login.index'))->with([
                'error' => __('动态码错误次数过多，请重新登录'),
            ]);
        }

        $secret = TotpManager::secretOf($admin);
        if ($secret === null) {
            return redirect(panel_route('login.setup'));
        }

        if (! TotpManager::verify($secret, (string) $request->input('code'))) {
            LoginRateLimiter::hitCodeFailure((int) $admin->id, $ip);

            return redirect(panel_route('login.challenge'))->with(['error' => __('动态码不正确')]);
        }

        LoginRateLimiter::clearCode((int) $admin->id, $ip);

        return $this->completeLogin($admin, $request);
    }

    public function setup(): mixed
    {
        $pending = static::pendingAdmin();
        if (! $pending) {
            return redirect(panel_route('login.index'));
        }

        [$admin] = $pending;

        if (TotpManager::isEnabled($admin)) {
            return redirect(panel_route('login.challenge'));
        }

        $secretKey = 'panel.2fa_secret';
        if (! session($secretKey)) {
            session([$secretKey => TotpManager::generateSecret()]);
        }

        $secret  = (string) session($secretKey);
        $otpauth = TotpManager::provisioningUri($secret, (string) $admin->email);

        return view('panel::totp_setup', [
            'secret'  => $secret,
            'otpauth' => $otpauth,
            'qr'      => TotpManager::qrDataUri($otpauth),
        ]);
    }

    public function enable(Request $request): mixed
    {
        $pending = static::pendingAdmin();
        if (! $pending) {
            return redirect(panel_route('login.index'));
        }

        [$admin]   = $pending;
        $secretKey = 'panel.2fa_secret';
        $secret    = (string) session($secretKey, '');
        $ip        = (string) $request->ip();

        if ($secret === '') {
            return redirect(panel_route('login.setup'));
        }

        if (LoginRateLimiter::tooManyCodeAttempts((int) $admin->id, $ip)) {
            session()->forget([$secretKey]);

            return redirect(panel_route('login.index'))->with([
                'error' => __('动态码错误次数过多，请重新登录'),
            ]);
        }

        if (! TotpManager::confirm($admin, $secret, (string) $request->input('code'))) {
            LoginRateLimiter::hitCodeFailure((int) $admin->id, $ip);

            return redirect(panel_route('login.setup'))->with([
                'error' => __('动态码不正确，请确认已正确添加密钥后重试'),
            ]);
        }

        LoginRateLimiter::clearCode((int) $admin->id, $ip);

        return $this->completeLogin($admin, $request);
    }

    /**
     * Read the pending session (overridable): downstream projects (multi-tenant, etc.) may tighten the validation by context.
     *
     * @return array{0: mixed, 1: string}|null [admin, token]
     */
    protected static function pendingAdmin(): ?array
    {
        return LoginController::pendingAdmin();
    }

    /**
     * 2FA passed, establish the full login session.
     */
    protected function completeLogin($admin, Request $request): mixed
    {
        session()->forget(['panel.2fa', 'panel.2fa_secret']);
        session()->regenerate();

        auth('admin')->login($admin);

        $admin->forceFill(['last_login_at' => now()])->save();

        LoginAlerts::onLogin($admin, $request);

        $admin->tokens()->delete();
        $token = $admin->createToken('admin-token')->plainTextToken;
        session(['panel_api_token' => $token]);

        return redirect()->intended(panel_route('home.index'));
    }
}
