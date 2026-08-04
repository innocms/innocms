<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace InnoCMS\Common\Support;

use InnoCMS\Common\Models\Category;
use Throwable;

/**
 * InnoLinkPicker entity-link: parse stored values and build storefront URLs.
 *
 * Flow: {@see normalize()} → {@see EntityLinkEnricher::enrichRow()} (DB, when needed) → {@see urlFromRow()} /
 * {@see urlFromStored()}. Storefront uses the same request locale as product/category pages (front middleware sets app + session).
 * Full row for themes: {@see forDisplay()}. Helpers: {@see entity_link_normalize()},
 * {@see entity_link_enrich()}, {@see entity_link_display()}, {@see entity_link_url()}.
 */
final class EntityLinkPayload
{
    /**
     * @return array{type: string, value: string, entity_label: string, link: string, entity_image: string, entity_price: string}
     */
    public static function defaults(): array
    {
        return [
            'type'         => 'page',
            'value'        => '',
            'entity_label' => '',
            'link'         => '',
            'entity_image' => '',
            'entity_price' => '',
        ];
    }

    /**
     * @param  array{type?: string, value?: mixed, entity_label?: string, link?: string, entity_image?: string, entity_price?: string}  $row
     * @param  array{type: string, value: string, entity_label: string, link: string, entity_image: string, entity_price: string}  $defaults
     * @return array{type: string, value: string, entity_label: string, link: string, entity_image: string, entity_price: string}
     */
    public static function mergeAndCast(array $row, array $defaults): array
    {
        $row                 = array_merge($defaults, $row);
        $row['type']         = (string) ($row['type'] ?? 'page');
        $row['entity_label'] = (string) ($row['entity_label'] ?? '');
        $row['link']         = (string) ($row['link'] ?? '');
        $row['entity_image'] = (string) ($row['entity_image'] ?? '');
        $row['entity_price'] = (string) ($row['entity_price'] ?? '');
        $v                   = $row['value'] ?? '';
        $row['value']        = ($v !== null && $v !== '') ? (string) $v : '';

        return $row;
    }

    /**
     * Normalize stored value to row shape (no DB enrichment). Same rules as panel InnoLinkPicker parse.
     *
     * @param  array<string, mixed>|string|null  $stored
     * @return array{type: string, value: string, entity_label: string, link: string, entity_image: string, entity_price: string}
     */
    public static function normalize(array|string|null $stored): array
    {
        $defaults = self::defaults();

        if ($stored === null || $stored === '') {
            return self::mergeAndCast([], $defaults);
        }

        if (is_array($stored)) {
            return self::mergeAndCast(array_merge($defaults, array_intersect_key($stored, $defaults)), $defaults);
        }

        $trim = trim((string) $stored);
        if (str_starts_with($trim, '{')) {
            $j = json_decode($trim, true);
            if (is_array($j)) {
                return self::mergeAndCast(array_merge($defaults, array_intersect_key($j, $defaults)), $defaults);
            }
        }

        if (preg_match('/^product:([\w-]+)/', $trim, $m)) {
            return self::mergeAndCast(array_merge($defaults, ['type' => 'product', 'value' => (string) $m[1]]), $defaults);
        }

        if (preg_match('/^category:([\w-]+)/', $trim, $m)) {
            return self::mergeAndCast(array_merge($defaults, ['type' => 'category', 'value' => (string) $m[1]]), $defaults);
        }

        if ($trim !== '') {
            return self::mergeAndCast(array_merge($defaults, ['type' => 'custom', 'link' => $trim]), $defaults);
        }

        return self::mergeAndCast([], $defaults);
    }

    /**
     * Storefront URL from an already-normalized row (same shape as {@see normalize()}).
     *
     * @param  array{type?: string, value?: string, link?: string, ...}  $row
     */
    public static function urlFromRow(array $row): string
    {
        $type  = strtolower((string) ($row['type'] ?? ''));
        $value = (string) ($row['value'] ?? '');
        $link  = trim((string) ($row['link'] ?? ''));

        // Custom links keep their raw URL (internal or external) as entered.
        if ($type === 'custom') {
            return $link !== '' ? $link : ($value !== '' ? $value : '#');
        }

        // For entity links an explicit external URL override wins; an internal
        // cached link is ignored so the URL is regenerated from the current route
        // (keeps slugs/format current and avoids environment-bound absolute URLs).
        if ($link !== '' && self::isExternalUrl($link)) {
            return $link;
        }

        if ($value === '') {
            return '#';
        }

        $canonical = EntityLinkEnricher::storefrontUrlForEntity($type, $value);
        if ($canonical !== null && $canonical !== '') {
            return $canonical;
        }

        try {
            return match ($type) {
                'product'  => self::entityUrl('products.show', 'product', $value),
                'category' => self::categoryUrl($value),
                'brand'    => front_route('brands.show', ['brand' => $value], false),
                'page'     => front_route('pages.show', ['page' => $value], false),
                'article'  => front_route('articles.show', ['article' => $value], false),
                'catalog'  => front_route('catalogs.show', ['catalog' => $value], false),
                default    => '#',
            };
        } catch (Throwable) {
            return '#';
        }
    }

    /**
     * Resolve a storefront URL for an entity route, falling back to a direct
     * path build when front_route() cannot resolve the locale-prefixed route name.
     *
     * @param  string  $route  Route name (e.g. 'products.show')
     * @param  string  $prefix  URL path segment (e.g. 'product')
     * @param  string  $value  Slug or ID value
     * @return string
     */
    private static function entityUrl(string $route, string $prefix, string $value): string
    {
        try {
            return front_route($route, ['slug' => $value], false);
        } catch (Throwable) {
            // fallback: build URL directly
        }

        $localePrefix = hide_url_locale() || locales()->isEmpty() ? '' : '/'.front_locale_code();

        return $localePrefix.'/'.$prefix.'-'.$value;
    }

    /**
     * Category storefront = product list filtered by ?cat=. Relative path.
     *
     * @param  string  $value  Category ID or slug
     */
    private static function categoryUrl(string $value): string
    {
        try {
            $resolved = EntityLinkEnricher::resolveByIdOrSlug(Category::class, $value);
            $cat      = $resolved ? ((string) ($resolved->slug ?: $resolved->id)) : $value;
            if ($cat === '') {
                return '#';
            }

            return front_route('products.index', [], false).'?cat='.urlencode($cat);
        } catch (Throwable) {
            return '#';
        }
    }

    /**
     * Whether a URL points off-site (host differs from app/request host).
     */
    private static function isExternalUrl(string $url): bool
    {
        if ($url === '' || $url[0] === '/') {
            return false;
        }
        $host = parse_url($url, PHP_URL_HOST);
        if (! $host) {
            return false;
        }
        $appHost = (string) (parse_url((string) config('app.url'), PHP_URL_HOST) ?? '');
        $reqHost = (string) (request()?->getHost() ?? '');
        if ($appHost !== '' && strcasecmp($host, $appHost) === 0) {
            return false;
        }
        if ($reqHost !== '' && strcasecmp($host, $reqHost) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Storefront URL: parse stored value then {@see urlFromRow()}.
     *
     * @param  array<string, mixed>|string|null  $stored
     */
    public static function urlFromStored(array|string|null $stored): string
    {
        if ($stored === null) {
            return '';
        }
        if (is_string($stored) && trim($stored) === '') {
            return '';
        }

        return self::urlFromRow(self::normalize($stored));
    }

    /**
     * Normalize + DB enrichment + resolved storefront href (for slideshow, menus, theme partials).
     *
     * @param  array<string, mixed>|string|null  $stored
     * @return array{type: string, value: string, entity_label: string, link: string, entity_image: string, entity_price: string, entity_href: string}
     */
    public static function forDisplay(array|string|null $stored): array
    {
        $row                = self::normalize($stored);
        $row                = EntityLinkEnricher::enrichRow($row);
        $row['entity_href'] = self::urlFromRow($row);

        return $row;
    }
}
