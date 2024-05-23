<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Models;

use InnoCMS\Common\Traits\Translatable;

class Page extends BaseModel
{
    use Translatable;

    protected $fillable = [
        'slug', 'viewed', 'active',
    ];

    public $appends = [
        'slug_url',
    ];

    /**
     * Get slug url link.
     *
     * @return string
     */
    public function getSlugUrlAttribute(): string
    {
        return front_route('pages.'.$this->slug);
    }
}
