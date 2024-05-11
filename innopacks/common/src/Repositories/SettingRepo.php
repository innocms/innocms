<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Repositories;

use Illuminate\Database\Eloquent\Builder;
use InnoCMS\Common\Models\Setting;

class SettingRepo extends BaseRepo
{
    /**
     * @param  array  $filters
     * @return Builder
     */
    public function builder(array $filters = []): Builder
    {
        $builder = Setting::query();
        $space   = $filters['space'] ?? '';
        if ($space) {
            $builder->where('space', $space);
        }

        $name = $filters['name'] ?? '';
        if ($name) {
            $builder->where('name', $name);
        }

        return $builder;
    }

    /**
     * Get setting group by space.
     */
    public function groupedSettings(): array
    {
        $settings = Setting::all(['space', 'name', 'value', 'json']);

        $result = [];
        foreach ($settings as $setting) {
            $space = $setting->space;
            $name  = $setting->name;
            $value = $setting->value;
            if ($setting->json) {
                $result[$space][$name] = json_decode($value, true);
            } else {
                $result[$space][$name] = $value;
            }
        }

        return $result;
    }

    /**
     * @param  $settings
     * @return void
     * @throws \Throwable
     */
    public function updateValues($settings): void
    {
        foreach ($settings as $name => $value) {
            if (in_array($name, ['_method', '_token'])) {
                continue;
            }
            $this->updateValue($name, $value);
        }
    }

    /**
     * @param  $name
     * @param  $value
     * @param  string  $space
     * @return mixed
     * @throws \Throwable
     */
    public function updateValue($name, $value, string $space = 'system'): mixed
    {
        if ($value === null) {
            $value = '';
        }

        $setting     = $this->builder(['space' => $space, 'name' => $name])->first();
        $settingData = [
            'space' => $space,
            'name'  => $name,
            'value' => is_array($value) ? json_encode($value) : $value,
            'json'  => is_array($value),
        ];

        if (empty($setting)) {
            $setting = new Setting($settingData);
            $setting->saveOrFail();
        } else {
            $setting->update($settingData);
        }

        return $setting;
    }
}
