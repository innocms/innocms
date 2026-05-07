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
use Illuminate\Validation\ValidationException;
use InnoCMS\Panel\Requests\LoginRequest;

class CliLoginController extends BaseController
{
    /**
     * Show the CLI login page or redirect if already authenticated.
     * If already logged in, generate a token and redirect to the callback URL immediately.
     */
    public function index(Request $request): mixed
    {
        $callback = $request->query('callback', '');

        // Validate callback URL - must be localhost/127.0.0.1
        if (! $this->isValidCallback($callback)) {
            return response('Invalid callback URL. Only localhost callbacks are allowed.', 400);
        }

        // Already authenticated -> generate token and redirect
        if (auth('admin')->check()) {
            return $this->redirectWithToken($callback);
        }

        // Store callback in session for use after login
        session(['cli_login_callback' => $callback]);

        return view('panel::cli_login');
    }

    /**
     * Handle login POST from the CLI login form.
     */
    public function store(LoginRequest $request): mixed
    {
        $callback = session('cli_login_callback', '');

        if (! auth('admin')->attempt($request->validated())) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        // Clear the session callback
        session()->forget('cli_login_callback');

        // Generate token and redirect
        if ($this->isValidCallback($callback)) {
            return $this->redirectWithToken($callback);
        }

        // Fallback: no valid callback, go to panel home
        return redirect(panel_route('home.index'));
    }

    /**
     * Generate a Sanctum token for the current admin and redirect to callback.
     */
    private function redirectWithToken(string $callback): mixed
    {
        $admin = auth('admin')->user();
        $token = $admin->createToken('cli-token')->plainTextToken;

        $separator = str_contains($callback, '?') ? '&' : '?';

        return redirect($callback.$separator.'token='.urlencode($token));
    }

    /**
     * Validate that the callback URL points to localhost.
     */
    private function isValidCallback(string $callback): bool
    {
        if ($callback === '') {
            return false;
        }

        $parsed = parse_url($callback);
        if (! isset($parsed['host'])) {
            return false;
        }

        $host = $parsed['host'];

        return in_array($host, ['localhost', '127.0.0.1', '[::1]'], true);
    }
}
