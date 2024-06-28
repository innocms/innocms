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

Route::middleware(['admin_auth:admin'])
    ->group(function () {
        Route::get('logout', [Controllers\LogoutController::class, 'index'])->name('logout.index');

        Route::get('/', [Controllers\DashboardController::class, 'index'])->name('dashboard.index');
        Route::resource('/articles', Controllers\ArticleController::class);
        Route::resource('/catalogs', Controllers\CatalogController::class);
        Route::resource('/pages', Controllers\PageController::class);
        Route::resource('/tags', Controllers\TagController::class);

        Route::get('/locales', [Controllers\LocaleController::class, 'index'])->name('locales.index');
        Route::post('/locales/install', [Controllers\LocaleController::class, 'install'])->name('locales.install');
        Route::get('/locales/{locale}/edit', [Controllers\LocaleController::class, 'edit'])->name('locales.edit');
        Route::put('/locales/{locale}', [Controllers\LocaleController::class, 'update'])->name('locales.update');
        Route::post('/locales/{code}/uninstall', [Controllers\LocaleController::class, 'uninstall'])->name('locales.uninstall');

        Route::get('/themes', [Controllers\ThemeController::class, 'index'])->name('themes.index');
        Route::put('/themes/{country}/active', [Controllers\ThemeController::class, 'enable'])->name('themes.active');
        Route::get('/themes/settings', [Controllers\ThemeController::class, 'settings'])->name('themes_settings.index');
        Route::put('/themes/settings', [Controllers\ThemeController::class, 'updateSettings'])->name('themes_settings.update');

        Route::get('/account', [Controllers\AccountController::class, 'index'])->name('account.index');
        Route::put('/account', [Controllers\AccountController::class, 'update'])->name('account.update');

        Route::get('/settings', [Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [Controllers\SettingController::class, 'update'])->name('settings.update');

        Route::resource('/admins', Controllers\AdminController::class);
        Route::put('/admins/{currency}/active', [Controllers\AdminController::class, 'active'])->name('admins.active');

        Route::resource('/roles', Controllers\RoleController::class);
    });
