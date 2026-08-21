<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front;

use Exception;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\FileViewFinder;
use InnoCMS\Common\Middleware\ContentFilterHook;
use InnoCMS\Common\Middleware\EventActionHook;
use InnoCMS\Common\Middleware\VisitTrackingMiddleware;
use InnoCMS\Front\Middleware\GlobalDataMiddleware;
use InnoCMS\Front\Middleware\SetFrontLocale;
use InnoCMS\Front\Repositories\MenuRepo;

class FrontServiceProvider extends ServiceProvider
{
    /**
     * Boot front service provider.
     *
     * @return void
     * @throws Exception
     */
    public function boot(): void
    {
        if (! has_install_lock()) {
            return;
        }

        load_settings();
        $this->registerWebRoutes();
        $this->loadTranslations();
        $this->registerApiRoutes();
        $this->registerSitemapRoute();
        $this->shareGlobalViewData();
        $this->publishViewTemplates();
        $this->loadThemeViewPath();
        $this->loadViewComponents();
        $this->loadThemeTranslations();
        $this->loadThemeRoutes();
        $this->bootTheme();
    }

    /**
     * Share global view data.
     *
     * GlobalDataMiddleware only runs on matched front routes, so error pages
     * (404/500 rendered outside routing) get menus via this composer instead.
     *
     * @return void
     */
    protected function shareGlobalViewData(): void
    {
        View::composer('layouts.header', function (\Illuminate\View\View $view) {
            if (! $view->offsetExists('menus')) {
                $view->with('menus', MenuRepo::getInstance()->getMenus());
            }
        });
    }

    /**
     * Register sitemap.xml route without content-filter middleware.
     *
     * @return void
     */
    protected function registerSitemapRoute(): void
    {
        Route::get('/sitemap.xml', [Controllers\SitemapController::class, 'index'])->name('front.sitemap.index');
    }

    /**
     * Register admin front routes.
     *
     * @return void
     * @throws Exception
     */
    protected function registerWebRoutes(): void
    {
        $router      = $this->app['router'];
        $middlewares = [
            SetFrontLocale::class,
            EventActionHook::class,
            ContentFilterHook::class,
            GlobalDataMiddleware::class,
            VisitTrackingMiddleware::class,
        ];

        foreach ($middlewares as $middleware) {
            $router->pushMiddlewareToGroup('front', $middleware);
        }

        Route::middleware('front')
            ->name('front.')
            ->group(function () {
                $path = __DIR__.'/../routes/root.php';
                if (is_file($path)) {
                    $this->loadRoutesFrom($path);
                }
            });

        $locales   = locales();
        $webRoutes = __DIR__.'/../routes/web.php';
        if (hide_url_locale() || $locales->isEmpty()) {
            Route::middleware('front')
                ->name('front.')
                ->group(function () use ($webRoutes) {
                    if (is_file($webRoutes)) {
                        $this->loadRoutesFrom($webRoutes);
                    }
                });
        } else {
            foreach ($locales as $locale) {
                Route::middleware('front')
                    ->prefix($locale->code)
                    ->name($locale->code.'.front.')
                    ->group(function () use ($webRoutes) {
                        if (is_file($webRoutes)) {
                            $this->loadRoutesFrom($webRoutes);
                        }
                    });
            }
        }
    }

    /**
     * Register front api routes (CMS specific).
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
        if (! is_dir(__DIR__.'/../lang')) {
            return;
        }

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'front');
        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/front'),
        ], 'lang');
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
        // `view.finder` is a container `bind`, not a singleton: each `make('view.finder')` is a new
        // instance. The View factory keeps the one it got when `view` was first resolved — mutating
        // a separately resolved finder does nothing. Always prepend on the factory's finder.
        $finder = $this->app->make('view')->getFinder();
        if (! $finder instanceof FileViewFinder) {
            return;
        }

        // Prepend search paths in place (do not replace finder or forget `view`) so package engines
        // and namespaces stay intact.
        $packViews = realpath(__DIR__.'/../resources/views') ?: (__DIR__.'/../resources/views');
        if (is_dir($packViews)) {
            $finder->prependLocation($packViews);
        }

        if ($theme = system_setting('theme')) {
            $themeViewPath = base_path("themes/{$theme}/views");
            if (is_dir($themeViewPath)) {
                $finder->prependLocation($themeViewPath);
            }
        }
    }

    /**
     * Load view components.
     *
     * @return void
     */
    protected function loadViewComponents(): void
    {
        $this->loadViewComponentsAs('front', [
            'breadcrumb' => Components\Breadcrumb::class,
        ]);
    }

    /**
     * Load theme languages.
     *
     * @return void
     */
    protected function loadThemeTranslations(): void
    {
        $currentTheme = system_setting('theme');
        if (! $currentTheme) {
            return;
        }

        $themeLangPath = base_path("themes/{$currentTheme}/lang");
        if (! is_dir($themeLangPath)) {
            return;
        }

        $this->loadTranslationsFrom($themeLangPath, "theme-{$currentTheme}");
    }

    /**
     * Load theme routes (Routes/front.php with locale handling, Routes/root.php without).
     *
     * @return void
     */
    protected function loadThemeRoutes(): void
    {
        $currentTheme = system_setting('theme');
        if (! $currentTheme) {
            return;
        }

        $themeBasePath = base_path("themes/{$currentTheme}");

        // Root routes (no locale prefix)
        $rootRoutePath = "$themeBasePath/routes/root.php";
        if (file_exists($rootRoutePath)) {
            Route::middleware('front')
                ->name('front.')
                ->group(function () use ($rootRoutePath) {
                    $this->loadRoutesFrom($rootRoutePath);
                });
        }

        // Front routes (with locale prefix handling)
        $frontRoutePath = "$themeBasePath/routes/front.php";
        if (file_exists($frontRoutePath)) {
            $locales = locales();
            if (hide_url_locale() || $locales->isEmpty()) {
                Route::middleware('front')
                    ->name('front.')
                    ->group(function () use ($frontRoutePath) {
                        $this->loadRoutesFrom($frontRoutePath);
                    });
            } else {
                foreach ($locales as $locale) {
                    Route::middleware('front')
                        ->prefix($locale->code)
                        ->name($locale->code.'.front.')
                        ->group(function () use ($frontRoutePath) {
                            $this->loadRoutesFrom($frontRoutePath);
                        });
                }
            }
        }
    }

    /**
     * Load theme boot file (setup/boot.php) for runtime hook registration.
     * Follows the same require → callable → call pattern as demo seeder.
     */
    protected function bootTheme(): void
    {
        $currentTheme = system_setting('theme');
        if (! $currentTheme) {
            return;
        }

        $bootFile = base_path("themes/{$currentTheme}/setup/boot.php");
        if (! is_file($bootFile)) {
            return;
        }

        $boot = require $bootFile;
        if (is_callable($boot)) {
            $boot();
        }
    }
}
