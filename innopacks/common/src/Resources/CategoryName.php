<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class CategoryName extends JsonResource
{
    /**
     * @param  Request  $request
     */
    public function toArray(Request $request): array
    {
        try {
            $url = (string) ($this->resource?->url ?? '#');
        } catch (Throwable) {
            $url = '#';
        }

        $data = [
            'id'     => $this->id,
            'slug'   => $this->slug,
            'name'   => $this->fallbackName(),
            'url'    => $url,
            'image'  => image_resize($this->image ?? '', 88, 88),
            'active' => (bool) $this->active,
        ];

        return fire_hook_filter('resource.category.name', $data);
    }
}
