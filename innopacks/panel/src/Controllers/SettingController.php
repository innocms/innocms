<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use Illuminate\Http\Request;
use InnoCMS\Common\Repositories\SettingRepo;

class SettingController
{
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        return view('panel::settings.index');
    }

    /**
     * @param  Request  $request
     * @return mixed
     * @throws \Throwable
     */
    public function update(Request $request): mixed
    {
        $settings = $request->all();

        try {
            SettingRepo::getInstance()->updateValues($settings);
            $oldAdminName = panel_name();
            $newAdminName = $settings['panel_name'] ?? 'panel';
            $settingUrl   = str_replace($oldAdminName, $newAdminName, panel_route('settings.index'));

            return redirect($settingUrl)->with('success', trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return redirect(panel_route('settings.index'))->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
