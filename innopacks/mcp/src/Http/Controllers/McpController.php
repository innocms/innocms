<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Mcp\Http\Controllers;

use Illuminate\Contracts\View\View;
use InnoCMS\Aicore\Services\ToolRegistry;

class McpController
{
    public function welcome(ToolRegistry $registry): View
    {
        $name = system_setting('panel_name') ?: system_setting_locale('meta_title') ?: config('app.name', 'InnoCMS');

        $locale = request()->query('lang') ?: front_locale_code();
        if ($locale) {
            app()->setLocale($locale);
        }

        return view('mcp::welcome', [
            'shopName' => $name,
            'shopLogo' => asset('images/logo-icon-light.svg'),
            'mcpUrl'   => url('/mcp'),
            'loginUrl' => url('/api/panel/login'),
            'tools'    => $registry->all(),
        ]);
    }
}
