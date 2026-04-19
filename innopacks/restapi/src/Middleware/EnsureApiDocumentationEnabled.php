<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\RestAPI\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates Scribe HTML, OpenAPI, and Postman routes behind system setting {@see api_docs_enabled()}.
 */
class EnsureApiDocumentationEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! api_docs_enabled()) {
            abort(404);
        }

        return $next($request);
    }
}
