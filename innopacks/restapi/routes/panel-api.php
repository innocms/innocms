<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Illuminate\Support\Facades\Route;
use InnoCMS\RestAPI\Middleware\EnsureUserIsAdmin;
use InnoCMS\RestAPI\PanelApiControllers;

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

    // Contacts
    Route::get('/contacts', [PanelApiControllers\ContactController::class, 'index'])->name('contacts.index');
    Route::put('/contacts/read-all', [PanelApiControllers\ContactController::class, 'markAllRead'])->name('contacts.mark_all_read');
    Route::put('/contacts/{contact}/read', [PanelApiControllers\ContactController::class, 'markRead'])->name('contacts.mark_read');
    Route::delete('/contacts/{contact}', [PanelApiControllers\ContactController::class, 'destroy'])->name('contacts.destroy');

    // File Manager
    Route::get('/file_manager/files', [PanelApiControllers\FileManagerController::class, 'getFiles'])->name('file_manager.get_files');
    Route::get('/file_manager/directories', [PanelApiControllers\FileManagerController::class, 'getDirectories'])->name('file_manager.get_directories');
    Route::post('/file_manager/directories', [PanelApiControllers\FileManagerController::class, 'createDirectory'])->name('file_manager.create_directory');
    Route::post('/file_manager/upload', [PanelApiControllers\FileManagerController::class, 'uploadFiles'])->name('file_manager.upload');
    Route::post('/file_manager/rename', [PanelApiControllers\FileManagerController::class, 'rename'])->name('file_manager.rename');
    Route::delete('/file_manager/files', [PanelApiControllers\FileManagerController::class, 'destroyFiles'])->name('file_manager.delete_files');
    Route::delete('/file_manager/directories', [PanelApiControllers\FileManagerController::class, 'destroyDirectories'])->name('file_manager.delete_directories');
    Route::post('/file_manager/move_directories', [PanelApiControllers\FileManagerController::class, 'moveDirectories'])->name('file_manager.move_directories');
    Route::post('/file_manager/move_files', [PanelApiControllers\FileManagerController::class, 'moveFiles'])->name('file_manager.move_files');
    Route::post('/file_manager/copy_files', [PanelApiControllers\FileManagerController::class, 'copyFiles'])->name('file_manager.copy_files');
    Route::post('/file_manager/download_remote', [PanelApiControllers\FileManagerController::class, 'downloadRemoteFile'])->name('file_manager.download_remote');
    Route::get('/file_manager/storage_config', [PanelApiControllers\FileManagerController::class, 'getStorageConfig'])->name('file_manager.storage_config');
    Route::post('/file_manager/storage_config', [PanelApiControllers\FileManagerController::class, 'saveStorageConfig'])->name('file_manager.save_storage_config');
});
