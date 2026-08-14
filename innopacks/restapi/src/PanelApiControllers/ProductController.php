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
use InnoCMS\Common\Models\Product;
use InnoCMS\Common\Repositories\ProductRepo;
use InnoCMS\Common\Resources\ProductName;
use InnoCMS\Common\Resources\ProductSimple;
use InnoCMS\Panel\Requests\ProductRequest;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\UrlParam;
use Throwable;

#[Group('Panel - Products')]
class ProductController extends BaseController
{
    /**
     * List products / resolve a set of IDs.
     * The autocomplete-list picker initialises with `?tag_ids=1,2,3`
     * (the param name is a shared picker convention).
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection
     */
    #[Endpoint('List products')]
    #[QueryParam('tag_ids', 'string', required: false, description: 'Comma-separated product IDs (used by the related-product picker)')]
    #[QueryParam('per_page', 'integer', required: false, description: 'Items per page')]
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->all();
        if (isset($filters['tag_ids'])) {
            $filters['product_ids'] = array_filter(explode(',', $filters['tag_ids']), 'is_numeric');
            unset($filters['tag_ids']);

            $products = ProductRepo::getInstance()->builder($filters)->limit(20)->get();

            return ProductSimple::collection($products);
        }

        $products = ProductRepo::getInstance()->list($filters);

        return ProductSimple::collection($products);
    }

    /**
     * Fuzzy search for the related-product picker.
     * /api/panel/products/autocomplete?keyword=xxx
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection
     */
    #[Endpoint('Autocomplete products')]
    #[QueryParam('keyword', 'string', required: false)]
    public function autocomplete(Request $request): AnonymousResourceCollection
    {
        $keyword  = $request->get('keyword');
        $products = ProductRepo::getInstance()->autocomplete($keyword, 20);

        return ProductName::collection($products);
    }

    /**
     * Create a product.
     * POST /api/panel/products
     *
     * @param  ProductRequest  $request
     * @return mixed
     * @throws Throwable
     */
    #[Endpoint('Create product')]
    public function store(ProductRequest $request): mixed
    {
        try {
            $data    = $request->all();
            $product = ProductRepo::getInstance()->create($data);

            return json_success(common_trans('base.updated_success'), $product);
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Get product detail.
     * GET /api/panel/products/{product}
     *
     * @param  Product  $product
     * @return mixed
     */
    #[Endpoint('Get product detail')]
    #[UrlParam('product', 'integer', description: 'Product ID')]
    public function show(Product $product): mixed
    {
        $product->load(['translations', 'categories.translations']);

        return read_json_success($product);
    }

    /**
     * Update a product.
     * PUT /api/panel/products/{product}
     *
     * @param  ProductRequest  $request
     * @param  Product  $product
     * @return mixed
     * @throws Throwable
     */
    #[Endpoint('Update product')]
    #[UrlParam('product', 'integer', description: 'Product ID')]
    public function update(ProductRequest $request, Product $product): mixed
    {
        try {
            $data = $request->all();
            ProductRepo::getInstance()->update($product, $data);

            return update_json_success($product);
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Partial update a product.
     * PATCH /api/panel/products/{product}
     *
     * @param  ProductRequest  $request
     * @param  Product  $product
     * @return mixed
     * @throws Throwable
     */
    #[Endpoint('Partial update product')]
    #[UrlParam('product', 'integer', description: 'Product ID')]
    public function patch(ProductRequest $request, Product $product): mixed
    {
        try {
            $data = $request->validated();
            ProductRepo::getInstance()->patch($product, $data);

            return update_json_success($product);
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Delete a product.
     * DELETE /api/panel/products/{product}
     *
     * @param  Product  $product
     * @return mixed
     */
    #[Endpoint('Delete product')]
    #[UrlParam('product', 'integer', description: 'Product ID')]
    public function destroy(Product $product): mixed
    {
        try {
            ProductRepo::getInstance()->destroy($product);

            return json_success(common_trans('base.deleted_success'));
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
