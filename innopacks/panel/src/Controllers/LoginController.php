<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use InnoCMS\Panel\Requests\LoginRequest;

class LoginController extends BaseController
{
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        if (auth('admin')->check()) {
            return redirect()->back();
        }

        return view('panel::login');
    }

    /**
     * Login post request
     *
     * @param  LoginRequest  $request
     * @return mixed
     */
    public function store(LoginRequest $request): mixed
    {
        if (auth('admin')->attempt($request->validated())) {
            return redirect(panel_route('home.index'));
        }

        return redirect()->back()->with(['error' => trans('auth.failed')])->withInput();
    }
}
