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
use Illuminate\Support\Facades\Blade;
use InnoCMS\Common\Models\Page;
use InnoCMS\Common\Models\Product;
use InnoCMS\Common\Repositories\PageRepo;

class PageController extends Controller
{
    /**
     * Page list.
     */
    public function index(): mixed
    {
        $pages = PageRepo::getInstance()->withActive()->builder()->get();

        return inno_view('pages.index', ['pages' => $pages]);
    }

    /**
     * Show page by slug via /page-{slug} route pattern.
     *
     * @param  Request  $request
     * @return mixed
     * @throws \Exception
     */
    public function slugShow(Request $request): mixed
    {
        $slug = $request->slug;
        $page = PageRepo::getInstance()
            ->builder(['slug' => $slug, 'active' => true])
            ->firstOrFail();

        return $this->renderPage($page);
    }

    /**
     * Show page by ID.
     *
     * @param  Request  $request
     * @return mixed
     * @throws \Exception
     */
    public function show(Request $request): mixed
    {
        $slug    = str_replace(['/', '.html'], '', $request->getRequestUri());
        $filters = [
            'slug'   => $slug,
            'active' => true,
        ];
        $page = PageRepo::getInstance()->builder($filters)->firstOrFail();

        return $this->renderPage($page);
    }

    /**
     * Render page: theme blade > template field > content field.
     *
     * Pages in $forceStandardLayout skip theme overrides and always use
     * pages.show so they get the shared.page-head + .content typography.
     *
     * @param  Page  $page
     * @return mixed
     * @throws \Exception
     */
    private function renderPage(Page $page): mixed
    {
        if (! $page->active) {
            abort(404);
        }

        $page->increment('viewed');
        $slug = $page->slug;

        $forceStandard = ['about'];

        // Theme has a slug-specific blade → use it directly (unless forced to standard)
        if (view()->exists("pages.$slug") && ! in_array($slug, $forceStandard, true)) {
            return inno_view("pages.$slug", ['page' => $page]);
        }

        // Fallback: backend template field (Blade code) or content (rich text)
        $data = [
            'slug' => $slug,
            'page' => $page,
        ];
        $template = $page->translation?->template ?? '';
        if ($template) {
            $result         = Blade::render($template, $data);
            $data['result'] = $result;
        }

        return inno_view('pages.show', $data);
    }

    /**
     * Official service demo pages.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function showOfficialDemoPage(Request $request): mixed
    {
        $slug = $request->slug;
        switch ($slug) {
            case 'services':
                return inno_view('front::pages._sample_services');
            case 'about':
                return inno_view('front::pages._sample_about');
            case 'products':
                $products = Product::query()
                    ->with(['translations', 'categories'])
                    ->where('active', true)
                    ->whereHas('categories', function ($q) {
                        $q->where('slug', 'software-products');
                    })
                    ->orderBy('position')
                    ->get();

                return inno_view('front::pages._sample_products', ['products' => $products]);
            default:
                return redirect()->route('home.index');
        }
    }
}
