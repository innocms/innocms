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

use InnoCMS\Common\Models\Article;
use InnoCMS\Common\Models\Catalog;
use InnoCMS\Common\Models\Page;
use Throwable;

/**
 * Fills entity_label / entity_image / entity_price from DB.
 *
 * @see entity_link_enrich()
 * @see entity_link_resolve()
 */
final class EntityLinkEnricher
{
    /**
     * @param  array{type: string, value: string, entity_label: string, link: string, entity_image: string, entity_price: string}  $row
     * @return array{type: string, value: string, entity_label: string, link: string, entity_image: string, entity_price: string}
     */
    public static function enrichRow(array $row): array
    {
        $type  = (string) ($row['type'] ?? '');
        $value = (string) ($row['value'] ?? '');
        if ($value === '' || $type === '' || $type === 'custom') {
            return $row;
        }

        $needImage = ($row['entity_image'] ?? '') === '';

        try {
            switch ($type) {
                case 'page':
                    $page = self::resolveByIdOrSlug(Page::class, $value, ['translation']);
                    if ($page instanceof Page && $page->translation) {
                        $row['entity_label'] = (string) $page->translation->title;
                    }
                    break;

                case 'article':
                    $article = self::resolveByIdOrSlug(Article::class, $value, ['translation']);
                    if ($article instanceof Article) {
                        $row['entity_label'] = $article->fallbackName('title');
                        if ($needImage && $article->image) {
                            $row['entity_image'] = (string) image_resize($article->image, 100, 100);
                        }
                    }
                    break;

                case 'catalog':
                    $catalog = self::resolveByIdOrSlug(Catalog::class, $value, ['translation']);
                    if ($catalog instanceof Catalog) {
                        $row['entity_label'] = $catalog->fallbackName('title');
                    }
                    break;
            }
        } catch (Throwable) {
            return $row;
        }

        return $row;
    }

    /**
     * @param  class-string  $modelClass
     */
    public static function resolveByIdOrSlug(string $modelClass, string $value, array $with = []): ?object
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        $query = $modelClass::query()->with($with);
        if (ctype_digit($value)) {
            return $query->find((int) $value);
        }

        return $query->where('slug', $value)->first();
    }

    /**
     * Storefront URL for CMS entities.
     *
     * @return string|null null if entity not found
     */
    public static function storefrontUrlForEntity(string $type, string $value): ?string
    {
        $type  = strtolower($type);
        $value = trim($value);
        if ($value === '' || $type === '' || $type === 'custom') {
            return null;
        }

        $model = match ($type) {
            'page'    => self::resolveByIdOrSlug(Page::class, $value, []),
            'article' => self::resolveByIdOrSlug(Article::class, $value, []),
            'catalog' => self::resolveByIdOrSlug(Catalog::class, $value, []),
            default   => null,
        };

        if ($model === null) {
            return null;
        }

        try {
            $url = $model->url ?? '';

            return is_string($url) && $url !== '' ? $url : null;
        } catch (Throwable) {
            return null;
        }
    }
}
