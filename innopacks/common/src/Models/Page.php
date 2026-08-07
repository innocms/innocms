<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Models;

use Exception;
use InnoCMS\Common\Traits\Translatable;

class Page extends BaseModel
{
    use Translatable;

    protected $fillable = [
        'slug', 'viewed', 'show_breadcrumb', 'active',
    ];

    public $appends = [
        'url',
    ];

    /**
     * Get page title with fallback.
     */
    public function getTitleAttribute(): string
    {
        return $this->fallbackName('title');
    }

    /**
     * Get page content with fallback.
     */
    public function getContentAttribute(): string
    {
        return $this->fallbackName('content');
    }

    /**
     * Get slug url link.
     *
     * @return string
     * @throws Exception
     */
    public function getUrlAttribute(): string
    {
        try {
            if ($this->slug) {
                return front_route('pages.slug_show', ['slug' => $this->slug]);
            }

            return front_route('pages.show', $this);
        } catch (Exception $e) {
            return '';
        }
    }
}
