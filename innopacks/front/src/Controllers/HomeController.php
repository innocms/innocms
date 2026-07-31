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
use Illuminate\Support\Collection;
use InnoCMS\Common\Models\Article;
use InnoCMS\Common\Models\Catalog;

class HomeController extends Controller
{
    /**
     * Home page for the default B2B theme.
     *
     * Data is resolved from demo catalogs by slug (products / industries / news),
     * so the page degrades gracefully if the demo content is removed.
     *
     * @return mixed
     * @throws \Exception
     */
    public function index(): mixed
    {
        $data = [
            'serviceCatalogs'  => $this->serviceCatalogs(),
            'featuredProducts' => $this->catalogArticles('products', 4),
            'industryArticles' => $this->industryArticles(),
            'latestNews'       => $this->latestNews(3),
            'productsCatalog'  => $this->catalogBySlug('products'),
            'newsCatalog'      => $this->catalogBySlug('news'),
        ];

        $data = fire_hook_filter('home.index.data', $data);

        return inno_view('home', $data);
    }

    private function catalogBySlug(string $slug): ?Catalog
    {
        return Catalog::query()
            ->where('slug', $slug)
            ->where('active', true)
            ->first();
    }

    /**
     * Children of the "products" catalog, used as service cards.
     */
    private function serviceCatalogs(): Collection
    {
        $products = $this->catalogBySlug('products');
        if (! $products) {
            return collect();
        }

        return Catalog::query()
            ->with('translation')
            ->where('parent_id', $products->id)
            ->where('active', true)
            ->orderBy('position')
            ->get();
    }

    /**
     * Articles assigned directly to the given catalog (featured items).
     */
    private function catalogArticles(string $slug, int $limit): Collection
    {
        $catalog = $this->catalogBySlug($slug);
        if (! $catalog) {
            return collect();
        }

        return Article::query()
            ->with('translation')
            ->where('catalog_id', $catalog->id)
            ->where('active', true)
            ->orderBy('position')
            ->take($limit)
            ->get();
    }

    /**
     * Representative articles for the children of the "industries" catalog.
     */
    private function industryArticles(): Collection
    {
        $industries = $this->catalogBySlug('industries');
        if (! $industries) {
            return collect();
        }

        $childIds = Catalog::query()
            ->where('parent_id', $industries->id)
            ->where('active', true)
            ->pluck('id');

        if ($childIds->isEmpty()) {
            return collect();
        }

        return Article::query()
            ->with(['translation', 'catalog.translation'])
            ->whereIn('catalog_id', $childIds)
            ->where('active', true)
            ->orderBy('position')
            ->get();
    }

    /**
     * Latest articles under the "resources" catalog and its children.
     */
    private function latestNews(int $limit): Collection
    {
        $resources = $this->catalogBySlug('resources');
        if (! $resources) {
            return collect();
        }

        $childIds = Catalog::query()
            ->where('parent_id', $resources->id)
            ->pluck('id')
            ->push($resources->id);

        return Article::query()
            ->with(['translation', 'catalog.translation'])
            ->whereIn('catalog_id', $childIds)
            ->where('active', true)
            ->orderByDesc('id')
            ->take($limit)
            ->get();
    }
}
