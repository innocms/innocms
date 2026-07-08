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
use InnoCMS\Common\Repositories\ProductRepo;
use InnoCMS\Common\Resources\ProductSimple;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;

#[Group('Panel - Products')]
class ProductController extends BaseController
{
    /**
     * List products / resolve a set of IDs.
     * The autocomplete-list picker initialises with `?tag_ids=1,2,3`
     * (the param name is a shared picker convention).
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection
     */
    #[Endpoint('List products')]
    #[QueryParam('tag_ids', 'string', required: false, description: 'Comma-separated product IDs (used by the related-product picker)')]
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->all();
        if (isset($filters['tag_ids'])) {
            $filters['product_ids'] = array_filter(explode(',', $filters['tag_ids']), 'is_numeric');
            unset($filters['tag_ids']);
        }

        $products = ProductRepo::getInstance()->builder($filters)->limit(20)->get();

        return ProductSimple::collection($products);
    }

    /**
     * Fuzzy search for the related-product picker.
     * /api/panel/products/autocomplete?keyword=xxx
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection
     */
    #[Endpoint('Autocomplete products')]
    #[QueryParam('keyword', 'string', required: false)]
    public function autocomplete(Request $request): AnonymousResourceCollection
    {
        $keyword  = $request->get('keyword');
        $products = ProductRepo::getInstance()->autocomplete($keyword, 20);

        return ProductSimple::collection($products);
    }
}
