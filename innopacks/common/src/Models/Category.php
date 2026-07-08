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
use Illuminate\Database\Eloquent\Relations\HasMany;
use InnoCMS\Common\Traits\HasPackageFactory;
use InnoCMS\Common\Traits\Translatable;

class Category extends BaseModel
{
    use HasPackageFactory, Translatable;

    protected $fillable = ['parent_id', 'slug', 'image', 'position', 'active'];

    /**
     * Model validation rules to prevent circular references.
     */
    protected static function booted(): void
    {
        static::saving(function ($category) {
            // Parent cannot be itself
            if ($category->parent_id && $category->parent_id == $category->id) {
                throw new Exception(trans('panel/category.parent_self'));
            }

            // Detect circular references up the ancestor chain. Depth-capped to
            // bound work on pathological trees; the in-array check also breaks cycles.
            if ($category->parent_id && $category->parent_id > 0) {
                $visited       = [$category->id];
                $currentParent = self::find($category->parent_id);
                $maxDepth      = 100;
                $depth         = 0;

                while ($currentParent && $depth < $maxDepth) {
                    if (in_array($currentParent->id, $visited)) {
                        throw new Exception(trans('panel/category.circular_reference'));
                    }
                    $visited[]     = $currentParent->id;
                    $currentParent = $currentParent->parent_id
                        ? self::find($currentParent->parent_id)
                        : null;
                    $depth++;
                }
            }
        });
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    /**
     * Get all children.
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    /**
     * Get active children.
     *
     * @return HasMany
     */
    public function activeChildren(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')
            ->where('active', 1)
            ->withCount('products');
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_categories', 'category_id', 'product_id');
    }

    /**
     * Get edit URL.
     *
     * @return string
     */
    public function getEditUrlAttribute(): string
    {
        return panel_route('categories.edit', $this);
    }

    /**
     * Get image URL.
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
     * Get product count. Falls back to a live count() query when
     * withCount('products') was not preloaded.
     *
     * @return int
     */
    public function getProductsCountAttribute(): int
    {
        if (array_key_exists('products_count', $this->attributes)) {
            return (int) $this->attributes['products_count'];
        }

        return $this->products()->count();
    }
}
