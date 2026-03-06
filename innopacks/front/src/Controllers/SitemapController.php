<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Controllers;

use Exception;
use Illuminate\Http\Request;
use InnoCMS\Common\Services\SitemapService;

class SitemapController
{
    /**
     * @param  Request  $request
     * @return mixed
     * @throws Exception
     */
    public function index(Request $request): mixed
    {
        try {
            return SitemapService::getInstance()->response($request);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
