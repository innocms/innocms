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
use InnoCMS\Common\Models\Product;
use InnoCMS\Common\Repositories\ProductRepo;
use InnoCMS\Panel\Requests\ProductRequest;

class ProductController extends BaseController
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
            'searchFields'  => ProductRepo::getSearchFieldOptions(),
            'filterButtons' => ProductRepo::getFilterButtonOptions(),
            'products'      => ProductRepo::getInstance()->list($filters),
        ];

        return view('panel::products.index', $data);
    }

    /**
     * Product creation page.
     *
     * @return mixed
     * @throws \Exception
     */
    public function create(): mixed
    {
        return $this->form(new Product);
    }

    /**
     * @param  ProductRequest  $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        try {
            ProductRepo::getInstance()->create($request->all());

            return redirect(panel_route('products.index'))->with('success', trans('panel/common.updated_success'));
        } catch (\Exception $e) {
            return redirect(panel_route('products.create'))->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Product  $product
     * @return mixed
     * @throws \Exception
     */
    public function edit(Product $product): mixed
    {
        return $this->form($product);
    }

    /**
     * Categories and related products are resolved via the autocomplete-list
     * pickers (restapi), so the form only needs the product itself.
     *
     * @param  $product
     * @return mixed
     * @throws \Exception
     */
    public function form($product): mixed
    {
        return view('panel::products.form', ['product' => $product]);
    }

    /**
     * @param  ProductRequest  $request
     * @param  Product  $product
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        try {
            ProductRepo::getInstance()->update($product, $request->all());

            return redirect(panel_route('products.index'))
                ->with('success', trans('panel/common.updated_success'));
        } catch (\Exception $e) {
            return redirect(panel_route('products.edit', $product))
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Product  $product
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            ProductRepo::getInstance()->destroy($product);

            return back()->with('success', trans('panel/common.deleted_success'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Duplicate a product (translations / categories / relations).
     *
     * @param  Product  $product
     * @return RedirectResponse
     */
    public function copy(Product $product): RedirectResponse
    {
        try {
            ProductRepo::getInstance()->copy($product);

            return redirect(panel_route('products.index'))->with('success', trans('panel/common.created_success'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
