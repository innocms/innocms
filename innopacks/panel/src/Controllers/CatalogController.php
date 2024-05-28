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
use InnoCMS\Common\Models\Catalog;
use InnoCMS\Common\Repositories\CatalogRepo;
use InnoCMS\Common\Resources\CatalogSimple;
use InnoCMS\Panel\Requests\CatalogRequest;

class CatalogController
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
            'catalogs' => CatalogRepo::getInstance()->list($filters),
        ];

        return view('panel::catalogs.index', $data);
    }

    /**
     * Catalog creation page.
     *
     * @return mixed
     * @throws \Exception
     */
    public function create(): mixed
    {
        return $this->form(new Catalog);
    }

    /**
     * @param  CatalogRequest  $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function store(CatalogRequest $request): RedirectResponse
    {
        try {
            $data = $request->all();
            CatalogRepo::getInstance()->create($data);

            return redirect(panel_route('catalogs.index'))->with('success', trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Catalog  $catalog
     * @return mixed
     * @throws \Exception
     */
    public function edit(Catalog $catalog): mixed
    {
        return $this->form($catalog);
    }

    /**
     * @param  $catalog
     * @return mixed
     * @throws \Exception
     */
    public function form($catalog): mixed
    {
        $catalogs = CatalogSimple::collection(CatalogRepo::getInstance()->all(['active' => 1]))->jsonSerialize();
        $data     = [
            'catalog'  => $catalog,
            'catalogs' => $catalogs,
        ];

        return view('panel::catalogs.form', $data);
    }

    /**
     * @param  CatalogRequest  $request
     * @param  Catalog  $catalog
     * @return RedirectResponse
     */
    public function update(CatalogRequest $request, Catalog $catalog): RedirectResponse
    {
        try {
            $data = $request->all();
            CatalogRepo::getInstance()->update($catalog, $data);

            return redirect(panel_route('catalogs.index'))
                ->with('success', trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return redirect(panel_route('catalogs.edit', $catalog))
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Catalog  $catalog
     * @return RedirectResponse
     */
    public function destroy(Catalog $catalog): RedirectResponse
    {
        try {
            CatalogRepo::getInstance()->destroy($catalog);

            return back()->with('success', trans('panel::common.deleted_success'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
