<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InnoCMS\Common\Models\Category;
use InnoCMS\Common\Repositories\CategoryRepo;
use InnoCMS\Panel\Requests\CategoryRequest;

class CategoryController extends BaseController
{
    /**
     * @param  Request  $request
     * @return mixed
     * @throws \Exception
     */
    public function index(Request $request): mixed
    {
        $filters = $request->all();
        $data    = [
            'searchFields'  => CategoryRepo::getSearchFieldOptions(),
            'filterButtons' => CategoryRepo::getFilterButtonOptions(),
            'categories'    => CategoryRepo::getInstance()->list($filters),
        ];

        return view('panel::categories.index', $data);
    }

    /**
     * Category creation page.
     *
     * @return mixed
     * @throws \Exception
     */
    public function create(): mixed
    {
        return $this->form(new Category);
    }

    /**
     * @param  CategoryRequest  $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            CategoryRepo::getInstance()->create($request->all());

            return redirect(panel_route('categories.index'))->with('success', trans('panel/common.updated_success'));
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Category  $category
     * @return mixed
     * @throws \Exception
     */
    public function edit(Category $category): mixed
    {
        return $this->form($category);
    }

    /**
     * @param  $category
     * @return mixed
     * @throws \Exception
     */
    public function form($category): mixed
    {
        // Exclude the category itself from the parent picker to avoid self-reference.
        // (Descendants are still listed; the model guards circular references on save.)
        $excludeIds = $category->id ? [$category->id] : [];
        $categories = CategoryRepo::getInstance()->getHierarchicalCategories([
            'exclude_ids' => $excludeIds,
        ]);

        $data = [
            'category'   => $category,
            'categories' => $categories,
        ];

        return view('panel::categories.form', $data);
    }

    /**
     * @param  CategoryRequest  $request
     * @param  Category  $category
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        try {
            CategoryRepo::getInstance()->update($category, $request->all());

            return redirect(panel_route('categories.index'))
                ->with('success', trans('panel/common.updated_success'));
        } catch (\Exception $e) {
            return redirect(panel_route('categories.edit', $category))
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Category  $category
     * @return RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        try {
            CategoryRepo::getInstance()->destroy($category);

            return back()->with('success', trans('panel/common.deleted_success'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
