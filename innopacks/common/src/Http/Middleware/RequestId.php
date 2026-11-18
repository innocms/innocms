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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * request_id propagation (ported down from the platform package): pass through the upstream X-Request-Id, otherwise generate a ULID;
 * it is written into the request attributes (consumed by ApiResponse) + the response header + the log context.
 *
 * It is registered at the very front of the api middleware group, so that even error responses carry the request_id.
 */
class RequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = (string) $request->headers->get('X-Request-Id', '');
        if ($requestId === '' || ! preg_match('/^[A-Za-z0-9_-]{8,64}$/', $requestId)) {
            $requestId = (string) Str::ulid();
        }

        $request->attributes->set('request_id', $requestId);
        Log::withContext(['request_id' => $requestId]);

        $response = $next($request);
        $response->headers->set('X-Request-Id', $requestId);

        return $response;
    }
}
