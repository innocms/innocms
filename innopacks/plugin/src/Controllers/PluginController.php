<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Plugin\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InnoShop\Plugin\Repositories\PluginRepo;
use InnoShop\Plugin\Repositories\SettingRepo;
use InnoShop\Plugin\Resources\PluginResource;
use Throwable;

class PluginController
{
    /**
     * Get all plugins.
     *
     * @return mixed
     */
    public function index(): mixed
    {
        $plugins = app('plugin')->getPlugins();

        $data = [
            'plugins' => array_values(PluginResource::collection($plugins)->jsonSerialize()),
        ];

        return view('plugin::plugins.index', $data);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $code   = $request->get('code');
            $plugin = app('plugin')->getPluginOrFail($code);
            PluginRepo::getInstance()->installPlugin($plugin);

            return json_success(trans('panel::common.saved_success'));
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * @param  $code
     * @return JsonResponse
     */
    public function destroy($code): JsonResponse
    {
        try {
            $plugin = app('plugin')->getPluginOrFail($code);
            PluginRepo::getInstance()->uninstallPlugin($plugin);

            return json_success(trans('panel::common.deleted_success'));
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * @param  $code
     * @return mixed
     */
    public function edit($code): mixed
    {
        try {
            $plugin = app('plugin')->getPluginOrFail($code);
            $view   = 'plugin::plugins.form';

            $data = [
                'view'    => $view,
                'plugin'  => $plugin,
                'columns' => $plugin->getColumns(),
            ];
            $data = fire_hook_filter('admin.plugin.edit.data', $data);

            return view($view, $data);
        } catch (\Exception $e) {
            $plugin = app('plugin')->getPlugin($code);
            $data   = [
                'error'       => $e->getMessage(),
                'plugin_code' => $code,
                'plugin'      => $plugin,
            ];

            return view('plugin::plugins.error', $data);
        }
    }

    /**
     * @param  Request  $request
     * @param  $code
     * @return mixed
     * @throws Throwable
     */
    public function update(Request $request, $code): mixed
    {
        $fields = $request->all();
        $plugin = app('plugin')->getPluginOrFail($code);
        if (method_exists($plugin, 'validate')) {
            $validator = $plugin->validate($fields);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        }

        SettingRepo::getInstance()->updateValues($fields, $code);

        $url = panel_route('plugins.edit', $code);

        return redirect($url)->with('success', trans('panel::common.updated_success'));
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function updateStatus(Request $request): JsonResponse
    {
        try {
            $code    = $request->get('code');
            $enabled = $request->get('enabled');
            app('plugin')->getPluginOrFail($code);
            SettingRepo::getInstance()->updateValue('active', $enabled, $code);

            return json_success(trans('panel::common.updated_success'));
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        } catch (Throwable $e) {
            return json_fail($e->getMessage());
        }
    }
}
