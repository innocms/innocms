<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Database\Seeders;

use InnoCMS\Common\Models\Locale;

class LocaleSeeder extends BaseSeeder
{
    public function run(): void
    {
        $items = $this->getLocales();
        if ($items) {
            $this->safeTruncate(Locale::class);
            foreach ($items as $item) {
                Locale::query()->create($item);
            }
        }
    }

    private function getLocales(): array
    {
        return [
            [
                'id'       => 1,
                'name'     => '简体中文',
                'code'     => 'zh-cn',
                'image'    => 'images/flags/zh-cn.svg',
                'position' => 1,
                'active'   => 1,
            ],
            [
                'id'       => 2,
                'name'     => 'English',
                'code'     => 'en',
                'image'    => 'images/flags/en.svg',
                'position' => 2,
                'active'   => 1,
            ],
        ];
    }
}
