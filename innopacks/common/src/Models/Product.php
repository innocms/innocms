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
use Illuminate\Database\Eloquent\Relations\HasMany;
use InnoCMS\Common\Models\Product\Relation;
use InnoCMS\Common\Traits\HasPackageFactory;
use InnoCMS\Common\Traits\Replicate;
use InnoCMS\Common\Traits\Translatable;
use Throwable;

class Product extends BaseModel
{
    use HasPackageFactory, Replicate, Translatable;

    public $timestamps = true;

    protected $fillable = [
        'slug', 'images', 'video', 'price', 'link', 'spu_code', 'position', 'viewed', 'active',
    ];

    protected $casts = [
        'images' => 'array',
        'video'  => 'json',
        'active' => 'boolean',
    ];

    protected $appends = ['image'];

    /**
     * @return HasMany
     */
    public function relations(): HasMany
    {
        return $this->hasMany(Relation::class, 'product_id');
    }

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }

    /**
     * @return BelongsToMany
     */
    public function relationProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_relations', 'product_id', 'relation_id');
    }

    /**
     * Cover image = first image in the images array.
     *
     * @return string
     */
    public function getImageAttribute(): string
    {
        $images = $this->images ?? [];

        return $images[0] ?? '';
    }

    /**
     * Get edit URL.
     *
     * @return string
     */
    public function getEditUrlAttribute(): string
    {
        return panel_route('products.edit', $this);
    }

    /**
     * Get product frontend URL.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        if ($this->slug) {
            return front_route('products.slug_show', ['slug' => $this->slug]);
        }

        return front_route('products.show', $this);
    }

    /**
     * Get cover image URL.
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        return $this->getImageUrl();
    }

    /**
     * @param  int  $width
     * @param  int  $height
     * @return string
     */
    public function getImageUrl(int $width = 600, int $height = 600): string
    {
        return image_resize($this->image ?? '', $width, $height);
    }

    /**
     * Storefront detail URL. Falls back to the product ID when the slug is empty,
     * so list/panel views never throw UrlGenerationException for slug-less products.
     *
     * @return string
     */
    public function getFrontUrlAttribute(): string
    {
        return $this->storefrontUrl();
    }

    /**
     * Alias for themes that use $product->url instead of $product->front_url.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return $this->storefrontUrl();
    }

    /**
     * Build the storefront detail URL with slug-or-ID fallback.
     *
     * @return string
     */
    private function storefrontUrl(): string
    {
        $slug = $this->slug ?: ((string) $this->id ?: '');

        try {
            if ($slug !== '') {
                return front_route('products.show', ['slug' => $slug], false);
            }
        } catch (Throwable) {
            // fallback: build URL directly to avoid RouteNotFoundException
            // when front_route() cannot resolve the locale-prefixed route name
        }

        if ($slug !== '') {
            $prefix = hide_url_locale() || locales()->isEmpty() ? '' : '/'.front_locale_code();

            return $prefix.'/product-'.$slug;
        }

        return '#';
    }
}
