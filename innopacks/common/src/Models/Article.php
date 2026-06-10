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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use InnoCMS\Common\Traits\Translatable;

class Article extends BaseModel
{
    use Translatable;

    protected $fillable = [
        'catalog_id', 'image', 'slug', 'position', 'viewed', 'author', 'active',
    ];

    public $appends = [
        'url',
    ];

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tags', 'article_id', 'tag_id');
    }

    /**
     * Get article title with fallback.
     */
    public function getTitleAttribute(): string
    {
        return $this->fallbackName('title');
    }

    /**
     * Get article content with fallback.
     */
    public function getContentAttribute(): string
    {
        return $this->fallbackName('content');
    }

    /**
     * Get article summary with fallback.
     */
    public function getSummaryAttribute(): string
    {
        return $this->fallbackName('summary');
    }

    /**
     * Get tag names.
     *
     * @return mixed
     */
    public function getTagNamesAttribute(): mixed
    {
        return $this->tags->map(fn ($tag) => $tag->fallbackName('name'))->implode(',');
    }

    /**
     * Get slug url link.
     *
     * @return string
     * @throws Exception
     */
    public function getUrlAttribute(): string
    {
        if ($this->slug) {
            return front_route('articles.slug_show', ['slug' => $this->slug]);
        }

        return front_route('articles.show', $this);
    }

    /**
     * Get article image with fallback logic.
     * Priority: current locale translation image -> main image
     */
    public function getImageAttribute(): string
    {
        $originalImage = $this->attributes['image'] ?? '';

        $translationImage = $this->fallbackName('image');

        return $translationImage ?: $originalImage;
    }
}
