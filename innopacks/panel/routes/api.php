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

// AI Content Generation
Route::post('/ai/generate', [ApiControllers\ContentAIController::class, 'generate'])->name('ai.generate');
Route::get('/ai/models', [ApiControllers\ContentAIController::class, 'getModels'])->name('ai.models');
Route::post('/ai/test', [ApiControllers\ContentAIController::class, 'testModel'])->name('ai.test');

// File Manager
Route::get('/file-manager', [ApiControllers\FileManagerController::class, 'index'])->name('file_manager.index');
Route::get('/file-manager/directories', [ApiControllers\FileManagerController::class, 'directories'])->name('file_manager.directories');
Route::post('/file-manager/directory', [ApiControllers\FileManagerController::class, 'createDirectory'])->name('file_manager.create_directory');
Route::post('/file-manager/upload', [ApiControllers\FileManagerController::class, 'upload'])->name('file_manager.upload');
Route::post('/file-manager/move', [ApiControllers\FileManagerController::class, 'move'])->name('file_manager.move');
Route::post('/file-manager/copy', [ApiControllers\FileManagerController::class, 'copy'])->name('file_manager.copy');
Route::post('/file-manager/rename', [ApiControllers\FileManagerController::class, 'rename'])->name('file_manager.rename');
Route::delete('/file-manager', [ApiControllers\FileManagerController::class, 'destroy'])->name('file_manager.destroy');
