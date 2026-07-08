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
}
