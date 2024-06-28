<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Plugin\PartnerLink;

use Plugin\PartnerLink\Models\PartnerLink;

class Boot
{
    public function init(): void
    {
        listen_hook_filter('component.sidebar.plugin.routes', function ($data) {
            $data[] = [
                'route' => 'partner_links.index',
                'title' => '友情链接',
            ];

            return $data;
        });

        listen_blade_insert('layouts.footer.top', function ($data) {
            $data['links'] = PartnerLink::query()->where('active', 1)->limit(10)->get();

            return view('PartnerLink::front.partner_links', $data);
        });
    }
}
