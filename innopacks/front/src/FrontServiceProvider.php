<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\FileViewFinder;
use InnoCMS\Common\Middleware\ContentFilterHook;
use InnoCMS\Common\Middleware\EventActionHook;
use InnoCMS\Front\Middleware\GlobalDataMiddleware;

class FrontServiceProvider extends ServiceProvider
{
    /**
     * Boot front service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadTranslations();

        if (! installed()) {
            return;
        }

        load_settings();
        $this->registerWebRoutes();
        $this->registerApiRoutes();
        $this->publishViewTemplates();
        $this->loadThemeViewPath();
        $this->loadViewComponents();
    }

    /**
     * Register admin front routes.
     *
     * @return void
     */
    protected function registerWebRoutes(): void
    {
        $middlewares = ['web', EventActionHook::class, ContentFilterHook::class, GlobalDataMiddleware::class];
        Route::middleware($middlewares)
            ->name('front.')
            ->group(function () {
                $this->loadRoutesFrom(realpath(__DIR__.'/../routes/web.php'));
            });
    }

    /**
     * Register admin api routes.
     *
     * @return void
     */
    protected function registerApiRoutes(): void
    {
        $middlewares = ['api', EventActionHook::class, ContentFilterHook::class];
        Route::prefix('api')
            ->middleware($middlewares)
            ->name('api.')
            ->group(function () {
                $this->loadRoutesFrom(realpath(__DIR__.'/../routes/api.php'));
            });
    }

    /**
     * Register front language
     * @return void
     */
    protected function loadTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'front');
    }

    /**
     * Publish view as default theme.
     * php artisan vendor:publish --provider='InnoCMS\Front\FrontServiceProvider' --tag=views
     *
     * @return void
     */
    protected function publishViewTemplates(): void
    {
        $originViewPath = __DIR__.'/../resources';
        $customViewPath = base_path('themes/default');

        $this->publishes([
            $originViewPath => $customViewPath,
        ], 'views');
    }

    /**
     * Load theme view path.
     *
     * @return void
     */
    protected function loadThemeViewPath(): void
    {
        $this->app->singleton('view.finder', function ($app) {
            $themePaths = [];
            if ($theme = system_setting('theme')) {
                $themeViewPath = base_path("themes/{$theme}/views");
                if (is_dir($themeViewPath)) {
                    $themePaths[] = $themeViewPath;
                }
            }
            $themePaths[] = realpath(__DIR__.'/../resources/views');

            $viewPaths = $app['config']['view.paths'];
            $viewPaths = array_merge($themePaths, $viewPaths);

            return new FileViewFinder($app['files'], $viewPaths);
        });
    }

    /**
     * Load view components.
     *
     * @return void
     */
    protected function loadViewComponents(): void
    {
        $this->loadViewComponentsAs('front', [

        ]);
    }
}
