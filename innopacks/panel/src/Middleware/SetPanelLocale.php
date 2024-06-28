<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Middleware;

use Illuminate\Http\Request;

class SetPanelLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws \Exception
     */
    public function handle(Request $request, \Closure $next): mixed
    {
        $frontLocales  = locales();
        $panelLocales  = panel_locales();
        $currentLocale = panel_locale_code();
        if (collect($panelLocales)->contains('code', $currentLocale)) {
            if (! $frontLocales->contains('code', $currentLocale)) {
                session(['locale' => front_locale_code()]);
            }
            app()->setLocale($currentLocale);
        } else {
            app()->setLocale(front_locale_code());
        }

        return $next($request);
    }
}
