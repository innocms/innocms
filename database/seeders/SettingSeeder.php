<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use InnoCMS\Common\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $items = $this->getSettings();
        if ($items) {
            Setting::query()->truncate();
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
                'value' => 'InnoCMS - 轻量级企业官网系统',
                'json'  => 0,
            ],
            [
                'id'    => 2,
                'space' => 'system',
                'name'  => 'meta_keywords',
                'value' => 'InnoCMS, CMS, 企业官网, 企业官网搭建, 快速建站, 轻量级, 创新, 开源, 多语言, Hook, 插件架构, 灵活, 强大',
                'json'  => 0,
            ],
            [
                'id'    => 3,
                'space' => 'system',
                'name'  => 'meta_description',
                'value' => 'InnoCMS 是一款专为企业官网快速建站而设计的轻量级内容管理系统（CMS）。它以其简洁、高效、易用的特性，帮助企业快速搭建起专业、美观且功能齐全的官方网站。InnoCMS 旨在提供一个稳定而灵活的平台，让企业能够轻松管理网站内容，同时也可以通过插件方便二次开发。',
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
                'value' => 'images/placeholder.png',
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
