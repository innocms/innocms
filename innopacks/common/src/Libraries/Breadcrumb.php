<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Libraries;

class Breadcrumb
{
    public static function getInstance(): Breadcrumb
    {
        return new self;
    }

    /**
     * Build a single breadcrumb trail entry by type.
     *
     * @param  string  $type  article|catalog|tag|page|route|static
     * @param  mixed  $object  model instance, route name, or url
     * @param  string  $title  override title (required by route/static)
     * @return array
     */
    public function getTrail(string $type, $object, string $title = ''): array
    {
        if (in_array($type, ['catalog', 'article', 'page'])) {
            return [
                'title' => $object->fallbackName('title'),
                'url'   => $object->url,
            ];
        } elseif ($type == 'tag') {
            return [
                'title' => $object->fallbackName('name'),
                'url'   => $object->url,
            ];
        } elseif ($type == 'route') {
            return [
                'title' => $title,
                'url'   => front_route($object),
            ];
        } elseif ($type == 'static') {
            return [
                'title' => $title,
                'url'   => $object,
            ];
        }

        return [];
    }
}
