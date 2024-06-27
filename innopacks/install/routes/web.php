<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Illuminate\Support\Facades\Route;
use InnoCMS\Install\Controllers;

Route::get('/install', [Controllers\InstallController::class, 'index'])->name('install.index');
Route::post('/install/connected', [Controllers\InstallController::class, 'checkConnected'])->name('install.connected');
Route::post('/install/complete', [Controllers\InstallController::class, 'complete'])->name('install.complete');
