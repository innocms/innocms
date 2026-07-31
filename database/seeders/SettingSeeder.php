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
                'value' => '傲锋精密 Apex Precision - CNC加工|钣金|压铸一站式精密零部件制造商',
                'json'  => 0,
            ],
            [
                'id'    => 2,
                'space' => 'system',
                'name'  => 'meta_keywords',
                'value' => 'CNC加工,精密零件,五轴加工,钣金加工,压铸,阳极氧化,OEM/ODM,傲锋精密,Apex Precision',
                'json'  => 0,
            ],
            [
                'id'    => 3,
                'space' => 'system',
                'name'  => 'meta_description',
                'value' => '傲锋精密（Apex Precision）成立于 2008 年，提供 CNC 加工、钣金、压铸与表面处理一站式精密零部件制造服务，通过 ISO 9001 / IATF 16949 / ISO 13485 认证，产品远销 30 多个国家和地区。',
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
                'value' => json_encode(['zh-cn' => '专业的企业官网建站系统，简洁、高效、易用。', 'en' => 'A professional enterprise website CMS — clean, efficient and easy to use.']),
                'json'  => 1,
            ],
            [
                'id'    => 11,
                'space' => 'system',
                'name'  => 'address',
                'value' => json_encode(['zh-cn' => '中国 北京市 朝阳区', 'en' => 'Chaoyang District, Beijing, China']),
                'json'  => 1,
            ],
            [
                'id'    => 12,
                'space' => 'system',
                'name'  => 'telephone',
                'value' => '400-888-8888',
                'json'  => 0,
            ],
            [
                'id'    => 13,
                'space' => 'system',
                'name'  => 'email',
                'value' => 'contact@example.com',
                'json'  => 0,
            ],
        ];
    }
}
