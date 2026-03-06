<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;

class MaintenanceMode
{
    /**
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        if (system_setting('maintenance_mode')) {
            if (current_admin()) {
                return $next($request);
            }

            $routeName = pure_route_name();

            if (! in_array($routeName, ['locales.switch'])) {
                return response()->view('maintenance');
            }
        }

        return $next($request);
    }
}
