<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     XING GUI YU <xingguiyu@foxmail.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Illuminate\Support\Facades\Route;
use Plugin\Carousel\Controllers\PanelCarouselController;
use Plugin\Carousel\Controllers\PanelCarouselImageController;

Route::resource('/carousels', PanelCarouselController::class);
Route::resource('/carousels/{carousel}/images', PanelCarouselImageController::class);
