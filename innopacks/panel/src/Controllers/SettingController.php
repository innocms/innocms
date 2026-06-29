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
use Illuminate\Http\Request;
use InnoCMS\Common\Repositories\SettingRepo;
use InnoCMS\Common\Services\GeoLite2Service;

class SettingController
{
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        $data = [
            'geolite2_info' => (new GeoLite2Service)->getDatabaseInfo(),
        ];

        return view('panel::settings.index', $data);
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

            return redirect($settingUrl)->with('success', trans('panel/common.updated_success'));
        } catch (Exception $e) {
            return redirect(panel_route('settings.index'))->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Download GeoLite2 database.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function downloadGeoLite2(Request $request): mixed
    {
        try {
            $url    = $request->input('url');
            $result = (new GeoLite2Service)->downloadDatabase($url);

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('panel/setting_geolite2.download_failed', ['error' => $e->getMessage()]),
            ], 400);
        }
    }

    /**
     * Get GeoLite2 database info.
     *
     * @return mixed
     */
    public function getGeoLite2Info(): mixed
    {
        try {
            clearstatcache();

            return response()->json([
                'success' => true,
                'data'    => (new GeoLite2Service)->getDatabaseInfo(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
