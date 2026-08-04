<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use InnoCMS\Common\Traits\Translatable;
use Throwable;

class Tag extends BaseModel
{
    use Translatable;

    protected $fillable = [
        'slug', 'position', 'active',
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_tags', 'tag_id', 'article_id');
    }

    public function getNameAttribute(): string
    {
        return $this->fallbackName('name');
    }

    /**
     * Get tag url link. Tags only expose /tags-{slug}, so a missing slug
     * degrades to '#' instead of throwing UrlGenerationException.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        try {
            if ($this->slug) {
                return front_route('tags.show', ['slug' => $this->slug], false);
            }

            return '#';
        } catch (Throwable) {
            return '#';
        }
    }
}
