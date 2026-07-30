<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Illuminate\Support\Facades\Route;
use InnoCMS\Aicore\Http\Controllers\PanelApi\AIImageController;

Route::post('/ai/generate_image', [AIImageController::class, 'generate'])->name('ai.generate_image');
Route::get('/ai/models_info', [AIImageController::class, 'modelsInfo'])->name('ai.models_info');
