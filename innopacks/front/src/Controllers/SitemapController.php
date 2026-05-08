<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innocms.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use InnoCMS\Common\Repositories\ArticleRepo;
use InnoCMS\Common\Repositories\CatalogRepo;
use InnoCMS\Common\Repositories\PageRepo;
use InnoCMS\Common\Repositories\TagRepo;

class SitemapController
{
    /**
     * Generate sitemap.xml response.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            $urls = [];

            $locales = enabled_locale_codes();

            foreach ($locales as $locale) {
                $this->addLocaleUrls($urls, $locale);
            }

            // If no locales, add default URLs
            if (empty($locales)) {
                $this->addLocaleUrls($urls, '');
            }

            $xml = $this->buildXml($urls);

            return response($xml, 200, ['Content-Type' => 'text/xml']);
        } catch (Exception $e) {
            Log::error('Sitemap generation error: '.$e->getMessage());

            return response($e->getMessage(), 500);
        }
    }

    /**
     * Add all URLs for a given locale.
     */
    private function addLocaleUrls(array &$urls, string $locale): void
    {
        // Homepage
        $this->addUrl($urls, $this->localeRoute($locale, 'home.index'));

        // Catalogs list
        $this->addUrl($urls, $this->localeRoute($locale, 'catalogs.index'));

        // Catalogs
        $catalogs = CatalogRepo::getInstance()->withActive()->builder()->limit(1000)->get();
        foreach ($catalogs as $item) {
            if ($item->slug) {
                $url = $this->localeRoute($locale, 'catalogs.slug_show', ['slug' => $item->slug]);
            } else {
                $url = $this->localeRoute($locale, 'catalogs.show', $item);
            }
            $this->addUrl($urls, $url, $item->updated_at?->format('Y-m-d'));
        }

        // Articles list
        $this->addUrl($urls, $this->localeRoute($locale, 'articles.index'));

        // Articles
        $articles = ArticleRepo::getInstance()->builder()->where('active', true)->limit(1000)->get();
        foreach ($articles as $item) {
            if ($item->slug) {
                $url = $this->localeRoute($locale, 'articles.slug_show', ['slug' => $item->slug]);
            } else {
                $url = $this->localeRoute($locale, 'articles.show', $item);
            }
            $this->addUrl($urls, $url, $item->updated_at?->format('Y-m-d'));
        }

        // Tags list
        $this->addUrl($urls, $this->localeRoute($locale, 'tags.index'));

        // Tags
        $tags = TagRepo::getInstance()->withActive()->builder()->limit(1000)->get();
        foreach ($tags as $item) {
            if ($item->slug) {
                $url = $this->localeRoute($locale, 'tags.show', ['slug' => $item->slug]);
                $this->addUrl($urls, $url, $item->updated_at?->format('Y-m-d'));
            }
        }

        // Pages
        $pages = PageRepo::getInstance()->withActive()->builder()->limit(1000)->get();
        foreach ($pages as $item) {
            if ($item->slug) {
                $url = $this->localeRoute($locale, 'pages.slug_show', ['slug' => $item->slug]);
                $this->addUrl($urls, $url, $item->updated_at?->format('Y-m-d'));
            }
        }
    }

    /**
     * Generate a locale-aware front route URL.
     */
    private function localeRoute(string $locale, string $name, mixed $parameters = []): string
    {
        try {
            if (empty($locale) || hide_url_locale()) {
                return route('front.'.$name, $parameters);
            }

            return route($locale.'.front.'.$name, $parameters);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return '';
        }
    }

    /**
     * Add a URL entry to the list if valid.
     */
    private function addUrl(array &$urls, string $url, ?string $lastmod = null): void
    {
        if (empty($url)) {
            return;
        }
        $urls[] = ['loc' => $url, 'lastmod' => $lastmod];
    }

    /**
     * Build the sitemap XML string.
     */
    private function buildXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '  <url>'."\n";
            $xml .= '    <loc>'.htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8').'</loc>'."\n";
            if ($url['lastmod']) {
                $xml .= '    <lastmod>'.$url['lastmod'].'</lastmod>'."\n";
            }
            $xml .= '  </url>'."\n";
        }

        $xml .= '</urlset>'."\n";

        return $xml;
    }
}
