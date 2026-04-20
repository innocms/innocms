<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Illuminate\Support\Facades\Route;
use InnoCMS\Panel\ApiControllers;
use InnoCMS\RestAPI\PanelApiControllers\FileManagerController;

// Article
Route::get('/articles', [ApiControllers\ArticleController::class, 'index'])->name('articles.index');
Route::post('/articles', [ApiControllers\ArticleController::class, 'store'])->name('articles.store');
Route::put('/articles/{article}', [ApiControllers\ArticleController::class, 'update'])->name('articles.update');
Route::delete('/articles/{article}', [ApiControllers\ArticleController::class, 'destroy'])->name('articles.destroy');

// Catalog
Route::get('/catalogs', [ApiControllers\CatalogController::class, 'index'])->name('catalogs.index');
Route::get('/catalogs/autocomplete', [ApiControllers\CatalogController::class, 'autocomplete'])->name('catalogs.autocomplete');
Route::post('/catalogs', [ApiControllers\CatalogController::class, 'store'])->name('catalogs.store');
Route::put('/catalogs/{catalog}', [ApiControllers\CatalogController::class, 'update'])->name('catalogs.update');
Route::delete('/catalogs/{catalog}', [ApiControllers\CatalogController::class, 'destroy'])->name('catalogs.destroy');

// Page
Route::get('/pages', [ApiControllers\PageController::class, 'index'])->name('pages.index');
Route::post('/pages', [ApiControllers\PageController::class, 'store'])->name('pages.store');
Route::put('/pages/{page}', [ApiControllers\PageController::class, 'update'])->name('pages.update');
Route::delete('/pages/{page}', [ApiControllers\PageController::class, 'destroy'])->name('pages.destroy');

// Tag
Route::get('/tags', [ApiControllers\TagController::class, 'index'])->name('tags.index');
Route::get('/tags/autocomplete', [ApiControllers\TagController::class, 'autocomplete'])->name('tags.autocomplete');
Route::post('/tags', [ApiControllers\TagController::class, 'store'])->name('tags.store');
Route::put('/tags/{tag}', [ApiControllers\TagController::class, 'update'])->name('tags.update');
Route::delete('/tags/{tag}', [ApiControllers\TagController::class, 'destroy'])->name('tags.destroy');

// File Manager
Route::get('/file_manager/files', [FileManagerController::class, 'getFiles'])->name('file_manager.get_files');
Route::get('/file_manager/directories', [FileManagerController::class, 'getDirectories'])->name('file_manager.get_directories');
Route::post('/file_manager/directories', [FileManagerController::class, 'createDirectory'])->name('file_manager.create_directory');
Route::post('/file_manager/upload', [FileManagerController::class, 'uploadFiles'])->name('file_manager.upload');
Route::post('/file_manager/rename', [FileManagerController::class, 'rename'])->name('file_manager.rename');
Route::delete('/file_manager/files', [FileManagerController::class, 'destroyFiles'])->name('file_manager.delete_files');
Route::delete('/file_manager/directories', [FileManagerController::class, 'destroyDirectories'])->name('file_manager.delete_directories');
Route::post('/file_manager/move_directories', [FileManagerController::class, 'moveDirectories'])->name('file_manager.move_directories');
Route::post('/file_manager/move_files', [FileManagerController::class, 'moveFiles'])->name('file_manager.move_files');
Route::post('/file_manager/copy_files', [FileManagerController::class, 'copyFiles'])->name('file_manager.copy_files');
Route::post('/file_manager/download_remote', [FileManagerController::class, 'downloadRemoteFile'])->name('file_manager.download_remote');
Route::get('/file_manager/storage_config', [FileManagerController::class, 'getStorageConfig']);
Route::post('/file_manager/storage_config', [FileManagerController::class, 'saveStorageConfig']);
