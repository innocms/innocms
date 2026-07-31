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
use Illuminate\Http\Request;
use InnoCMS\Common\Models\Article;
use InnoCMS\Common\Models\Catalog;
use InnoCMS\Common\Repositories\ArticleRepo;
use InnoCMS\Common\Repositories\CatalogRepo;
use InnoCMS\Common\Repositories\TagRepo;

class ArticleController extends Controller
{
    /**
     * @param  Request  $request
     * @return mixed
     * @throws \Exception
     */
    public function index(Request $request): mixed
    {
        $keyword        = $request->get('keyword');
        $sidebarCatalog = $this->newsCatalog();
        $catalogIds     = $sidebarCatalog
            ? $sidebarCatalog->children->pluck('id')->push($sidebarCatalog->id)->all()
            : null;

        $filters = ['active' => true, 'keyword' => $keyword];
        if ($catalogIds) {
            $filters['catalog_ids'] = $catalogIds;
        }

        $data = [
            'articles'       => ArticleRepo::getInstance()->list($filters),
            'catalogs'       => CatalogRepo::getInstance()->list(['active' => true]),
            'tags'           => TagRepo::getInstance()->list(['active' => true]),
            'sidebarCatalog' => $sidebarCatalog,
        ];

        return inno_view('articles.index', $data);
    }

    /**
     * @param  Article  $article
     * @return mixed
     * @throws \Exception
     */
    public function show(Article $article): mixed
    {
        abort_unless((bool) $article->active, 404);
        $article->increment('viewed');
        $data = [
            'article'        => $article,
            'catalogs'       => CatalogRepo::getInstance()->list(['active' => true]),
            'tags'           => TagRepo::getInstance()->list(['active' => true]),
            'sidebarHot'     => ArticleRepo::getInstance()->list(['active' => true])->sortByDesc('viewed')->take(5),
            'sidebarCatalog' => $this->newsCatalog(),
        ];

        return inno_view('articles.show', $data);
    }

    /**
     * @param  Request  $request
     * @return mixed
     * @throws \Exception
     */
    public function slugShow(Request $request): mixed
    {
        $slug    = $request->slug;
        $article = ArticleRepo::getInstance()->builder(['active' => true])->where('slug', $slug)->firstOrFail();
        $article->increment('viewed');
        $data = [
            'slug'           => $slug,
            'article'        => $article,
            'catalogs'       => CatalogRepo::getInstance()->list(['active' => true]),
            'tags'           => TagRepo::getInstance()->list(['active' => true]),
            'sidebarHot'     => ArticleRepo::getInstance()->list(['active' => true])->sortByDesc('viewed')->take(5),
            'sidebarCatalog' => $this->newsCatalog(),
        ];

        return inno_view('articles.show', $data);
    }

    /**
     * The "news" catalog drives the global articles listing and its sidebar.
     * Returns null when the demo catalog is absent so the views degrade gracefully.
     */
    private function newsCatalog(): ?Catalog
    {
        return Catalog::query()
            ->with(['translation', 'children.translation'])
            ->where('slug', 'news')
            ->where('active', true)
            ->first();
    }
}
