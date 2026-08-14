<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Illuminate\Support\Facades\Route;
use InnoCMS\Restapi\Middleware\EnsureUserIsAdmin;
use InnoCMS\Restapi\PanelApiControllers;

Route::get('/', [PanelApiControllers\IntroductionController::class, 'index'])->name('base.index');
Route::post('/login', [PanelApiControllers\AuthController::class, 'login'])->name('auth.login');

$middlewares = ['auth:sanctum', EnsureUserIsAdmin::class];
Route::middleware($middlewares)->group(function () {

    Route::get('/admin', [PanelApiControllers\AuthController::class, 'admin'])->name('auth.admin');

    // Locales
    Route::get('/locales', [PanelApiControllers\LocaleController::class, 'index'])->name('locales.index');
    Route::get('/locales/{locale}', [PanelApiControllers\LocaleController::class, 'show'])->name('locales.show');

    Route::get('/dashboard', [PanelApiControllers\DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/{date}', [PanelApiControllers\DashboardController::class, 'daily'])->name('dashboard.daily');

    Route::get('/articles', [PanelApiControllers\ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/names', [PanelApiControllers\ArticleController::class, 'names'])->name('articles.names');
    Route::get('/articles/autocomplete', [PanelApiControllers\ArticleController::class, 'autocomplete'])->name('articles.autocomplete');
    Route::post('/articles', [PanelApiControllers\ArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}', [PanelApiControllers\ArticleController::class, 'show'])->name('articles.show');
    Route::put('/articles/{article}', [PanelApiControllers\ArticleController::class, 'update'])->name('articles.update');
    Route::patch('/articles/{article}', [PanelApiControllers\ArticleController::class, 'patch'])->name('articles.patch');
    Route::delete('/articles/{article}', [PanelApiControllers\ArticleController::class, 'destroy'])->name('articles.destroy');

    Route::get('/catalogs', [PanelApiControllers\CatalogController::class, 'index'])->name('catalogs.index');
    Route::get('/catalogs/names', [PanelApiControllers\CatalogController::class, 'names'])->name('catalogs.names');
    Route::get('/catalogs/autocomplete', [PanelApiControllers\CatalogController::class, 'autocomplete'])->name('catalogs.autocomplete');
    Route::post('/catalogs', [PanelApiControllers\CatalogController::class, 'store'])->name('catalogs.store');
    Route::get('/catalogs/{catalog}', [PanelApiControllers\CatalogController::class, 'show'])->name('catalogs.show');
    Route::put('/catalogs/{catalog}', [PanelApiControllers\CatalogController::class, 'update'])->name('catalogs.update');
    Route::patch('/catalogs/{catalog}', [PanelApiControllers\CatalogController::class, 'patch'])->name('catalogs.patch');
    Route::delete('/catalogs/{catalog}', [PanelApiControllers\CatalogController::class, 'destroy'])->name('catalogs.destroy');

    Route::get('/pages', [PanelApiControllers\PageController::class, 'index'])->name('pages.index');
    Route::get('/pages/names', [PanelApiControllers\PageController::class, 'names'])->name('pages.names');
    Route::get('/pages/autocomplete', [PanelApiControllers\PageController::class, 'autocomplete'])->name('pages.autocomplete');
    Route::post('/pages', [PanelApiControllers\PageController::class, 'store'])->name('pages.store');
    Route::get('/pages/{page}', [PanelApiControllers\PageController::class, 'show'])->name('pages.show');
    Route::put('/pages/{page}', [PanelApiControllers\PageController::class, 'update'])->name('pages.update');
    Route::patch('/pages/{page}', [PanelApiControllers\PageController::class, 'patch'])->name('pages.patch');
    Route::delete('/pages/{page}', [PanelApiControllers\PageController::class, 'destroy'])->name('pages.destroy');

    Route::get('/tags', [PanelApiControllers\TagController::class, 'index'])->name('tags.index');
    Route::get('/tags/names', [PanelApiControllers\TagController::class, 'names'])->name('tags.name');
    Route::get('/tags/autocomplete', [PanelApiControllers\TagController::class, 'autocomplete'])->name('tags.autocomplete');
    Route::post('/tags', [PanelApiControllers\TagController::class, 'store'])->name('tags.store');
    Route::get('/tags/{tag}', [PanelApiControllers\TagController::class, 'show'])->name('tags.show');
    Route::put('/tags/{tag}', [PanelApiControllers\TagController::class, 'update'])->name('tags.update');
    Route::patch('/tags/{tag}', [PanelApiControllers\TagController::class, 'patch'])->name('tags.patch');
    Route::delete('/tags/{tag}', [PanelApiControllers\TagController::class, 'destroy'])->name('tags.destroy');

    // Products
    Route::get('/products', [PanelApiControllers\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/autocomplete', [PanelApiControllers\ProductController::class, 'autocomplete'])->name('products.autocomplete');
    Route::post('/products', [PanelApiControllers\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [PanelApiControllers\ProductController::class, 'show'])->name('products.show');
    Route::put('/products/{product}', [PanelApiControllers\ProductController::class, 'update'])->name('products.update');
    Route::patch('/products/{product}', [PanelApiControllers\ProductController::class, 'patch'])->name('products.patch');
    Route::delete('/products/{product}', [PanelApiControllers\ProductController::class, 'destroy'])->name('products.destroy');

    // Product categories
    Route::get('/categories', [PanelApiControllers\CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/autocomplete', [PanelApiControllers\CategoryController::class, 'autocomplete'])->name('categories.autocomplete');
    Route::post('/categories', [PanelApiControllers\CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}', [PanelApiControllers\CategoryController::class, 'show'])->name('categories.show');
    Route::put('/categories/{category}', [PanelApiControllers\CategoryController::class, 'update'])->name('categories.update');
    Route::patch('/categories/{category}', [PanelApiControllers\CategoryController::class, 'patch'])->name('categories.patch');
    Route::delete('/categories/{category}', [PanelApiControllers\CategoryController::class, 'destroy'])->name('categories.destroy');

    // Contacts
    Route::get('/contacts', [PanelApiControllers\ContactController::class, 'index'])->name('contacts.index');
    Route::put('/contacts/read-all', [PanelApiControllers\ContactController::class, 'markAllRead'])->name('contacts.mark_all_read');
    Route::put('/contacts/{contact}/read', [PanelApiControllers\ContactController::class, 'markRead'])->name('contacts.mark_read');
    Route::delete('/contacts/{contact}', [PanelApiControllers\ContactController::class, 'destroy'])->name('contacts.destroy');

    // Media Library
    Route::get('/media/files', [PanelApiControllers\FileManagerController::class, 'getFiles'])->name('media.files');
    Route::get('/media/directories', [PanelApiControllers\FileManagerController::class, 'getDirectories'])->name('media.directories');
    Route::post('/media/directories', [PanelApiControllers\FileManagerController::class, 'createDirectory'])->name('media.create_directory');
    Route::post('/media/upload', [PanelApiControllers\FileManagerController::class, 'uploadFiles'])->name('media.upload');
    Route::post('/media/rename', [PanelApiControllers\FileManagerController::class, 'rename'])->name('media.rename');
    Route::delete('/media/files', [PanelApiControllers\FileManagerController::class, 'destroyFiles'])->name('media.delete_files');
    Route::delete('/media/directories', [PanelApiControllers\FileManagerController::class, 'destroyDirectories'])->name('media.delete_directories');
    Route::post('/media/move_directories', [PanelApiControllers\FileManagerController::class, 'moveDirectories'])->name('media.move_directories');
    Route::post('/media/move_files', [PanelApiControllers\FileManagerController::class, 'moveFiles'])->name('media.move_files');
    Route::post('/media/copy_files', [PanelApiControllers\FileManagerController::class, 'copyFiles'])->name('media.copy_files');
    Route::post('/media/download_remote', [PanelApiControllers\FileManagerController::class, 'downloadRemoteFile'])->name('media.download_remote');
    Route::get('/media/storage_config', [PanelApiControllers\FileManagerController::class, 'getStorageConfig'])->name('media.storage_config');
    Route::post('/media/storage_config', [PanelApiControllers\FileManagerController::class, 'saveStorageConfig'])->name('media.save_storage_config');
    Route::get('/media/media/{id}', [PanelApiControllers\FileManagerController::class, 'getMediaDetail'])->name('media.media_detail');
    Route::patch('/media/media/{id}', [PanelApiControllers\FileManagerController::class, 'updateMedia'])->name('media.media_update');
    Route::get('/media/stats', [PanelApiControllers\FileManagerController::class, 'getMediaStats'])->name('media.stats');
});
