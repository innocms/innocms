<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InnoCMS\Common\Models\Admin;
use InnoCMS\Common\Repositories\AdminRepo;
use InnoCMS\Common\Repositories\RoleRepo;
use Throwable;

class AdminController extends BaseController
{
    /**
     * @param  Request  $request
     * @return mixed
     * @throws Exception
     */
    public function index(Request $request): mixed
    {
        $filters = $request->all();
        $data    = [
            'admins' => AdminRepo::getInstance()->list($filters),
        ];

        return view('panel::admins.index', $data);
    }

    /**
     * @param  Admin  $admin
     * @return Admin
     */
    public function show(Admin $admin): Admin
    {
        return $admin->load(['adminStates']);
    }

    /**
     * Admin creation page.
     *
     * @return mixed
     * @throws Exception
     */
    public function create(): mixed
    {
        return $this->form(new Admin);
    }

    /**
     * @param  Request  $request
     * @return mixed
     * @throws Throwable
     */
    public function store(Request $request): mixed
    {
        try {
            $data = $request->all();
            AdminRepo::getInstance()->create($data);

            return redirect(panel_route('admins.index'))
                ->with('success', trans('panel::common.created_success'));
        } catch (Exception $e) {
            return redirect(panel_route('admins.index'))
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Admin  $admin
     * @return mixed
     * @throws Exception
     */
    public function edit(Admin $admin): mixed
    {
        return $this->form($admin);
    }

    /**
     * @param  $admin
     * @return mixed
     * @throws Exception
     */
    public function form($admin): mixed
    {
        $data = [
            'admin' => $admin,
            'roles' => RoleRepo::getInstance()->list(),
        ];

        return view('panel::admins.form', $data);
    }

    /**
     * @param  Request  $request
     * @param  Admin  $admin
     * @return mixed
     */
    public function update(Request $request, Admin $admin): mixed
    {
        try {
            $data = $request->all();
            AdminRepo::getInstance()->update($admin, $data);

            return redirect(panel_route('admins.index'))
                ->with('success', trans('panel::common.updated_success'));
        } catch (Exception $e) {
            return redirect(panel_route('admins.index'))
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Admin  $admin
     * @return RedirectResponse
     */
    public function destroy(Admin $admin): RedirectResponse
    {
        try {
            AdminRepo::getInstance()->destroy($admin);

            return redirect(panel_route('admins.index'))
                ->with('success', trans('panel::common.deleted_success'));
        } catch (Exception $e) {
            return redirect(panel_route('admins.index'))
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
