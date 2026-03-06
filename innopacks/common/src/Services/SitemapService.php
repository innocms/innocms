<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use InnoCMS\Common\Repositories\ArticleRepo;
use InnoCMS\Common\Repositories\CatalogRepo;
use InnoCMS\Common\Repositories\PageRepo;
use Spatie\Sitemap\Sitemap;
use Symfony\Component\HttpFoundation\Response;

class SitemapService extends BaseService
{
    private Sitemap $sitemap;

    public function __construct()
    {
        $this->sitemap = Sitemap::create();
    }

    /**
     * Render sitemap.xml
     *
     * @param  $request
     * @return Response
     * @throws Exception
     */
    public function response($request): Response
    {
        $locales = enabled_locale_codes();
        $this->sitemap->add(route('front.home.index'));

        foreach ($locales as $locale) {
            $this->addCatalogs($locale);
            $this->addArticles($locale);
            $this->addPages($locale);
        }

        return $this->sitemap->toResponse($request);
    }

    /**
     * @param  $locale
     * @return void
     * @throws Exception
     */
    private function addCatalogs($locale): void
    {
        $catalogs = CatalogRepo::getInstance()->withActive()->builder()->limit(1000)->get();
        foreach ($catalogs as $item) {
            if ($item->slug) {
                $url = $this->frontRoute($locale, 'catalogs.slug_show', ['slug' => $item->slug]);
            } else {
                $url = $this->frontRoute($locale, 'catalogs.show', $item);
            }
            $this->addUrl($url);
        }
    }

    /**
     * @param  $locale
     * @return void
     * @throws Exception
     */
    private function addArticles($locale): void
    {
        $articles = ArticleRepo::getInstance()->withActive()->builder()->limit(1000)->get();
        foreach ($articles as $item) {
            if ($item->slug) {
                $url = $this->frontRoute($locale, 'articles.slug_show', ['slug' => $item->slug]);
            } else {
                $url = $this->frontRoute($locale, 'articles.show', $item);
            }
            $this->addUrl($url);
        }
    }

    /**
     * @param  $locale
     * @return void
     * @throws Exception
     */
    private function addPages($locale): void
    {
        $pages = PageRepo::getInstance()->withActive()->builder()->limit(1000)->get();
        foreach ($pages as $item) {
            $url = $this->frontRoute($locale, 'pages.'.$item->slug);
            $this->addUrl($url);
        }
    }

    /**
     * @param  $locale
     * @param  $name
     * @param  mixed  $parameters
     * @return string
     * @throws Exception
     */
    private function frontRoute($locale, $name, mixed $parameters = []): string
    {
        try {
            if (hide_url_locale()) {
                return route('front.'.$name, $parameters);
            }

            return route($locale.'.front.'.$name, $parameters);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return '';
        }
    }

    /**
     * Add a URL to the sitemap
     *
     * @param  mixed  $url
     * @return void
     */
    private function addUrl(mixed $url): void
    {
        if (empty($url)) {
            return;
        }
        $this->sitemap->add($url);
    }
}
