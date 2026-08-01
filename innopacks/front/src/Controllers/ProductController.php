<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Controllers;

use App\Http\Controllers\Controller;
use InnoCMS\Common\Models\Category;
use InnoCMS\Common\Models\Product;

class ProductController extends Controller
{
    public function index(): mixed
    {
        $products = Product::query()
            ->with(['translations', 'categories'])
            ->where('active', true)
            ->whereHas('categories', function ($q) {
                $q->where('slug', 'software-products');
            })
            ->orderBy('position')
            ->get();

        $category = Category::query()
            ->where('slug', 'software-products')
            ->where('active', true)
            ->first();

        return inno_view('products.index', [
            'products' => $products,
            'category' => $category,
        ]);
    }

    public function show(string $slug): mixed
    {
        $product = Product::query()
            ->with(['translations', 'categories', 'relationProducts'])
            ->where('slug', $slug)
            ->where('active', true)
            ->firstOrFail();

        $product->increment('viewed');

        $related = Product::query()
            ->with('translations')
            ->where('active', true)
            ->whereHas('categories', function ($q) {
                $q->where('slug', 'software-products');
            })
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        return inno_view('products.show', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}
