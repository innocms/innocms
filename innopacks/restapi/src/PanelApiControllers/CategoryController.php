<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Restapi\PanelApiControllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use InnoCMS\Common\Models\Category;
use InnoCMS\Common\Repositories\CategoryRepo;
use InnoCMS\Common\Resources\CategoryName;
use InnoCMS\Common\Resources\CategorySimple;
use InnoCMS\Panel\Requests\CategoryRequest;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\UrlParam;
use Throwable;

#[Group('Panel - Product Categories')]
class CategoryController extends BaseController
{
    /**
     * List categories / resolve a set of IDs.
     * The autocomplete-list picker initialises with `?tag_ids=1,2,3`
     * (the param name is a shared picker convention).
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection
     */
    #[Endpoint('List product categories')]
    #[QueryParam('tag_ids', 'string', required: false, description: 'Comma-separated category IDs (used by the category picker)')]
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->all();
        if (isset($filters['tag_ids'])) {
            $filters['category_ids'] = array_filter(explode(',', $filters['tag_ids']), 'is_numeric');
            unset($filters['tag_ids']);
        }

        $categories = CategoryRepo::getInstance()->builder($filters)->limit(50)->get();

        return CategorySimple::collection($categories);
    }

    /**
     * Fuzzy search for the category picker.
     * /api/panel/categories/autocomplete?keyword=xxx
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection
     */
    #[Endpoint('Autocomplete product categories')]
    #[QueryParam('keyword', 'string', required: false)]
    public function autocomplete(Request $request): AnonymousResourceCollection
    {
        $keyword    = $request->get('keyword');
        $categories = CategoryRepo::getInstance()->autocomplete($keyword, 50);

        return CategoryName::collection($categories);
    }

    /**
     * Create a product category.
     * POST /api/panel/categories
     *
     * @param  CategoryRequest  $request
     * @return mixed
     * @throws Throwable
     */
    #[Endpoint('Create product category')]
    public function store(CategoryRequest $request): mixed
    {
        try {
            $data     = $request->all();
            $category = CategoryRepo::getInstance()->create($data);

            return json_success(common_trans('base.updated_success'), $category);
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Get product category detail.
     * GET /api/panel/categories/{category}
     *
     * @param  Category  $category
     * @return mixed
     */
    #[Endpoint('Get product category detail')]
    #[UrlParam('category', 'integer', description: 'Category ID')]
    public function show(Category $category): mixed
    {
        $category->load(['translations', 'parent', 'children.translations']);

        return read_json_success($category);
    }

    /**
     * Update a product category.
     * PUT /api/panel/categories/{category}
     *
     * @param  CategoryRequest  $request
     * @param  Category  $category
     * @return mixed
     * @throws Throwable
     */
    #[Endpoint('Update product category')]
    #[UrlParam('category', 'integer', description: 'Category ID')]
    public function update(CategoryRequest $request, Category $category): mixed
    {
        try {
            $data = $request->all();
            CategoryRepo::getInstance()->update($category, $data);

            return update_json_success($category);
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Partial update a product category.
     * PATCH /api/panel/categories/{category}
     *
     * @param  CategoryRequest  $request
     * @param  Category  $category
     * @return mixed
     * @throws Throwable
     */
    #[Endpoint('Partial update product category')]
    #[UrlParam('category', 'integer', description: 'Category ID')]
    public function patch(CategoryRequest $request, Category $category): mixed
    {
        try {
            $data = $request->validated();
            CategoryRepo::getInstance()->patch($category, $data);

            return update_json_success($category);
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Delete a product category. Refuses when the category still has children.
     * DELETE /api/panel/categories/{category}
     *
     * @param  Category  $category
     * @return mixed
     */
    #[Endpoint('Delete product category')]
    #[UrlParam('category', 'integer', description: 'Category ID')]
    public function destroy(Category $category): mixed
    {
        try {
            if ($category->children()->count()) {
                throw new Exception(trans('panel/category.has_children'));
            }
            CategoryRepo::getInstance()->destroy($category);

            return json_success(common_trans('base.deleted_success'));
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
