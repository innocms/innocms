<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

if (! function_exists('panel_name')) {
    /**
     * Admin panel name
     *
     * @return string
     */
    function panel_name(): string
    {
        return 'panel';
    }
}

if (! function_exists('panel_locale')) {
    /**
     * Get panel locale code
     *
     * @return string
     */
    function panel_locale(): string
    {
        if (is_admin()) {
            return current_admin()->locale;
        }

        return locale();
    }
}

if (! function_exists('panel_languages')) {
    /**
     * Get all panel languages
     *
     * @return array
     */
    function panel_languages(): array
    {
        $languageDir = inno_path('panel/lang');

        return array_values(array_diff(scandir($languageDir), ['..', '.', '.DS_Store']));
    }
}

if (! function_exists('panel_route')) {
    /**
     * Get backend panel route
     *
     * @param  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    function panel_route($name, mixed $parameters = [], bool $absolute = true): string
    {
        try {
            $panelName = panel_name();

            return route($panelName.'.'.$name, $parameters, $absolute);
        } catch (\Exception $e) {
            return route($panelName.'.home.index');
        }

    }
}

if (! function_exists('current_admin')) {
    /**
     * get current admin user.
     */
    function current_admin(): ?Authenticatable
    {
        return auth('admin')->user();
    }
}

if (! function_exists('is_admin')) {
    /**
     * Check if current is admin panel
     * @return bool
     */
    function is_admin(): bool
    {
        $adminName = panel_name();
        $uri       = request()->getRequestUri();
        if (Str::startsWith($uri, "/{$adminName}")) {
            return true;
        }

        return false;
    }
}
