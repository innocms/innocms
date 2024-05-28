<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InnoCMS\Common\Models\Catalog;
use InnoCMS\Common\Repositories\ArticleRepo;
use InnoCMS\Common\Repositories\CatalogRepo;

class CatalogController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        return redirect()->to(route('articles.index'));
    }

    /**
     * @param  Catalog  $catalog
     * @return mixed
     * @throws \Exception
     */
    public function show(Catalog $catalog): mixed
    {
        $catalogs = CatalogRepo::getInstance()->list(['active' => true]);
        $articles = ArticleRepo::getInstance()->list(['active' => true, 'catalog_id' => $catalog->id]);

        $data = [
            'catalog'  => $catalog,
            'catalogs' => $catalogs,
            'articles' => $articles,
        ];

        return view('front::catalogs.show', $data);
    }

    /**
     * @param  Request  $request
     * @return mixed
     * @throws \Exception
     */
    public function slugShow(Request $request): mixed
    {
        $slug     = $request->slug;
        $catalog  = CatalogRepo::getInstance()->builder(['active' => true])->where('slug', $slug)->firstOrFail();
        $catalogs = CatalogRepo::getInstance()->list(['active' => true]);
        $articles = ArticleRepo::getInstance()->list(['active' => true, 'catalog_id' => $catalog->id]);

        $data = [
            'slug'     => $slug,
            'catalog'  => $catalog,
            'catalogs' => $catalogs,
            'articles' => $articles,
        ];

        return view('front::catalogs.show', $data);
    }
}
