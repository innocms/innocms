<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Illuminate\Support\Facades\Route;
use InnoCMS\Plugin\Controllers\PluginController;

// There is no page for creating a plugin and no separate detail page: the form page of a
// plugin is its configuration. The resource routes for both would point at methods that
// do not exist.
Route::resource('/plugins', PluginController::class)->except(['create', 'show']);
Route::post('/plugins/enabled', [PluginController::class, 'updateStatus'])->name('plugins.update_status');
Route::post('/plugins/seeders', [PluginController::class, 'runSeeders'])->name('plugins.run_seeders');
