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

class CategoryController extends Controller
{
    /**
     * Category index: list active categories.
     *
     * @return mixed
     */
    public function index(): mixed
    {
        $categories = Category::query()
            ->with('translation')
            ->where('active', true)
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        return inno_view('categories.index', ['categories' => $categories]);
    }

    /**
     * Show a category and the active products within it (id binding).
     *
     * @param  Category  $category
     * @return mixed
     */
    public function show(Category $category): mixed
    {
        abort_unless($category->active, 404);

        return $this->renderShow($category);
    }

    /**
     * Show a category by slug and the active products within it.
     *
     * @param  string  $slug
     * @return mixed
     */
    public function slugShow(string $slug): mixed
    {
        $category = Category::query()
            ->with('translation')
            ->where('slug', $slug)
            ->where('active', true)
            ->firstOrFail();

        return $this->renderShow($category);
    }

    /**
     * Render the category show page with its active products.
     *
     * @param  Category  $category
     * @return mixed
     */
    private function renderShow(Category $category): mixed
    {
        $category->load('translation');

        $products = Product::query()
            ->with(['translations', 'categories'])
            ->where('active', true)
            ->whereHas('categories', function ($q) use ($category): void {
                $q->where('categories.id', $category->id);
            })
            ->orderBy('position')
            ->get();

        return inno_view('categories.show', ['category' => $category, 'products' => $products]);
    }
}
