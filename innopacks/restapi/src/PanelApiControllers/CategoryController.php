<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\RestAPI\PanelApiControllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use InnoCMS\Common\Repositories\CategoryRepo;
use InnoCMS\Common\Resources\CategorySimple;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;

#[Group('Panel - Product Categories')]
class CategoryController extends BaseController
{
    /**
     * List categories / resolve a set of IDs.
     * The autocomplete-list picker initialises with `?tag_ids=1,2,3`
     * (the param name is a shared picker convention).
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection
     */
    #[Endpoint('List product categories')]
    #[QueryParam('tag_ids', 'string', required: false, description: 'Comma-separated category IDs (used by the category picker)')]
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->all();
        if (isset($filters['tag_ids'])) {
            $filters['category_ids'] = array_filter(explode(',', $filters['tag_ids']), 'is_numeric');
            unset($filters['tag_ids']);
        }

        $categories = CategoryRepo::getInstance()->builder($filters)->limit(50)->get();

        return CategorySimple::collection($categories);
    }

    /**
     * Fuzzy search for the category picker.
     * /api/panel/categories/autocomplete?keyword=xxx
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection
     */
    #[Endpoint('Autocomplete product categories')]
    #[QueryParam('keyword', 'string', required: false)]
    public function autocomplete(Request $request): AnonymousResourceCollection
    {
        $keyword    = $request->get('keyword');
        $categories = CategoryRepo::getInstance()->autocomplete($keyword, 50);

        return CategorySimple::collection($categories);
    }
}
