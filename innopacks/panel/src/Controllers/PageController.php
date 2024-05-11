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
use InnoCMS\Common\Models\Page;
use InnoCMS\Common\Repositories\PageRepo;
use InnoCMS\Panel\Requests\PageRequest;

class PageController extends BaseController
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
            'pages' => PageRepo::getInstance()->list($filters),
        ];

        return view('panel::pages.index', $data);
    }

    /**
     * Page creation page.
     *
     * @return mixed
     */
    public function create(): mixed
    {
        $data = [
            'page' => new Page(),
        ];

        return view('panel::pages.form', $data);
    }

    /**
     * @param  PageRequest  $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function store(PageRequest $request): RedirectResponse
    {
        try {
            $data = $request->all();
            PageRepo::getInstance()->create($data);

            return back()->with('success', trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Page  $page
     * @return mixed
     */
    public function edit(Page $page): mixed
    {
        $data = [
            'page' => $page,
        ];

        return view('panel::pages.form', $data);
    }

    /**
     * @param  PageRequest  $request
     * @param  Page  $page
     * @return RedirectResponse
     */
    public function update(PageRequest $request, Page $page): RedirectResponse
    {
        try {
            $data = $request->all();
            PageRepo::getInstance()->update($page, $data);

            return redirect(panel_route('pages.index'))->with('success', trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Page  $page
     * @return RedirectResponse
     */
    public function destroy(Page $page): RedirectResponse
    {
        try {
            PageRepo::getInstance()->destroy($page);

            return back()->with('success', trans('panel::common.deleted_success'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
