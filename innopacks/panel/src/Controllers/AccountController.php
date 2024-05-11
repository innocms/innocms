<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

class AccountController extends BaseController
{
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        $data = [
            'admin' => current_admin(),
        ];

        return view('panel::account.index', $data);
    }

    public function update()
    {

    }
}
