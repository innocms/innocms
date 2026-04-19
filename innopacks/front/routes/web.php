<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Illuminate\Support\Facades\Route;
use InnoCMS\Common\Repositories\PageRepo;
use InnoCMS\Front\Controllers;

$hasSuffix = installed() && system_setting('has_suffix');

Route::get('/', [Controllers\HomeController::class, 'index'])->name('home.index');

// Upload
Route::post('/upload/images', [Controllers\UploadController::class, 'images'])->name('upload.images');
Route::post('/upload/files', [Controllers\UploadController::class, 'files'])->name('upload.files');

if ($hasSuffix) {
    // Catalogs
    Route::get('/catalogs.html', [Controllers\CatalogController::class, 'index'])->name('catalogs.index');
    Route::get('/catalogs/{catalog}.html', [Controllers\CatalogController::class, 'show'])->name('catalogs.show');
    Route::get('/catalog-{slug}.html', [Controllers\CatalogController::class, 'slugShow'])->name('catalogs.slug_show');

    // Articles
    Route::get('/articles.html', [Controllers\ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{article}.html', [Controllers\ArticleController::class, 'show'])->name('articles.show');
    Route::get('/article-{slug}.html', [Controllers\ArticleController::class, 'slugShow'])->name('articles.slug_show');

    // Tags
    Route::get('/tags.html', [Controllers\TagController::class, 'index'])->name('tags.index');
    Route::get('/tags-{slug}.html', [Controllers\TagController::class, 'show'])->name('tags.show');

    // Pages, like product, service, about
    if (installed()) {
        $pages = PageRepo::getInstance()->withActive()->builder()->get();
        foreach ($pages as $page) {
            Route::get($page->slug.'.html', [Controllers\PageController::class, 'show'])->name('pages.'.$page->slug);
        }
    }
} else {
    // Catalogs
    Route::get('/catalogs', [Controllers\CatalogController::class, 'index'])->name('catalogs.index');
    Route::get('/catalogs/{catalog}', [Controllers\CatalogController::class, 'show'])->name('catalogs.show');
    Route::get('/catalog-{slug}', [Controllers\CatalogController::class, 'slugShow'])->name('catalogs.slug_show');

    // Articles
    Route::get('/articles', [Controllers\ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{article}', [Controllers\ArticleController::class, 'show'])->name('articles.show');
    Route::get('/article-{slug}', [Controllers\ArticleController::class, 'slugShow'])->name('articles.slug_show');

    // Tags
    Route::get('/tags', [Controllers\TagController::class, 'index'])->name('tags.index');
    Route::get('/tags-{slug}', [Controllers\TagController::class, 'show'])->name('tags.show');

    // Pages, like product, service, about
    if (installed()) {
        $pages = PageRepo::getInstance()->withActive()->builder()->get();
        foreach ($pages as $page) {
            Route::get($page->slug, [Controllers\PageController::class, 'show'])->name('pages.'.$page->slug);
        }
    }
}

// Official service demo pages
Route::prefix('official_demo')
    ->name('official.demo.')
    ->group(function () {
        Route::get('/{slug}', 'InnoCMS\Front\Controllers\PageController@showOfficialDemoPage')->name('pages.show');
    });
