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
use Illuminate\Http\Request;
use InnoCMS\Common\Models\Category;
use InnoCMS\Common\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request): mixed
    {
        $cat      = trim((string) $request->query('cat', ''));
        $category = null;
        $query    = Product::query()
            ->with(['translations', 'categories'])
            ->where('active', true);

        // ?cat=<slug|id> filters the list to one category; absent = all active products.
        if ($cat !== '') {
            $category = Category::query()
                ->where('active', true)
                ->where(function ($q) use ($cat): void {
                    $q->where('slug', $cat);
                    if (ctype_digit($cat)) {
                        $q->orWhere('id', (int) $cat);
                    }
                })
                ->first();

            if ($category) {
                $query->whereHas('categories', function ($q) use ($category): void {
                    $q->where('categories.id', $category->id);
                });
            } else {
                // An explicit but invalid ?cat= (typo / deleted / deactivated) must
                // not degrade into the full catalog — 404 the storefront archive.
                abort(404);
            }
        }

        $products = $query->orderBy('position')->get();

        return inno_view('products.index', [
            'products' => $products,
            'category' => $category,
        ]);
    }

    public function show(Product $product): mixed
    {
        abort_unless($product->active, 404);

        $product->load(['translations', 'categories', 'relationProducts']);

        return $this->renderShow($product);
    }

    public function slugShow(string $slug): mixed
    {
        $product = Product::query()
            ->with(['translations', 'categories', 'relationProducts'])
            ->where('active', true)
            ->where(function ($q) use ($slug): void {
                $q->where('slug', $slug);
                // Resolve the ID fallback used by Product::front_url for slug-less products.
                if (ctype_digit($slug)) {
                    $q->orWhere('id', (int) $slug);
                }
            })
            ->firstOrFail();

        return $this->renderShow($product);
    }

    private function renderShow(Product $product): mixed
    {
        $product->increment('viewed');

        $categoryIds = $product->categories->pluck('id');
        $related     = Product::query()
            ->with('translations')
            ->where('active', true)
            ->when($categoryIds->isNotEmpty(), function ($q) use ($categoryIds) {
                $q->whereHas('categories', function ($cq) use ($categoryIds): void {
                    $cq->whereIn('categories.id', $categoryIds);
                });
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
