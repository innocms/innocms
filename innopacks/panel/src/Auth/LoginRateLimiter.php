<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Auth;

use Illuminate\Support\Facades\RateLimiter;

/**
 * Login failure lockout (panel base capability): account + IP dimension.
 *
 * 5 password attempts / 15 minutes, 5 dynamic code attempts / 15 minutes; exceeding the limit is rejected
 * with the remaining time shown.
 * Counting goes through RateLimiter (cache), with no table dependency.
 */
class LoginRateLimiter
{
    public const MAX_PASSWORD_ATTEMPTS = 5;

    public const MAX_CODE_ATTEMPTS = 5;

    public const DECAY_SECONDS = 900;

    public static function passwordKey(string $email, string $ip): string
    {
        return 'panel.login.pw:'.sha1(strtolower($email).'|'.$ip);
    }

    public static function codeKey(int $adminId, string $ip): string
    {
        return 'panel.login.code:'.$adminId.'|'.$ip;
    }

    public static function tooManyPasswordAttempts(string $email, string $ip): bool
    {
        return RateLimiter::tooManyAttempts(static::passwordKey($email, $ip), static::MAX_PASSWORD_ATTEMPTS);
    }

    public static function hitPasswordFailure(string $email, string $ip): void
    {
        RateLimiter::hit(static::passwordKey($email, $ip), static::DECAY_SECONDS);
    }

    public static function clearPassword(string $email, string $ip): void
    {
        RateLimiter::clear(static::passwordKey($email, $ip));
    }

    public static function tooManyCodeAttempts(int $adminId, string $ip): bool
    {
        return RateLimiter::tooManyAttempts(static::codeKey($adminId, $ip), static::MAX_CODE_ATTEMPTS);
    }

    public static function hitCodeFailure(int $adminId, string $ip): void
    {
        RateLimiter::hit(static::codeKey($adminId, $ip), static::DECAY_SECONDS);
    }

    public static function clearCode(int $adminId, string $ip): void
    {
        RateLimiter::clear(static::codeKey($adminId, $ip));
    }

    public static function availableIn(string $key): int
    {
        return RateLimiter::availableIn($key);
    }
}
