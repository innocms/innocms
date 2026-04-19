<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Illuminate\Support\Facades\Route;
use InnoCMS\Front\Controllers;

Route::get('/', [Controllers\HomeController::class, 'index'])->name('home.index');

// Upload
Route::post('/upload/images', [Controllers\UploadController::class, 'images'])->name('upload.images');
Route::post('/upload/files', [Controllers\UploadController::class, 'files'])->name('upload.files');
