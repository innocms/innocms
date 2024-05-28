<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoShop\Plugin\Repositories;

use InnoCMS\Common\Repositories\SettingRepo as CommonSettingRepo;
use InnoShop\Plugin\Models\Setting;

class SettingRepo extends CommonSettingRepo
{
    /**
     * Get plugin active column.
     *
     * @return array
     */
    public function getPluginActiveColumn(): array
    {
        return [
            'name'     => 'active',
            'label'    => trans('panel::common.active'),
            'type'     => 'bool',
            'required' => true,
        ];
    }

    /**
     * Get all columns by plugin code.
     *
     * @param  $pluginCode
     * @return mixed
     */
    public static function getPluginColumns($pluginCode): mixed
    {
        return Setting::query()
            ->where('space', $pluginCode)
            ->get()
            ->keyBy('name');
    }
}
