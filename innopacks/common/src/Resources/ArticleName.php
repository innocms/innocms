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

class ArticleName extends JsonResource
{
    /**
     * @param  Request  $request
     */
    public function toArray(Request $request): array
    {
        try {
            $url = (string) $this->url;
        } catch (Throwable) {
            $url = '';
        }

        $data = [
            'id'         => $this->id,
            'slug'       => $this->slug,
            'name'       => $this->fallbackName('title'),
            'url'        => $url,
            'image'      => image_resize($this->image, 88, 88),
            'active'     => (bool) $this->active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];

        return fire_hook_filter('resource.article.name', $data);
    }
}
