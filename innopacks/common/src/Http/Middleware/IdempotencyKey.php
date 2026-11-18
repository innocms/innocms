<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use InnoCMS\Common\Http\ApiErrorCode;
use InnoCMS\Common\Http\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Idempotency-Key handling (ported down from the platform package): used by write operations such as create/update/payment/refund.
 *
 * Behaviour:
 *  - Same key with a stored response → replay it as is (prevents duplicate orders / duplicate refunds)
 *  - Same key while in progress (the lock exists) → 40901 idempotency conflict
 *  - New key → acquire the processing lock and pass through; store the snapshot after a 2xx/3xx response (valid for 24h)
 *
 * It only takes effect for requests carrying the Idempotency-Key header; the key is bound to "the logged-in panel account + method + path" to prevent cross-use
 * (the platform version isolates by tenant_id, the base has no tenant concept, so the logged-in account dimension is used instead).
 */
class IdempotencyKey
{
    protected const TTL = 86400;

    public const HEADER = 'Idempotency-Key';

    /**
     * Unified key format rule (shared by the client and server validation entry points).
     */
    public static function isValidKey(string $key): bool
    {
        return preg_match('/^[A-Za-z0-9_-]{8,64}$/', $key) === 1;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = (string) $request->header(static::HEADER, '');
        if (! static::isValidKey($key)) {
            return $next($request);
        }

        $scope = $this->scopeKey($request, $key);
        $lock  = Cache::get("idem.lock.{$scope}");
        if ($lock !== null) {
            return ApiResponse::fail(ApiErrorCode::IdempotencyConflict, '相同幂等键的请求正在处理中，请稍后重试');
        }

        $cached = Cache::get("idem.res.{$scope}");
        if ($cached !== null) {
            $payload = unserialize($cached);

            return response()->json($payload['body'], $payload['status'])->withHeaders($payload['headers'] ?? []);
        }

        Cache::put("idem.lock.{$scope}", 1, 30);

        try {
            $response = $next($request);

            if ($response->isSuccessful() || $response->isRedirection()) {
                Cache::put("idem.res.{$scope}", serialize([
                    'status'  => $response->getStatusCode(),
                    'body'    => json_decode($response->getContent(), true),
                    'headers' => [],
                ]), self::TTL);
            }

            return $response;
        } finally {
            Cache::forget("idem.lock.{$scope}");
        }
    }

    protected function scopeKey(Request $request, string $key): string
    {
        $actorId = auth('admin')->id() ?? 0;

        return "{$actorId}.{$request->method()}.{$request->path()}.{$key}";
    }
}
