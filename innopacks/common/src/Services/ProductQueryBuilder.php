<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use InnoCMS\Common\Models\Category;
use InnoCMS\Common\Repositories\CategoryRepo;

/**
 * Product query builder.
 * Builds Eloquent queries from filter conditions. Display-only scope: no
 * attribute / brand / stock / sku filters (those belong to e-commerce).
 */
class ProductQueryBuilder
{
    /**
     * Apply category filters.
     *
     * @param  Builder  $builder
     * @param  array  $filters
     * @return Builder
     */
    public function applyCategoryFilters(Builder $builder, array $filters): Builder
    {
        $typeCategoryIds     = [];
        $specificCategoryIds = [];

        // Parent slug filter (filter by a parent category, including its children)
        $parentSlug = $filters['parent_slug'] ?? '';
        if ($parentSlug) {
            $parentCategory = Category::query()->where('slug', $parentSlug)->first();
            if ($parentCategory) {
                $childCategories = CategoryRepo::getInstance()->builder(['parent_id' => $parentCategory->id])->get();
                $typeCategoryIds = array_merge($typeCategoryIds, $childCategories->pluck('id')->toArray());
                $typeCategoryIds[] = $parentCategory->id;
            }
        }

        // Single category ID filter
        $categoryId = $filters['category_id'] ?? 0;
        if ($categoryId) {
            $specificCategoryIds[] = $categoryId;
        }

        // Category slug filter
        $categorySlug = $filters['category_slug'] ?? '';
        if ($categorySlug) {
            $category = Category::query()->where('slug', $categorySlug)->first();
            if ($category) {
                $categories          = CategoryRepo::getInstance()->builder(['parent_id' => $category->id])->get();
                $childCategoryIds    = $categories->pluck('id')->toArray();
                $specificCategoryIds = array_merge($specificCategoryIds, $childCategoryIds);
                $specificCategoryIds[] = $category->id;
            }
        }

        // Multiple category IDs filter
        $filterCategoryIds = $filters['category_ids'] ?? [];
        if ($filterCategoryIds instanceof Collection) {
            $filterCategoryIds = $filterCategoryIds->toArray();
        }
        if ($filterCategoryIds) {
            $specificCategoryIds = array_merge($specificCategoryIds, $filterCategoryIds);
        }

        // If both type and specific categories exist, intersect (AND); otherwise union (OR)
        if (! empty($typeCategoryIds) && ! empty($specificCategoryIds)) {
            $intersectionIds = array_intersect($typeCategoryIds, $specificCategoryIds);
            if (! empty($intersectionIds)) {
                $builder->whereHas('categories', function (Builder $query) use ($intersectionIds) {
                    $query->whereIn('category_id', $intersectionIds);
                });
            } else {
                $builder->whereRaw('1 = 0');
            }
        } elseif (! empty($typeCategoryIds)) {
            $builder->whereHas('categories', function (Builder $query) use ($typeCategoryIds) {
                $query->whereIn('category_id', $typeCategoryIds);
            });
        } elseif (! empty($specificCategoryIds)) {
            $specificCategoryIds = array_unique($specificCategoryIds);
            $builder->whereHas('categories', function (Builder $query) use ($specificCategoryIds) {
                $query->whereIn('category_id', $specificCategoryIds);
            });
        }

        return $builder;
    }

    /**
     * Apply price filters against the main products.price column.
     *
     * @param  Builder  $builder
     * @param  array  $filters
     * @return Builder
     */
    public function applyPriceFilters(Builder $builder, array $filters): Builder
    {
        $priceStart = $filters['price_start'] ?? '';
        if ($priceStart !== '') {
            $builder->where('price', '>=', $priceStart);
        }

        $priceEnd = $filters['price_end'] ?? '';
        if ($priceEnd !== '') {
            $builder->where('price', '<=', $priceEnd);
        }

        return $builder;
    }

    /**
     * Apply search filters.
     *
     * @param  Builder  $builder
     * @param  array  $filters
     * @return Builder
     */
    public function applySearchFilters(Builder $builder, array $filters): Builder
    {
        $keyword     = $filters['keyword'] ?? $filters['search'] ?? '';
        $searchField = $filters['search_field'] ?? '';

        if ($keyword) {
            if ($searchField === 'name') {
                $builder->whereHas('translations', function (Builder $query) use ($keyword) {
                    $query->where('name', 'like', "%$keyword%");
                });
            } elseif ($searchField === 'spu_code') {
                $builder->where('spu_code', 'like', "%$keyword%");
            } else {
                $builder->where(function ($query) use ($keyword) {
                    $query->whereHas('translations', function (Builder $q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    })->orWhere('spu_code', 'like', "%$keyword%");
                });
            }
        }

        return $builder;
    }

    /**
     * Apply basic filters.
     *
     * @param  Builder  $builder
     * @param  array  $filters
     * @return Builder
     */
    public function applyBasicFilters(Builder $builder, array $filters): Builder
    {
        $slug = $filters['slug'] ?? '';
        if ($slug) {
            $builder->where('slug', $slug);
        }

        $productIDs = $filters['product_ids'] ?? [];
        if ($productIDs) {
            $builder->whereIn('products.id', $productIDs);
        }

        if (isset($filters['active']) && $filters['active'] !== '') {
            $builder->where('products.active', (bool) $filters['active']);
        }

        return $builder;
    }

    /**
     * Apply date filters.
     *
     * @param  Builder  $builder
     * @param  array  $filters
     * @return Builder
     */
    public function applyDateFilters(Builder $builder, array $filters): Builder
    {
        $dateFilter = $filters['date_filter'] ?? '';
        if ($dateFilter && $dateFilter !== 'all') {
            $now = now();
            switch ($dateFilter) {
                case 'today':
                    $builder->whereDate('products.created_at', $now->toDateString());
                    break;
                case 'this_week':
                    $builder->whereBetween('products.created_at', [
                        $now->startOfWeek()->toDateTimeString(),
                        $now->endOfWeek()->toDateTimeString(),
                    ]);
                    break;
                case 'this_month':
                    $builder->whereBetween('products.created_at', [
                        $now->startOfMonth()->toDateTimeString(),
                        $now->endOfMonth()->toDateTimeString(),
                    ]);
                    break;
                case 'custom':
                    $startDate = $filters['start_date'] ?? '';
                    $endDate   = $filters['end_date'] ?? '';
                    if ($startDate) {
                        $builder->whereDate('products.created_at', '>=', $startDate);
                    }
                    if ($endDate) {
                        $builder->whereDate('products.created_at', '<=', $endDate);
                    }
                    break;
            }
        }

        $createdStart = $filters['created_at_start'] ?? '';
        if ($createdStart) {
            $builder->where('products.created_at', '>', $createdStart);
        }

        $createdEnd = $filters['created_at_end'] ?? '';
        if ($createdEnd) {
            $builder->where('products.created_at', '<', $createdEnd);
        }

        return $builder;
    }
}
