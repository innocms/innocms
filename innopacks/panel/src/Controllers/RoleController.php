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
use InnoCMS\Common\Repositories\RoleRepo;
use InnoCMS\Panel\Repositories\RouteRepo;
use Spatie\Permission\Models\Role;

class RoleController extends BaseController
{
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        $data = [
            'roles' => Role::query()->paginate(),
        ];

        return view('panel::roles.index', $data);
    }

    /**
     * @param  Role  $role
     * @return Role
     */
    public function show(Role $role): Role
    {
        return $role;
    }

    /**
     * Role creation page.
     *
     * @return mixed
     * @throws Exception
     */
    public function create(): mixed
    {
        return $this->form(new Role);
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function store(Request $request): mixed
    {
        try {
            $data = $request->all();
            RoleRepo::getInstance()->create($data);

            return redirect(panel_route('roles.index'))
                ->with('success', trans('panel::common.created_success'));
        } catch (Exception $e) {
            return redirect(panel_route('roles.index'))
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Role  $role
     * @return mixed
     * @throws Exception
     */
    public function edit(Role $role): mixed
    {
        return $this->form($role);
    }

    /**
     * @param  Role  $role
     * @return mixed
     */
    public function form(Role $role): mixed
    {
        $data = [
            'role'        => $role,
            'permissions' => RouteRepo::getInstance($role)->getPanelPermissions(),
        ];

        return view('panel::roles.form', $data);
    }

    /**
     * @param  Request  $request
     * @param  Role  $role
     * @return mixed
     */
    public function update(Request $request, Role $role): mixed
    {
        try {
            $data = $request->all();
            RoleRepo::getInstance()->update($role, $data);

            return redirect(panel_route('roles.index'))
                ->with('success', trans('panel::common.updated_success'));
        } catch (Exception $e) {
            return redirect(panel_route('roles.index'))
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Role  $role
     * @return RedirectResponse
     */
    public function destroy(Role $role): RedirectResponse
    {
        try {
            RoleRepo::getInstance()->destroy($role);

            return redirect(panel_route('roles.index'))
                ->with('success', trans('panel::common.deleted_success'));
        } catch (Exception $e) {
            return redirect(panel_route('roles.index'))
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
