<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InnoCMS\Common\Repositories\CatalogRepo;
use InnoCMS\Common\Repositories\PageRepo;
use InnoCMS\Common\Repositories\SettingRepo;
use InnoCMS\Panel\Repositories\ThemeRepo;

class ThemeController extends BaseController
{
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        $data = [
            'themes' => ThemeRepo::getInstance()->getListFromPath(),
        ];

        return view('panel::themes.index', $data);
    }

    /**
     * @return mixed
     */
    public function settings(): mixed
    {
        $data = [
            'catalogs' => CatalogRepo::getInstance()->getTopCatalogs(),
            'pages'    => PageRepo::getInstance()->withActive()->builder()->get(),
            'themes'   => ThemeRepo::getInstance()->getListFromPath(),
        ];

        return view('panel::themes.settings', $data);
    }

    /**
     * @param  Request  $request
     * @return mixed
     * @throws \Throwable
     */
    public function updateSettings(Request $request): mixed
    {
        $settings   = $request->all();
        $settingUrl = panel_route('themes_settings.index');

        try {
            SettingRepo::getInstance()->updateValues($settings);

            return redirect($settingUrl)->with('success', trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return redirect($settingUrl)->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  string  $themeCode
     * @return JsonResponse
     * @throws \Throwable
     */
    public function enable(string $themeCode): JsonResponse
    {
        try {
            SettingRepo::getInstance()->updateSystemValue('theme', $themeCode);

            return json_success(trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
