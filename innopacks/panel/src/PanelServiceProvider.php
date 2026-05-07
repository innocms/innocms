<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use InnoCMS\Common\Middleware\ContentFilterHook;
use InnoCMS\Common\Middleware\EventActionHook;
use InnoCMS\Common\Models\Admin;
use InnoCMS\Common\Services\StorageService;
use InnoCMS\Panel\Console\Commands\ChangeRootPassword;
use InnoCMS\Panel\Middleware\AdminAuthenticate;
use InnoCMS\Panel\Middleware\GlobalPanelData;
use InnoCMS\Panel\Middleware\SetPanelLocale;
use InnoCMS\Panel\Services\ThemeService;
use InnoCMS\RestAPI\Services\FileManagerInterface;
use InnoCMS\RestAPI\Services\FileManagerService;

class PanelServiceProvider extends ServiceProvider
{
    /**
     * Boot panel service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        load_settings();
        $this->registerGuard();
        $this->registerUploadFileSystem();
        $this->registerCommands();
        $this->registerWebRoutes();
        $this->registerFileManagerService();
        $this->loadTranslations();
        $this->loadViewTemplates();
        $this->loadViewComponents();
    }

    /**
     * @return void
     */
    public function register(): void
    {
        app('router')->aliasMiddleware('admin_auth', AdminAuthenticate::class);
        $this->app->singleton(ThemeService::class);
    }

    /**
     * Register admin user guard.
     */
    private function registerGuard(): void
    {
        Config::set('auth.providers.admins', [
            'driver' => 'eloquent',
            'model'  => Admin::class,
        ]);

        Config::set('auth.guards.admin', [
            'driver'   => 'session',
            'provider' => 'admins',
        ]);
    }

    /**
     * @return void
     */
    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ChangeRootPassword::class,
            ]);
        }
    }

    /**
     * Register the media filesystem disk.
     *
     * @return void
     */
    protected function registerUploadFileSystem(): void
    {
        $driver    = system_setting('file_manager_driver', 'local');
        $s3Drivers = ['oss', 'cos', 'qiniu', 's3', 'obs', 'r2', 'minio'];

        if (in_array($driver, $s3Drivers)) {
            $prefix   = "storage_{$driver}_";
            $s3Config = [
                'driver'                  => 's3',
                'key'                     => system_setting($prefix.'key', system_setting('storage_key', '')),
                'secret'                  => system_setting($prefix.'secret', system_setting('storage_secret', '')),
                'region'                  => system_setting($prefix.'region', system_setting('storage_region', '')),
                'bucket'                  => system_setting($prefix.'bucket', system_setting('storage_bucket', '')),
                'endpoint'                => system_setting($prefix.'endpoint', system_setting('storage_endpoint', '')),
                'url'                     => system_setting($prefix.'cdn_domain', system_setting('storage_cdn_domain', '')) ?: null,
                'use_path_style_endpoint' => false,
                'visibility'              => 'public',
                'options'                 => ['ACL' => 'public-read'],
                'throw'                   => true,
            ];
            Config::set('filesystems.disks.media', $s3Config);
        } else {
            Config::set('filesystems.disks.media', [
                'driver'      => 'local',
                'root'        => public_path(rtrim(StorageService::STORAGE_PREFIX, '/')),
                'url'         => env('APP_URL').'/'.ltrim(StorageService::STORAGE_PREFIX, '/'),
                'visibility'  => 'public',
                'throw'       => true,
                'permissions' => [
                    'file' => [
                        'public'  => 0755,
                        'private' => 0755,
                    ],
                    'dir' => [
                        'public'  => 0755,
                        'private' => 0755,
                    ],
                ],
            ]);
        }
    }

    /**
     * Register admin panel routes.
     *
     * @return void
     */
    private function registerWebRoutes(): void
    {
        $middlewares = ['web', EventActionHook::class, ContentFilterHook::class, GlobalPanelData::class, SetPanelLocale::class];
        $adminName   = panel_name();
        Route::prefix($adminName)
            ->middleware($middlewares)
            ->name("$adminName.")
            ->group(function () {
                $this->loadRoutesFrom(realpath(__DIR__.'/../routes/web.php'));
            });
    }

    /**
     * Bind FileManagerInterface to the appropriate implementation.
     */
    protected function registerFileManagerService(): void
    {
        $this->app->singleton(FileManagerInterface::class, function () {
            return new FileManagerService;
        });
    }

    /**
     * Register panel language
     * @return void
     */
    private function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'panel');
    }

    /**
     * Load view components.
     *
     * @return void
     */
    private function loadViewComponents(): void
    {
        $this->loadViewComponentsAs('panel', [
            'form-input'             => Components\Forms\Input::class,
            'sidebar'                => Components\Sidebar::class,
            'alert'                  => Components\Alert::class,
            'form-image'             => Components\Forms\Image::class,
            'form-select'            => Components\Forms\Select::class,
            'form-rich-text'         => Components\Forms\RichText::class,
            'form-lang-tab'          => Components\Forms\LangTab::class,
            'form-textarea'          => Components\Forms\Textarea::class,
            'form-codemirror'        => Components\Forms\Codemirror::class,
            'form-autocomplete-list' => Components\Forms\AutocompleteList::class,
            'form-switch-radio'      => Components\Forms\SwitchRadio::class,
            'no-data'                => Components\NoData::class,
            'form-date'              => Components\Forms\Date::class,
            'data-search'            => Components\Data\DataSearch::class,
        ]);
    }

    /**
     * Load templates
     *
     * @return void
     */
    private function loadViewTemplates(): void
    {
        $originViewPath = inno_path('panel/resources/views');
        $customViewPath = resource_path('views/vendor/innocms-panel');

        $this->publishes([
            $originViewPath => $customViewPath,
        ], 'views');

        $this->loadViewsFrom($customViewPath, 'panel');
        $this->loadViewsFrom($originViewPath, 'panel');
    }
}
