<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Illuminate\Support\Facades\Route;
use InnoCMS\Panel\Controllers;

Route::get('login', [Controllers\LoginController::class, 'index'])->name('login.index');
Route::post('login', [Controllers\LoginController::class, 'store'])->name('login.store');

Route::get('cli-login', [Controllers\CliLoginController::class, 'index'])->name('cli-login.index');
Route::post('cli-login', [Controllers\CliLoginController::class, 'store'])->name('cli-login.store');

Route::middleware(['admin_auth:admin'])
    ->group(function () {
        Route::get('logout', [Controllers\LogoutController::class, 'index'])->name('logout.index');

        Route::get('/', [Controllers\DashboardController::class, 'index'])->name('dashboard.index');

        Route::get('/analytics', [Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
        Route::post('/analytics/reaggregate', [Controllers\AnalyticsController::class, 'reaggregate'])->name('analytics.reaggregate');

        Route::get('/visits', [Controllers\VisitController::class, 'index'])->name('visits.index');
        Route::get('/visits/{visit}', [Controllers\VisitController::class, 'show'])->name('visits.show');
        Route::post('/visits/batch-locate', [Controllers\VisitController::class, 'batchLocate'])->name('visits.batch_locate');
        Route::post('/visits/{visit}/locate', [Controllers\VisitController::class, 'locate'])->name('visits.locate');
        Route::post('/visits/{visit}/parse-ua', [Controllers\VisitController::class, 'parseUA'])->name('visits.parse_ua');

        Route::get('/locale/{code}', [Controllers\LocaleController::class, 'switch'])->name('locale.switch');

        Route::resource('/articles', Controllers\ArticleController::class);
        Route::put('/articles/{article}/active', [Controllers\ArticleController::class, 'active'])->name('articles.active');
        Route::resource('/catalogs', Controllers\CatalogController::class);
        Route::put('/catalogs/{catalog}/active', [Controllers\CatalogController::class, 'active'])->name('catalogs.active');
        Route::resource('/pages', Controllers\PageController::class);
        Route::put('/pages/{page}/active', [Controllers\PageController::class, 'active'])->name('pages.active');
        Route::resource('/tags', Controllers\TagController::class);
        Route::put('/tags/{tag}/active', [Controllers\TagController::class, 'active'])->name('tags.active');

        Route::post('/products/copy/{product}', [Controllers\ProductController::class, 'copy'])->name('products.copy');
        Route::resource('/products', Controllers\ProductController::class);
        Route::put('/products/{product}/active', [Controllers\ProductController::class, 'active'])->name('products.active');

        Route::resource('/categories', Controllers\CategoryController::class);
        Route::put('/categories/{category}/active', [Controllers\CategoryController::class, 'active'])->name('categories.active');

        Route::get('/contacts', [Controllers\ContactController::class, 'index'])->name('contacts.index');
        Route::get('/contacts/{contact}', [Controllers\ContactController::class, 'show'])->name('contacts.show');
        Route::delete('/contacts/{contact}', [Controllers\ContactController::class, 'destroy'])->name('contacts.destroy');

        Route::get('/file_manager', [Controllers\FileManagerController::class, 'index'])->name('file_manager.index');
        Route::get('/file_manager/iframe', [Controllers\FileManagerController::class, 'iframe'])->name('file_manager.iframe');

        Route::get('/locales', [Controllers\LocaleController::class, 'index'])->name('locales.index');
        Route::post('/locales/install', [Controllers\LocaleController::class, 'install'])->name('locales.install');
        Route::get('/locales/{locale}/edit', [Controllers\LocaleController::class, 'edit'])->name('locales.edit');
        Route::put('/locales/{locale}', [Controllers\LocaleController::class, 'update'])->name('locales.update');
        Route::post('/locales/{code}/uninstall', [Controllers\LocaleController::class, 'uninstall'])->name('locales.uninstall');

        Route::get('/themes', [Controllers\ThemeController::class, 'index'])->name('themes.index');
        Route::post('/themes/{code}/import-demo', [Controllers\ThemeController::class, 'importDemo'])->name('themes.import_demo');
        Route::put('/themes/{code}/active', [Controllers\ThemeController::class, 'enable'])->name('themes.active');
        Route::get('/themes/settings', [Controllers\ThemeController::class, 'settings'])->name('themes_settings.index');
        Route::put('/themes/settings', [Controllers\ThemeController::class, 'updateSettings'])->name('themes_settings.update');

        Route::get('/account', [Controllers\AccountController::class, 'index'])->name('account.index');
        Route::put('/account', [Controllers\AccountController::class, 'update'])->name('account.update');

        Route::get('/settings', [Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [Controllers\SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/download-geolite2', [Controllers\SettingController::class, 'downloadGeoLite2'])->name('settings.download_geolite2');
        Route::get('/settings/geolite2-info', [Controllers\SettingController::class, 'getGeoLite2Info'])->name('settings.geolite2_info');

        Route::post('/content_ai/generate', [Controllers\ContentAIController::class, 'generate'])->name('content_ai.generate');
        Route::get('/content_ai/models', [Controllers\ContentAIController::class, 'getModels'])->name('content_ai.models');
        Route::post('/content_ai/test-model', [Controllers\ContentAIController::class, 'testModel'])->name('content_ai.test_model');

        Route::resource('/admins', Controllers\AdminController::class);
        Route::put('/admins/{admin}/active', [Controllers\AdminController::class, 'active'])->name('admins.active');

        Route::resource('/roles', Controllers\RoleController::class);
    });
