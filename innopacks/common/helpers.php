<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use InnoCMS\Common\Repositories\LocaleRepo;
use InnoCMS\Common\Repositories\SettingRepo;
use InnoCMS\Common\Services\ImageService;

if (! function_exists('load_settings')) {
    /**
     * @return void
     */
    function load_settings(): void
    {
        if (! installed()) {
            return;
        }
        $result = SettingRepo::getInstance()->groupedSettings();
        config(['inno' => $result]);
    }
}

if (! function_exists('setting')) {
    /**
     * 获取后台设置到 settings 表的值
     *
     * @param  $key
     * @param  null  $default
     * @return mixed
     */
    function setting($key, $default = null): mixed
    {
        return config("inno.{$key}", $default);
    }
}

if (! function_exists('system_setting')) {
    /**
     * Get system settings
     *
     * @param  $key
     * @param  null  $default
     * @return mixed
     */
    function system_setting($key, $default = null): mixed
    {
        return setting("system.{$key}", $default);
    }
}

if (! function_exists('is_secure')) {
    /**
     * Check if current env is https
     *
     * @return bool
     */
    function is_secure(): bool
    {
        if (! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
            return true;
        } elseif (! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        } elseif (isset($_SERVER['SERVER_PORT']) && intval($_SERVER['SERVER_PORT']) === 443) {
            return true;
        } elseif (isset($_SERVER['REQUEST_SCHEME']) && strtolower($_SERVER['REQUEST_SCHEME']) === 'https') {
            return true;
        }

        return false;
    }
}

if (! function_exists('installed')) {
    /**
     * @return bool
     */
    function installed(): bool
    {
        try {
            if (Schema::hasTable('settings') && file_exists(storage_path('installed'))) {
                return true;
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return false;
        }

        return false;
    }
}

if (! function_exists('inno_path')) {
    /**
     * Get innopack path
     *
     * @param  string  $path
     * @return string
     */
    function inno_path(string $path): string
    {
        return base_path("innopacks/{$path}");
    }
}

if (! function_exists('current_customer')) {
    /**
     * get current admin user.
     */
    function current_customer(): ?Authenticatable
    {
        return auth('web')->user();
    }
}

if (! function_exists('current_customer_id')) {
    /**
     * Get current customer ID
     *
     * @return int
     */
    function current_customer_id(): int
    {
        $customer = current_customer();

        return $customer->id ?? 0;
    }
}

if (! function_exists('current_guest_id')) {
    /**
     * Get guest ID from session ID
     *
     * @return string
     */
    function current_guest_id(): string
    {
        return session()->getId();
    }
}

if (! function_exists('locales')) {
    /**
     * Get available locales
     *
     * @return mixed
     * @throws Exception
     */
    function locales(): mixed
    {
        return LocaleRepo::getInstance()->getActiveList();
    }
}

if (! function_exists('front_locale_code')) {
    /**
     * Get current locale code.
     *
     * @return string
     */
    function front_locale_code(): string
    {
        return \session('locale') ?? system_setting('front_locale', config('app.locale'));
    }
}

if (! function_exists('locale_code')) {
    /**
     * Get current locale code.
     *
     * @return string
     * @throws Exception
     */
    function locale_code(): string
    {
        $configLocale = config('app.locale');
        if (is_admin()) {
            $locale = current_admin()->locale ?? $configLocale;
            if (locales()->contains('code', $locale)) {
                return $locale;
            }
        }

        return \session('locale') ?? system_setting('front_locale', $configLocale);
    }
}

if (! function_exists('current_locale')) {
    /**
     * Get current locale code.
     *
     * @return mixed
     * @throws Exception
     */
    function current_locale(): mixed
    {
        return LocaleRepo::getInstance()->builder(['code' => front_locale_code()])->first();
    }
}

if (! function_exists('front_lang_path_codes')) {
    /**
     * Get all panel languages
     *
     * @return array
     */
    function front_lang_path_codes(): array
    {
        $languageDir = front_lang_dir();

        return array_values(array_diff(scandir($languageDir), ['..', '.', '.DS_Store']));
    }
}

if (! function_exists('front_lang_dir')) {
    /**
     * Get all panel languages
     *
     * @return string
     */
    function front_lang_dir(): string
    {
        if (is_dir(lang_path('vendor/front'))) {
            $languageDir = lang_path('vendor/front');
        } else {
            $languageDir = inno_path('panel/lang');
        }

        return $languageDir;
    }
}

if (! function_exists('json_success')) {
    /**
     * @param  $message
     * @param  $data
     * @return JsonResponse
     */
    function json_success($message, $data = null): JsonResponse
    {
        if ($data instanceof Model) {
            $data = $data->toArray();
        }

        $json = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];

        return response()->json($json);
    }
}

if (! function_exists('json_fail')) {
    /**
     * @param  $message
     * @param  $data
     * @param  int  $code
     * @return JsonResponse
     */
    function json_fail($message, $data = null, int $code = 422): JsonResponse
    {
        if ($data instanceof Model) {
            $data = $data->toArray();
        }

        $json = [
            'success' => false,
            'message' => $message,
            'data'    => $data,
        ];

        return response()->json($json, $code);
    }
}

if (! function_exists('image_resize')) {
    /**
     * Resize image
     *
     * @param  string|null  $image
     * @param  int  $width
     * @param  int  $height
     * @return string
     * @throws Exception
     */
    function image_resize(?string $image = '', int $width = 100, int $height = 100): string
    {
        if (Str::startsWith($image, 'http')) {
            return $image;
        }

        return (new ImageService($image))->resize($width, $height);
    }
}

if (! function_exists('image_origin')) {
    /**
     * Get origin image
     *
     * @throws Exception
     */
    function image_origin($image)
    {
        if (Str::startsWith($image, 'http')) {
            return $image;
        }

        return (new ImageService($image))->originUrl();
    }
}

if (! function_exists('sub_string')) {
    /**
     * @param  $string
     * @param  int  $length
     * @param  string  $dot
     * @return string
     */
    function sub_string($string, int $length = 16, string $dot = '...'): string
    {
        $string    = (string) $string;
        $strLength = mb_strlen($string);
        if ($length <= 0) {
            return $string;
        } elseif ($strLength <= $length) {
            return $string;
        }

        return mb_substr($string, 0, $length).$dot;
    }
}

if (! function_exists('create_directories')) {
    /**
     * Create directories recursively
     *
     * @param  $directoryPath
     * @return void
     */
    function create_directories($directoryPath): void
    {
        $ds   = DIRECTORY_SEPARATOR;
        $path = '';

        $directoryPath = str_replace(['/', '\\'], $ds, $directoryPath);
        if (substr($directoryPath, 0, 1) === $ds) {
            $path = $ds;
        }

        $directories = explode($ds, $directoryPath);
        foreach ($directories as $directory) {
            if ($directory === '') {
                continue;
            }

            if ($path === '' || $path === $ds) {
                $path .= $directory;
            } else {
                $path .= $ds.$directory;
            }

            if (! is_dir($path)) {
                if (! @mkdir($path, 0755, true) && ! is_dir($path)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
                }
            }
        }
    }
}

if (! function_exists('front_route')) {
    /**
     * Get backend panel route
     *
     * @param  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    function front_route($name, mixed $parameters = [], bool $absolute = true): string
    {
        return route('front.'.$name, $parameters, $absolute);
    }
}

if (! function_exists('equal_route_name')) {
    /**
     * Check route is current
     *
     * @param  $routeName
     * @return bool
     */
    function equal_route_name($routeName): bool
    {
        $currentRouteName = Route::getCurrentRoute()->getName();
        if (is_string($routeName)) {
            return $currentRouteName == $routeName;
        } elseif (is_array($routeName)) {
            return in_array($currentRouteName, $routeName);
        }

        return false;
    }
}

if (! function_exists('equal_route_param')) {
    /**
     * Check route is current
     *
     * @param  $routeName
     * @param  array  $parameters
     * @return bool
     */
    function equal_route_param($routeName, array $parameters = []): bool
    {
        $currentRouteName = Route::getCurrentRoute()->getName();
        if ($routeName != $currentRouteName) {
            return false;
        }

        $currentRouteParameters = Route::getCurrentRoute()->parameters();

        return $parameters == $currentRouteParameters;
    }
}

if (! function_exists('equal_url')) {
    /**
     * Check url equal current.
     *
     * @param  $url
     * @return bool
     */
    function equal_url($url): bool
    {
        return url()->current() == $url;
    }
}

if (! function_exists('locales')) {
    /**
     * Get available locales
     *
     * @return mixed
     * @throws Exception
     */
    function locales(): mixed
    {
        return LocaleRepo::getInstance()->getActiveList();
    }
}

if (! function_exists('has_debugbar')) {
    /**
     * Check debugbar installed or not
     *
     * @return bool
     */
    function has_debugbar(): bool
    {
        return class_exists(Debugbar::class);
    }
}

if (! function_exists('currencies')) {
    /**
     * @return mixed
     */
    function currencies(): mixed
    {
        return CurrencyRepo::getInstance()->enabledList();
    }
}

if (! function_exists('current_currency')) {
    /**
     * @return mixed
     */
    function current_currency(): mixed
    {
        return currencies()->where('code', current_currency_code())->first();
    }
}

if (! function_exists('current_currency_code')) {
    /**
     * @return string
     */
    function current_currency_code(): string
    {
        return Session::get('currency') ?? system_setting('currency', 'usd');
    }
}

if (! function_exists('theme_path')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @return string
     */
    function theme_path(string $path): string
    {
        return base_path('themes/'.$path);
    }
}

if (! function_exists('theme_asset')) {
    /**
     * Generate asset path for the theme, demo code like below:
     * <link rel="stylesheet" href="{{ theme_asset('default','swiper-bundle.min.css') }}">,
     * swiper-bundle.min.css is in /themes/default/public
     *
     * @param  string  $theme
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function theme_asset(string $theme, string $path, ?bool $secure = null): string
    {
        $originThemePath = "$theme/public/$path";
        $destThemePath   = "themes/$theme/$path";
        if (! file_exists(public_path($destThemePath))) {
            create_directories(dirname(public_path($destThemePath)));
            copy(theme_path($originThemePath), public_path($destThemePath));
        }

        return app('url')->asset($destThemePath, $secure);
    }
}

if (! function_exists('innoshop_version')) {
    /**
     * Generate an asset path for the application.
     *
     * @return string
     */
    function innocms_version(): string
    {
        return 'v'.config('innocms.version').'('.config('innocms.build').')';
    }
}

if (! function_exists('to_sql')) {
    /**
     * Render SQL by builder object
     * @param  mixed  $builder
     * @return string
     */
    function to_sql(Builder $builder): string
    {
        $sql    = $builder->toSql();
        $driver = DB::getDriverName();
        if ($driver == 'mysql') {
            $sql = str_replace('"', '`', $sql);
        }

        foreach ($builder->getBindings() as $binding) {
            $value = is_numeric($binding) ? $binding : "'".$binding."'";
            $sql   = preg_replace('/\?/', $value, $sql, 1);
        }

        return $sql;
    }
}
