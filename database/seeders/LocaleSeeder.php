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
            [
                'id'       => 3,
                'name'     => 'Deutsch',
                'code'     => 'de',
                'image'    => 'images/flags/de.svg',
                'position' => 3,
                'active'   => 1,
            ],
            [
                'id'       => 4,
                'name'     => 'Español',
                'code'     => 'es',
                'image'    => 'images/flags/es.svg',
                'position' => 4,
                'active'   => 1,
            ],
            [
                'id'       => 5,
                'name'     => 'Français',
                'code'     => 'fr',
                'image'    => 'images/flags/fr.svg',
                'position' => 5,
                'active'   => 1,
            ],
            [
                'id'       => 6,
                'name'     => 'Italiano',
                'code'     => 'it',
                'image'    => 'images/flags/it.svg',
                'position' => 6,
                'active'   => 1,
            ],
            [
                'id'       => 7,
                'name'     => '日本語',
                'code'     => 'ja',
                'image'    => 'images/flags/ja.svg',
                'position' => 7,
                'active'   => 1,
            ],
            [
                'id'       => 8,
                'name'     => '한국어',
                'code'     => 'ko',
                'image'    => 'images/flags/ko.svg',
                'position' => 8,
                'active'   => 1,
            ],
            [
                'id'       => 9,
                'name'     => 'Português',
                'code'     => 'pt',
                'image'    => 'images/flags/pt.svg',
                'position' => 9,
                'active'   => 1,
            ],
            [
                'id'       => 10,
                'name'     => 'Русский',
                'code'     => 'ru',
                'image'    => 'images/flags/ru.svg',
                'position' => 10,
                'active'   => 1,
            ],
        ];
    }
}
