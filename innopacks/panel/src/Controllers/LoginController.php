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
use Illuminate\Support\Str;
use InnoCMS\Common\Models\Admin;
use InnoCMS\Panel\Auth\LoginAlerts;
use InnoCMS\Panel\Auth\LoginRateLimiter;
use InnoCMS\Panel\Auth\TotpManager;
use InnoCMS\Panel\Requests\LoginRequest;

class LoginController extends BaseController
{
    protected const PENDING_KEY = 'panel.2fa';

    protected const PENDING_SECRET_KEY = 'panel.2fa_secret';

    /**
     * @return mixed
     */
    public function index(Request $request): mixed
    {
        if (auth('admin')->check()) {
            // Already logged in while visiting the login page: jump to the admin panel home
            // (redirect()->back() falls back to the current URL when there is no Referer,
            // which makes the login page redirect to itself → ERR_TOO_MANY_REDIRECTS)
            return redirect(panel_route('dashboard.index'));
        }

        if ($request->has('locale')) {
            session(['panel_locale' => $request->get('locale')]);

            return redirect(panel_route('login.index'));
        }

        return view('panel::login');
    }

    /**
     * Login post request.
     *
     * After the password passes, branch by the 2FA state (panel.security.force_2fa defaults to
     * false, so existing deployments keep exactly the old behaviour when it is not enabled):
     *  - TOTP already enabled → challenge page
     *  - Force switch on + not bound → setup wizard
     *  - Otherwise → complete the login directly
     */
    public function store(LoginRequest $request): mixed
    {
        $credentials = $request->validated();
        $email       = (string) ($credentials['email'] ?? '');
        $ip          = (string) $request->ip();

        if (LoginRateLimiter::tooManyPasswordAttempts($email, $ip)) {
            LoginAlerts::onLockout($email, $request);

            $seconds = LoginRateLimiter::availableIn(LoginRateLimiter::passwordKey($email, $ip));

            return redirect()->back()->with([
                'error' => __('尝试次数过多，账号已临时锁定，请 :seconds 后再试', ['seconds' => gmdate('i分s秒', $seconds)]),
            ])->withInput();
        }

        // validate() only verifies the credentials without establishing the session:
        // the session is created later in completeLogin() after the 2FA branch.
        if (! auth('admin')->validate($credentials)) {
            LoginRateLimiter::hitPasswordFailure($email, $ip);

            return redirect()->back()->with(['error' => trans('auth.failed')])->withInput();
        }

        LoginRateLimiter::clearPassword($email, $ip);

        /** @var Admin $admin */
        $admin = Admin::query()->where('email', $email)->first();

        session()->regenerate();
        session([static::PENDING_KEY => ['id' => (int) $admin->id, 'token' => Str::random(40)]]);

        if (TotpManager::isEnabled($admin)) {
            return redirect(panel_route('login.challenge'));
        }

        if ((bool) config('panel.security.force_2fa', false)) {
            return redirect(panel_route('login.setup'));
        }

        return $this->completeLogin($admin, $request);
    }

    /**
     * The admin model class configured for the auth provider.
     *
     * Red line: resolve accounts through the runtime provider model, never the bare
     * base class. Downstream products bind a subclass; the spatie role pivot matches
     * by the morph class of the instance, so a login resolved as the base class would
     * lose every role assignment made through the subclass.
     */
    public static function adminModel(): string
    {
        /** @var class-string<Admin> $class */
        $class = (string) config('auth.providers.admins.model', Admin::class);

        return $class;
    }

    /**
     * Read the pending session (overridable): downstream projects may tighten the validation by context.
     *
     * @return array{0: Admin, 1: string}|null [admin, token]
     */
    public static function pendingAdmin(): ?array
    {
        $pending = session(static::PENDING_KEY);
        if (! is_array($pending) || empty($pending['id']) || empty($pending['token'])) {
            return null;
        }

        $adminClass = static::adminModel();

        /** @var Admin $admin */
        $admin = $adminClass::query()->find((int) $pending['id']);
        if (! $admin || ! $admin->active) {
            session()->forget(static::PENDING_KEY);

            return null;
        }

        return [$admin, (string) $pending['token']];
    }

    /**
     * Complete the login: establish the full login session (keeping the base token/session convention).
     */
    protected function completeLogin($admin, Request $request): mixed
    {
        session()->forget([static::PENDING_KEY, static::PENDING_SECRET_KEY]);
        session()->regenerate();

        auth('admin')->login($admin);

        $admin->forceFill(['last_login_at' => now()])->save();

        LoginAlerts::onLogin($admin, $request);

        $admin->tokens()->delete();
        $token = $admin->createToken('admin-token')->plainTextToken;
        session(['panel_api_token' => $token]);

        return redirect(panel_route('home.index'));
    }
}
