<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Plugin\Custom;

class Boot
{
    public function init(): void
    {
        listen_hook_filter('global.header.menus', function ($data) {
            return [
                ['name' => '产品', 'url' => front_route('pages.products')],
                ['name' => '服务', 'url' => front_route('pages.services')],
                ['name' => '新闻', 'url' => front_route('articles.index')],
                ['name' => '关于', 'url' => front_route('pages.about')],
            ];
        });
    }
}
