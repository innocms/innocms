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
use InnoCMS\Panel\Repositories\LocaleRepo;

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

if (! function_exists('panel_locales')) {
    /**
     * Get available locales
     *
     * @return array
     * @throws Exception
     */
    function panel_locales(): array
    {
        return LocaleRepo::getInstance()->getPanelLanguages();
    }
}

if (! function_exists('panel_locale_code')) {
    /**
     * Get panel locale code
     *
     * @return string
     * @throws Exception
     */
    function panel_locale_code(): string
    {
        return current_admin()->locale ?? session('panel_locale', setting_locale_code());
    }
}

if (! function_exists('current_panel_locale')) {
    /**
     * Get current locale code.
     *
     * @return array
     * @throws Exception
     */
    function current_panel_locale(): array
    {
        return LocaleRepo::getInstance()->getLocaleByCode(panel_locale_code());
    }
}

if (! function_exists('panel_lang_path_codes')) {
    /**
     * Get all panel languages
     *
     * @return array
     */
    function panel_lang_path_codes(): array
    {
        $localeCodes = array_values(array_diff(scandir(lang_path()), ['..', '.', '.DS_Store']));

        return array_values(array_filter($localeCodes, function ($code) {
            return file_exists(lang_path("{$code}/panel"));
        }));
    }
}

if (! function_exists('panel_lang_dir')) {
    /**
     * Get all panel languages
     *
     * @return string
     */
    function panel_lang_dir(): string
    {
        return lang_path('panel');
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
        } catch (Exception $e) {
            return route($panelName.'.dashboard.index');
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

if (! function_exists('has_translator')) {
    /**
     * Check if the translator is enabled.
     */
    function has_translator(): bool
    {
        return false;
    }
}

if (! function_exists('is_setting_locale')) {
    /**
     * Check if locale is the default setting locale.
     */
    function is_setting_locale($localeCode): bool
    {
        return setting_locale_code() == $localeCode;
    }
}

if (! function_exists('default_locale_class')) {
    /**
     * Get CSS class for default locale marker.
     */
    function default_locale_class($localeCode): string
    {
        return is_setting_locale($localeCode) ? 'border border-2 border-danger-subtle ' : '';
    }
}

if (! function_exists('locale_field_data')) {
    /**
     * Get locale field data for a model (translations array).
     */
    function locale_field_data($model, string $fieldName): array
    {
        $data = [];
        foreach (locales() as $locale) {
            $code  = $locale->code;
            $value = old("translations.{$code}.{$fieldName}");
            if ($value === null && $model) {
                $value = $model->translate($code, $fieldName);
            }
            $data[$code] = (string) ($value ?? '');
        }

        return $data;
    }
}
