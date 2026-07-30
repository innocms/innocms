<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Aicore;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use InnoCMS\Aicore\Images\ImageDriverManager;
use InnoCMS\Aicore\Services\ProviderRegistry;
use InnoCMS\Aicore\Services\ToolRegistry;
use InnoCMS\Aicore\Tools\ArticleDetailTool;
use InnoCMS\Aicore\Tools\ArticleListTool;
use InnoCMS\Aicore\Tools\CatalogListTool;
use InnoCMS\Aicore\Tools\LocaleListTool;
use InnoCMS\Aicore\Tools\PageListTool;
use InnoCMS\Aicore\Tools\TagListTool;

class AicoreServiceProvider extends ServiceProvider
{
    /**
     * config path.
     */
    private string $basePath = __DIR__.'/../';

    /**
     * Register AI services.
     */
    public function register(): void
    {
        $this->loadViewsFrom($this->basePath.'resources/views', 'aicore');
        $this->loadTranslationsFrom($this->basePath.'lang', 'aicore');
        $this->app->singleton(ProviderRegistry::class);
        $this->app->singleton(ToolRegistry::class);
    }

    /**
     * Boot AI service provider.
     */
    public function boot(): void
    {
        if (! has_install_lock()) {
            return;
        }

        $this->loadAiConfig();
        $this->registerCoreTools();
        $this->registerDefaultImageDriver();
        $this->registerPanelRoutes();
        $this->registerPanelApiRoutes();
        $this->loadViewTemplates();
    }

    /**
     * Register the default image driver manager as a low-priority fallback on
     * the `ai.image_generate_driver` hook. The manager dispatches to the
     * correct vendor-specific driver (OpenAI-compatible, MiniMax, ...) based
     * on the active provider. Plugins providing a custom driver should
     * register with a higher priority to bypass this entirely.
     */
    private function registerDefaultImageDriver(): void
    {
        app('eventy')->addFilter('ai.image_generate_driver', function ($driver) {
            return $driver ?: ImageDriverManager::class;
        }, -100, 1);
    }

    /**
     * Register built-in read-only tools into the ToolRegistry.
     * Plugin tools join later via the `ai.tools` hook on first registry read.
     */
    private function registerCoreTools(): void
    {
        $registry = $this->app->make(ToolRegistry::class);

        foreach ([
            ArticleListTool::class,
            ArticleDetailTool::class,
            CatalogListTool::class,
            PageListTool::class,
            TagListTool::class,
            LocaleListTool::class,
        ] as $toolClass) {
            $registry->register($this->app->make($toolClass));
        }
    }

    /**
     * Load AI config from system_setting into config('ai.*') for laravel/ai SDK.
     */
    private function loadAiConfig(): void
    {
        if (! installed()) {
            return;
        }

        app(ProviderRegistry::class)->buildLaravelAiConfig();
    }

    /**
     * Register admin panel AI routes.
     */
    private function registerPanelRoutes(): void
    {
        $adminName = panel_name();

        Route::prefix($adminName)
            ->middleware('panel')
            ->name('panel.')
            ->group(function () {
                $path = $this->basePath.'routes/panel.php';
                if (is_file($path)) {
                    $this->loadRoutesFrom($path);
                }
            });
    }

    /**
     * Register panel API AI routes.
     */
    private function registerPanelApiRoutes(): void
    {
        Route::prefix('api/panel')
            ->middleware('panel_api')
            ->name('api.panel.')
            ->group(function () {
                $path = $this->basePath.'routes/panel-api.php';
                if (is_file($path)) {
                    $this->loadRoutesFrom($path);
                }
            });
    }

    /**
     * Load templates for publishing.
     */
    private function loadViewTemplates(): void
    {
        $originViewPath = inno_path('aicore/resources/views');
        $customViewPath = resource_path('views/vendor/aicore');

        $this->publishes([
            $originViewPath => $customViewPath,
        ], 'views');
    }
}
