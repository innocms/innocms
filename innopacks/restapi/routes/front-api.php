<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Illuminate\Support\Facades\Route;
use InnoCMS\RestAPI\FrontApiControllers;

Route::get('/', [FrontApiControllers\HomeController::class, 'base'])->name('home.base');
Route::get('/home', [FrontApiControllers\HomeController::class, 'index'])->name('home.index');
Route::get('/settings', [FrontApiControllers\SettingController::class, 'index'])->name('settings.index');

Route::get('/articles', [FrontApiControllers\ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article}', [FrontApiControllers\ArticleController::class, 'show'])->name('articles.show');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/upload/images', [FrontApiControllers\UploadController::class, 'images'])->name('upload.images');
    Route::post('/upload/files', [FrontApiControllers\UploadController::class, 'files'])->name('upload.files');
});
