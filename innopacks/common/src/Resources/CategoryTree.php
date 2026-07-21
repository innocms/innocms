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

class CategoryTree extends JsonResource
{
    /**
     * Transform the category into a nested array (with children) for the
     * panel tree list. Kept separate from CategorySimple so the REST API
     * picker (flat {id,name}) stays unaffected.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->fallbackName(),
            'active'   => (bool) $this->active,
            'children' => self::collection($this->children)->jsonSerialize(),
        ];
    }
}
