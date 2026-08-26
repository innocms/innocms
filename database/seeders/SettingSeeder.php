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
                'value' => 'InnoCMS - 轻量级 B2B 独立站系统 | 模块化 · 插件 · 主题 · 多语言',
                'json'  => 0,
            ],
            [
                'id'    => 2,
                'space' => 'system',
                'name'  => 'meta_keywords',
                'value' => 'InnoCMS, B2B独立站, 独立站系统, 外贸网站, 快速建站, 开源, 多语言, Hook, 插件架构, 主题, 轻量级',
                'json'  => 0,
            ],
            [
                'id'    => 3,
                'space' => 'system',
                'name'  => 'meta_description',
                'value' => 'InnoCMS 是一款轻量级 B2B 独立站系统，基于 Laravel，采用模块化 innopacks 架构与 Hook 插件系统，内置商品、主题切换与多语言，几分钟即可上线品牌独立站。',
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
            [
                'id'    => 9,
                'space' => 'system',
                'name'  => 'store_name',
                'value' => json_encode(['zh-cn' => 'InnoCMS', 'en' => 'InnoCMS']),
                'json'  => 1,
            ],
            [
                'id'    => 10,
                'space' => 'system',
                'name'  => 'store_description',
                'value' => json_encode(['zh-cn' => '专业的轻量级 B2B 独立站系统，内置商品，简洁、高效、易用。', 'en' => 'A professional lightweight B2B standalone website system with built-in products — clean, efficient and easy to use.']),
                'json'  => 1,
            ],
            [
                'id'    => 11,
                'space' => 'system',
                'name'  => 'address',
                'value' => json_encode(['zh-cn' => '成都高新区吉泰路566号', 'en' => 'No. 566 Jitai Road, High-tech Zone, Chengdu']),
                'json'  => 1,
            ],
            [
                'id'    => 12,
                'space' => 'system',
                'name'  => 'telephone',
                'value' => '+86 136-4808-9236',
                'json'  => 0,
            ],
            [
                'id'    => 13,
                'space' => 'system',
                'name'  => 'email',
                'value' => 'team@innoshop.com',
                'json'  => 0,
            ],
            [
                'id'    => 14,
                'space' => 'system',
                'name'  => 'business_hours',
                'value' => json_encode(['zh-cn' => '周一至周五 9:00-18:00', 'en' => 'Mon-Fri 9:00-18:00']),
                'json'  => 1,
            ],
        ];
    }
}
