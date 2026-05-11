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
use Illuminate\Support\Str;
use InnoCMS\Common\Libraries\ApiHook;
use InnoCMS\Common\Libraries\ViewHook;
use InnoCMS\Common\Repositories\LocaleRepo;
use InnoCMS\Common\Repositories\SettingRepo;
use InnoCMS\Common\Services\ImageService;
use InnoCMS\Common\Services\StorageService;

if (! function_exists('load_settings')) {
    /**
     * @return void
     */
    function load_settings(): void
    {
        if (! installed()) {
            return;
        }

        if (config('inno')) {
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

if (! function_exists('system_setting_locale')) {
    /**
     * Get system setting for current locale
     *
     * @param  $key
     * @param  null  $default
     * @return mixed
     */
    function system_setting_locale($key, $default = null): mixed
    {
        $localeCode = front_locale_code();

        return setting("system.{$key}.$localeCode", $default);
    }
}

if (! function_exists('enabled_locale_codes')) {
    /**
     * Get available locale codes
     *
     * @return array
     */
    function enabled_locale_codes(): array
    {
        return locales()->pluck('code')->toArray();
    }
}

if (! function_exists('setting_locale_code')) {
    /**
     * Get setting locale code.
     *
     * @return string
     */
    function setting_locale_code(): string
    {
        return system_setting('front_locale', config('app.locale', 'en'));
    }
}

if (! function_exists('hide_url_locale')) {
    /**
     * @return bool
     */
    function hide_url_locale(): bool
    {
        return count(locales()) == 1 && system_setting('hide_url_locale');
    }
}

if (! function_exists('is_mobile')) {
    /**
     * Check if current request is from mobile device.
     *
     * @return bool
     */
    function is_mobile(): bool
    {
        $userAgent = request()->userAgent();
        if (empty($userAgent)) {
            return false;
        }
        $mobileKeywords = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'Windows Phone', 'BlackBerry'];

        foreach ($mobileKeywords as $keyword) {
            if (stripos($userAgent, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('cache_key')) {
    /**
     * Generate cache key with locale context
     *
     * @param  string  $name
     * @param  array  $params
     * @return string
     */
    function cache_key(string $name, array $params = []): string
    {
        $params['locale_code'] = front_locale_code();

        return $name.'-'.md5(json_encode($params));
    }
}

if (! function_exists('ini_size_to_bytes')) {
    /**
     * Convert PHP ini size value to bytes
     *
     * @param  string  $size  PHP ini size value (e.g. "8M", "2G")
     * @return int
     */
    function ini_size_to_bytes(string $size): int
    {
        $unit  = strtoupper(substr($size, -1));
        $value = (int) substr($size, 0, -1);

        switch ($unit) {
            case 'K':
                return $value * 1024;
            case 'M':
                return $value * 1024 * 1024;
            case 'G':
                return $value * 1024 * 1024 * 1024;
            default:
                return (int) $size;
        }
    }
}

if (! function_exists('smart_log')) {
    /**
     * Smart logging function that respects debug mode and log levels
     *
     * @param  string  $level
     * @param  string  $message
     * @param  array  $context
     * @param  bool|null  $force
     * @return void
     */
    function smart_log(string $level, string $message, array $context = [], ?bool $force = null): void
    {
        $level       = strtolower($level);
        $validLevels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

        if (! in_array($level, $validLevels)) {
            $level = 'info';
        }

        $isDebug        = config('app.debug', false);
        $criticalLevels = ['error', 'critical', 'alert', 'emergency'];
        $shouldLog      = false;

        if ($force === true) {
            $shouldLog = true;
        } elseif ($force === false) {
            $shouldLog = false;
        } elseif (in_array($level, $criticalLevels)) {
            $shouldLog = true;
        } elseif ($isDebug) {
            $shouldLog = true;
        }

        if (! $shouldLog) {
            return;
        }

        Log::{$level}($message, $context);
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
        } catch (Exception $e) {
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
        return session('locale') ?? setting_locale_code();
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
        $localeCodes = array_values(array_diff(scandir(lang_path()), ['..', '.', '.DS_Store']));

        return array_values(array_filter($localeCodes, function ($code) {
            return is_dir(lang_path($code));
        }));
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
        return lang_path('front');
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

if (! function_exists('common_trans')) {
    /**
     * Translate keys used by REST API responses (e.g. base.updated_success).
     */
    function common_trans(string $key): string
    {
        $parts = explode('.', $key, 2);
        if (($parts[0] ?? '') === 'base' && isset($parts[1])) {
            return trans('panel/common.'.$parts[1]);
        }

        return trans($key);
    }
}

if (! function_exists('create_json_success')) {
    /**
     * JSON response for successful create/store operations.
     *
     * @param  mixed  $data
     */
    function create_json_success($data = null): JsonResponse
    {
        $hook = ApiHook::getInstance()->getHookName(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3));
        if ($hook) {
            $data = fire_hook_filter($hook, $data);
        }

        return json_success(common_trans('base.saved_success'), $data);
    }
}

if (! function_exists('read_json_success')) {
    /**
     * JSON response for successful read/show operations.
     *
     * @param  mixed  $data
     */
    function read_json_success($data = null): JsonResponse
    {
        $hook = ApiHook::getInstance()->getHookName(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3));
        if ($hook) {
            $data = fire_hook_filter($hook, $data);
        }

        return json_success(common_trans('base.read_success'), $data);
    }
}

if (! function_exists('update_json_success')) {
    /**
     * JSON response after successful update.
     *
     * @param  mixed  $data
     */
    function update_json_success($data = null): JsonResponse
    {
        $hook = ApiHook::getInstance()->getHookName(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3));
        if ($hook) {
            $data = fire_hook_filter($hook, $data);
        }

        return json_success(common_trans('base.updated_success'), $data);
    }
}

if (! function_exists('storage_url')) {
    /**
     * Generate file URL based on current storage driver configuration.
     *
     * @param  ?string  $path
     * @return string
     */
    function storage_url(?string $path): string
    {
        if (empty($path)) {
            return '';
        }
        if (Str::startsWith($path, 'http')) {
            return $path;
        }

        return StorageService::getInstance()->url($path);
    }
}

if (! function_exists('image_resize')) {
    /**
     * Resize image
     *
     * @param  string|null  $image
     * @param  int  $width
     * @param  int  $height
     * @param  string|null  $mode
     * @return string
     * @throws Exception
     */
    function image_resize(?string $image = '', int $width = 100, int $height = 100, ?string $mode = null): string
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
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
                }
            }
        }
    }
}

if (! function_exists('front_route')) {
    /**
     * Get frontend route with locale support
     *
     * @param  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    function front_route($name, mixed $parameters = [], bool $absolute = true): string
    {
        if (hide_url_locale() || locales()->isEmpty()) {
            return route('front.'.$name, $parameters, $absolute);
        }

        return route(front_locale_code().'.front.'.$name, $parameters, $absolute);
    }
}

if (! function_exists('front_root_route')) {
    /**
     * Get frontend root route (always without locale prefix)
     *
     * @param  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    function front_root_route($name, mixed $parameters = [], bool $absolute = true): string
    {
        return route('front.'.$name, $parameters, $absolute);
    }
}

if (! function_exists('has_front_route')) {
    /**
     * Check if frontend route exists
     *
     * @param  $name
     * @return bool
     */
    function has_front_route($name): bool
    {
        if (hide_url_locale() || locales()->isEmpty()) {
            $route = 'front.'.$name;
        } else {
            $route = front_locale_code().'.front.'.$name;
        }

        return Route::has($route);
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
    function equal_url(string $url): bool
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

if (! function_exists('inno_view')) {
    /**
     * Render a view after optional ViewHook data filter (same pattern as InnoShop Factory).
     *
     * @param  mixed  $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return mixed
     */
    function inno_view($view = null, array $data = [], array $mergeData = []): mixed
    {
        $hook = ViewHook::getInstance()->getHookName(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 8));
        if ($hook) {
            $data = fire_hook_filter($hook, $data);
        }

        return view($view, $data, $mergeData);
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

if (! function_exists('should_copy_static_file')) {
    /**
     * Copy a theme static asset into public/ when missing or stale (same idea as InnoShop Factory).
     * Used by theme_asset(); does not copy directories or missing sources.
     *
     * @param  string  $sourceFile  Absolute path under themes/{code}/public/...
     * @param  string  $destFile  Absolute path under public/themes/{code}/...
     * @return bool True when the destination file exists after the operation
     */
    function should_copy_static_file(string $sourceFile, string $destFile): bool
    {
        if (file_exists($sourceFile) && is_dir($sourceFile)) {
            return false;
        }

        $shouldCopy = false;

        if (! file_exists($destFile)) {
            $shouldCopy = true;
        } elseif (file_exists($sourceFile) && is_file($sourceFile)) {
            $shouldCopy = filemtime($sourceFile) > filemtime($destFile);
        }

        if ($shouldCopy && file_exists($sourceFile) && is_file($sourceFile)) {
            create_directories(dirname($destFile));

            return copy($sourceFile, $destFile);
        }

        return file_exists($destFile);
    }
}

if (! function_exists('theme_asset')) {
    /**
     * Theme static URL (signatures aligned with InnoShop Factory: path first, optional theme code).
     * Copies from themes/{theme}/public/{path} into public/themes/{theme}/{path} when needed.
     *
     * @param  string  $path  e.g. css/app.css — leading slash ignored
     * @param  string  $theme  Theme folder code; defaults to active system_setting('theme'), then "default"
     */
    function theme_asset(string $path, string $theme = '', ?bool $secure = null): string
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');

        if ($theme === '') {
            $theme = (string) (system_setting('theme') ?: 'default');
        }

        $theme = strtolower($theme);

        $originThemePath = "$theme/public/$path";
        $destThemePath   = "themes/$theme/$path";

        $sourceFile = theme_path($originThemePath);
        $destFile   = public_path($destThemePath);

        should_copy_static_file($sourceFile, $destFile);

        $assetUrl  = app('url')->asset($destThemePath, $secure);
        $version   = file_exists($destFile) ? filemtime($destFile) : time();
        $separator = strpos($assetUrl, '?') !== false ? '&' : '?';

        return $assetUrl.$separator.'v='.$version;
    }
}

if (! function_exists('theme_image')) {
    /**
     * Theme image URL with resize caching (aligned with InnoShop Factory theme_image).
     *
     * @param  string  $path  Relative to themes/{theme}/public/
     */
    function theme_image(string $path, string $theme = '', int $width = 100, int $height = 100, ?string $mode = null): string
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');
        if ($path === '') {
            return (new ImageService(''))->resize($width, $height);
        }

        if ($theme === '') {
            $theme = (string) (system_setting('theme') ?: 'default');
        }

        $theme = strtolower($theme);

        $sourceFile    = theme_path("$theme/public/$path");
        $destThemePath = "themes/$theme/$path";
        $destFile      = public_path($destThemePath);

        $fileExists = should_copy_static_file($sourceFile, $destFile);

        if (! $fileExists) {
            return (new ImageService(''))->resize($width, $height);
        }

        return image_resize($destThemePath, $width, $height, $mode);
    }
}

if (! function_exists('innocms_version')) {
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

if (! function_exists('innocms_brand_link')) {
    /**
     * Get brand link html.
     *
     * @return string
     */
    function innocms_brand_link(): string
    {
        if (is_admin()) {
            $default = '<a href="https://www.innocms.com" class="ms-2" target="_blank">InnoCMS</a>';
        } else {
            $default = 'Powered By <a href="https://www.innocms.com" class="ms-2" target="_blank">InnoCMS</a>';
        }

        return fire_hook_filter('innocms.brand.link.display', $default);
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
