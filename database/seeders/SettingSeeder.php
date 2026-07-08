<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Database\Seeders;

use InnoCMS\Common\Models\Setting;

class SettingSeeder extends BaseSeeder
{
    public function run(): void
    {
        $items = $this->getSettings();
        if ($items) {
            $this->safeTruncate(Setting::class);
            foreach ($items as $item) {
                Setting::query()->create($item);
            }
        }
    }

    /**
     * @return array[]
     */
    private function getSettings(): array
    {
        return [
            [
                'id'    => 1,
                'space' => 'system',
                'name'  => 'meta_title',
                'value' => 'InnoCMS - 企业官网系统',
                'json'  => 0,
            ],
            [
                'id'    => 2,
                'space' => 'system',
                'name'  => 'meta_keywords',
                'value' => 'InnoCMS, CMS, 企业官网, 企业官网搭建, 快速建站, 创新, 开源, 多语言, Hook, 插件架构, 灵活, 强大',
                'json'  => 0,
            ],
            [
                'id'    => 3,
                'space' => 'system',
                'name'  => 'meta_description',
                'value' => 'InnoCMS 专为企业官网快速建站而设计的内容管理系统（CMS），简洁、高效、易用，帮助企业搭建专业、美观且功能齐全的官方网站，并支持通过插件便捷扩展。',
                'json'  => 0,
            ],
            [
                'id'    => 4,
                'space' => 'system',
                'name'  => 'front_logo',
                'value' => 'images/logo.png',
                'json'  => 0,
            ],
            [
                'id'    => 5,
                'space' => 'system',
                'name'  => 'panel_logo',
                'value' => 'images/logo-panel.png',
                'json'  => 0,
            ],
            [
                'id'    => 6,
                'space' => 'system',
                'name'  => 'placeholder',
                'value' => 'images/placeholder.svg',
                'json'  => 0,
            ],
            [
                'id'    => 7,
                'space' => 'system',
                'name'  => 'favicon',
                'value' => 'images/favicon.png',
                'json'  => 0,
            ],
            [
                'id'    => 8,
                'space' => 'system',
                'name'  => 'theme',
                'value' => 'default',
                'json'  => 0,
            ],
        ];
    }
}
