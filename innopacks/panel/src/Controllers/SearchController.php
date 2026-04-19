<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InnoCMS\Panel\Repositories\MenuSearchRepo;

class SearchController extends BaseController
{
    /**
     * Search panel menus by keyword.
     */
    public function menus(Request $request): JsonResponse
    {
        $keyword = mb_substr(trim($request->get('keyword', '')), 0, 100);
        $results = MenuSearchRepo::getInstance()->search($keyword);

        return response()->json($results);
    }
}
