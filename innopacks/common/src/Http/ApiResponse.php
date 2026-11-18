<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Http;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Unified API response envelope (ported down from the platform package):
 *   { code, message, data, request_id }
 *
 * The panel/frontend json_ok()/json_fail() helpers use the legacy {success,...} structure, the two kinds must not be mixed:
 * /api/* routes go through this class, page ajax calls keep using json_ok/json_fail.
 * request_id is written into the request attributes by InnoCMS\Common\Http\Middleware\RequestId.
 */
class ApiResponse
{
    public static function ok(mixed $data = null, string $message = 'ok', int $status = 200): JsonResponse
    {
        return static::build(0, $message, $data, $status);
    }

    public static function fail(ApiErrorCode $code, string $message, mixed $data = null): JsonResponse
    {
        return static::build($code->value, $message, $data, $code->httpStatus());
    }

    /**
     * Pagination structure: items + pagination, it does not use the default Laravel pagination skeleton.
     */
    public static function page(LengthAwarePaginator $paginator, callable $transform): JsonResponse
    {
        return static::ok([
            'items'      => array_values(array_map($transform, $paginator->items())),
            'pagination' => [
                'page'     => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total'    => $paginator->total(),
            ],
        ]);
    }

    protected static function build(int $code, string $message, mixed $data, int $status): JsonResponse
    {
        return response()->json([
            'code'       => $code,
            'message'    => $message,
            'data'       => $data ?? (object) [],
            'request_id' => request()->attributes->get('request_id', ''),
        ], $status);
    }
}
