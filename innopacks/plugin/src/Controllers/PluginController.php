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
     * @param  Request  $request
     * @param  $code
     * @return mixed
     * @throws Exception
     */
    public function edit(Request $request, $code): mixed
    {
        $data = [
            'plugin'  => null,
            'columns' => [
                [
                    'name'      => 'app_key',
                    'label'     => 'dsadsad',
                    'label_key' => 'common.app_key',
                    'type'      => 'string',
                    'required'  => true,
                ],
                [
                    'name'      => 'app_key',
                    'label'     => 'dsadsad',
                    'label_key' => 'common.app_key',
                    'type'      => 'checkbox',
                    'required'  => true,
                    'options'   => [
                        ['value' => false, 'label' => 'common.no'],
                        ['value' => true, 'label' => 'common.yes'],
                    ],
                ],
                [
                    'name'      => 'app_secret',
                    'label'     => 'dsadsad',
                    'label_key' => 'common.app_secret',
                    'type'      => 'rich-text',
                    'required'  => true,
                ],
                [
                    'name'      => 'app_secret',
                    'label'     => 'dsadsad',
                    'label_key' => 'common.app_secret',
                    'type'      => 'image',
                    'required'  => true,
                ],
                [
                    'name'      => 'app_secret',
                    'label'     => 'dsadsad',
                    'label_key' => 'common.app_secret',
                    'type'      => 'textarea',
                    'required'  => true,
                ],
                [
                    'name'      => 'app_key',
                    'label'     => 'dsadsad',
                    'label_key' => 'common.app_key',
                    'type'      => 'bool',
                    'required'  => true,
                ],
                [
                    'name'            => 'product_active',
                    'label_key'       => 'common.product_active',
                    'label'           => '89880809',
                    'description_key' => 'common.sync_product_description',
                    'type'            => 'select',
                    'options'         => [
                        ['value' => false, 'label' => 'common.no'],
                        ['value' => true, 'label' => 'common.yes'],
                    ],
                    'required' => true,
                ],
            ],
        ];

        return view('plugin::plugins.form', $data);
    }

    /**
     * @param  Request  $request
     * @param  $code
     * @return void
     * @throws Exception
     */
    public function update(Request $request, $code)
    {

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
