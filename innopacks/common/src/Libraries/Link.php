<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Libraries;

use Exception;
use Illuminate\Support\Str;
use InnoCMS\Common\Models\Article;
use InnoCMS\Common\Models\Catalog;
use InnoCMS\Common\Models\Category;
use InnoCMS\Common\Models\Page;
use InnoCMS\Common\Models\Product;
use InnoCMS\Common\Models\Tag;

class Link
{
    public const TYPES = [
        'product', 'category', 'page', 'catalog', 'article', 'tag', 'static', 'custom',
    ];

    public static function getInstance(): self
    {
        return new self;
    }

    /**
     * Handle link.
     *
     * @throws Exception
     */
    public function link($type, $value): mixed
    {
        if (empty($type) || empty($value) || ! in_array($type, self::TYPES)) {
            return '';
        }

        if (is_array($value)) {
            throw new Exception('Value must be integer, string or object');
        }

        if ($type == 'product') {
            if (! $value instanceof Product) {
                $value = Product::query()->find($value);
            }

            return $value->url ?? '';
        } elseif ($type == 'category') {
            if (! $value instanceof Category) {
                $value = Category::query()->find($value);
            }

            return $value->url ?? '';
        } elseif ($type == 'page') {
            if (! $value instanceof Page) {
                $value = Page::query()->find($value);
            }

            return $value->url ?? '';
        } elseif ($type == 'catalog') {
            if (! $value instanceof Catalog) {
                $value = Catalog::query()->find($value);
            }

            return $value->url ?? '';
        } elseif ($type == 'article') {
            if (! $value instanceof Article) {
                $value = Article::query()->find($value);
            }

            return $value->url ?? '';
        } elseif ($type == 'tag') {
            if (! $value instanceof Tag) {
                $value = Tag::query()->find($value);
            }

            return $value->url ?? '';
        } elseif ($type == 'static') {
            return front_route($value);
        } elseif ($type == 'custom') {
            if (Str::startsWith($value, ['http://', 'https://'])) {
                return $value;
            }

            return "//{$value}";
        }

        return '';
    }
}
