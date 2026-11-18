<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Http;

/**
 * API error codes (ported down from the platform package).
 *
 * The code is decoupled from the HTTP status code: transport level problems use the HTTP status, business semantics use the code.
 * Add new error codes here, business code must not scatter magic numbers around.
 */
enum ApiErrorCode: int
{
    case BadRequest = 40000;

    case Unauthorized = 40100;

    case TokenExpired = 40101;

    case Forbidden = 40300;

    case NotFound = 40400;

    case StatusConflict = 40900;

    case IdempotencyConflict = 40901;

    case ValidationFailed = 42200;

    case TooManyRequests = 42900;

    case SystemError = 50000;

    case UpstreamError = 50200;

    public function httpStatus(): int
    {
        return match ($this) {
            self::BadRequest                                => 400,
            self::Unauthorized, self::TokenExpired          => 401,
            self::Forbidden                                 => 403,
            self::NotFound                                  => 404,
            self::StatusConflict, self::IdempotencyConflict => 409,
            self::ValidationFailed                          => 422,
            self::TooManyRequests                           => 429,
            self::SystemError                               => 500,
            self::UpstreamError                             => 502,
        };
    }
}
