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
        load_settings();
        $this->registerWebRoutes();
        $this->registerApiRoutes();
        $this->loadTranslations();
        $this->loadViewTemplates();
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
     * Load view components.
     *
     * @return void
     */
    protected function loadViewComponents(): void
    {
        $this->loadViewComponentsAs('front', [

        ]);
    }

    /**
     * Load templates
     *
     * @return void
     */
    private function loadViewTemplates(): void
    {
        $originViewPath = inno_path('front/resources/views');
        $customViewPath = resource_path('views/vendor/innocms-front');

        $this->publishes([
            $originViewPath => $customViewPath,
        ], 'views');

        $this->loadViewsFrom($customViewPath, 'front');
        $this->loadViewsFrom($originViewPath, 'front');
    }
}
