<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Repositories;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InnoCMS\Common\Handlers\TranslationHandler;
use InnoCMS\Common\Models\Category;
use InnoCMS\Common\Models\Product;
use InnoCMS\Common\Repositories\Product\RelationRepo;
use InnoCMS\Common\Services\ProductQueryBuilder;
use Throwable;

class ProductRepo extends BaseRepo
{
    const AVAILABLE_SORT_FIELDS = [
        'position',
        'viewed',
        'updated_at',
        'created_at',
        'price',
        'pt.name',
    ];

    /**
     * Get available sort options for products.
     *
     * @return array
     */
    public static function getSortOptions(): array
    {
        $options = [
            'pt.name'    => trans('panel/common.name'),
            'price'      => trans('panel/product.price'),
            'position'   => trans('panel/common.position'),
            'viewed'     => trans('panel/common.viewed'),
            'created_at' => trans('panel/common.created_at'),
            'updated_at' => trans('panel/common.updated_at'),
        ];

        return fire_hook_filter('common.repo.product.sort_options', $options);
    }

    /**
     * Get search field options for data_search component.
     *
     * @return array
     */
    public static function getSearchFieldOptions(): array
    {
        $options = [
            ['value' => '', 'label' => trans('panel/common.all_fields')],
            ['value' => 'name', 'label' => trans('panel/product.name')],
            ['value' => 'spu_code', 'label' => trans('panel/product.model')],
        ];

        return fire_hook_filter('common.repo.product.search_field_options', $options);
    }

    /**
     * Get filter button options for data_search component.
     *
     * @return array
     */
    public static function getFilterButtonOptions(): array
    {
        $filters = [
            [
                'name'    => 'active',
                'label'   => trans('panel/common.status'),
                'type'    => 'button',
                'options' => [
                    ['value' => '', 'label' => trans('panel/common.all')],
                    ['value' => '1', 'label' => trans('panel/common.active')],
                    ['value' => '0', 'label' => trans('panel/common.inactive')],
                ],
            ],
        ];

        return fire_hook_filter('common.repo.product.filter_button_options', $filters);
    }

    /**
     * @param  array  $filters
     * @return LengthAwarePaginator
     * @throws Exception
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $builder = $this->builder($filters);
        $this->applySorting($builder, $filters);

        return $builder->paginate($filters['per_page'] ?? system_setting('product_per_page', 12));
    }

    /**
     * Apply sorting to the builder.
     *
     * @param  Builder  $builder
     * @param  array  $filters
     * @return void
     * @throws Exception
     */
    private function applySorting(Builder $builder, array $filters): void
    {
        $sort  = $filters['sort'] ?? system_setting('product_default_sort', 'created_at');
        $order = $filters['order'] ?? 'desc';

        if ($sort == 'pt.name') {
            $builder->select(['products.*', 'pt.name']);
            $builder->join('product_translations as pt', function ($join) {
                $join->on('products.id', '=', 'pt.product_id')
                    ->where('pt.locale', locale_code());
            });
        }

        if (! in_array($sort, self::AVAILABLE_SORT_FIELDS)) {
            $sort = 'created_at';
        }

        if (! in_array($order, ['asc', 'desc'])) {
            $order = 'desc';
        }

        if ($sort && $order) {
            $builder->orderBy($sort, $order);
        }
    }

    /**
     * Create product.
     *
     * @param  $data
     * @return mixed
     * @throws Throwable
     */
    public function create($data): mixed
    {
        $product = new Product;

        return $this->createOrUpdate($product, $data);
    }

    /**
     * Update product.
     *
     * @param  $item
     * @param  $data
     * @return mixed
     * @throws Throwable
     */
    public function update($item, $data): mixed
    {
        return $this->createOrUpdate($item, $data);
    }

    /**
     * @param  mixed  $item
     * @return void
     */
    public function destroy(mixed $item): void
    {
        $item->categories()->sync([]);
        $item->relations()->delete();
        $item->translations()->delete();
        $item->delete();
    }

    /**
     * Copy product and related data.
     *
     * @param  Product  $product
     * @return mixed
     */
    public function copy(Product $product): mixed
    {
        $product->load([
            'translations',
            'categories',
            'relations',
        ]);

        return DB::transaction(function () use ($product) {
            $copy = $product->replicate();
            $copy->slug = $this->uniqueCopySlug($product->slug);
            if ($copy->spu_code) {
                $copy->spu_code .= '-'.random_int(10000, 99999);
            }
            $copy->save();

            // Replicate translations (auto-increment PK, safe to rebuild).
            foreach ($product->translations as $translation) {
                $copy->translations()->create($translation->replicate()->toArray());
            }

            // Re-attach the same categories.
            $copy->categories()->attach($product->categories->pluck('id'));

            // Replicate relations as bidirectional, re-pointed at the copy.
            RelationRepo::getInstance()->handleBidirectionalRelations(
                $copy,
                $product->relations->pluck('relation_id')->toArray()
            );

            return $copy;
        });
    }

    /**
     * Build a unique slug for a copy by retrying until it doesn't collide.
     * A NULL/empty base stays NULL (the column is nullable-unique, NULLs don't clash).
     *
     * @param  ?string  $base
     * @return ?string
     */
    private function uniqueCopySlug(?string $base): ?string
    {
        if (empty($base)) {
            return null;
        }

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $candidate = $base.'-'.random_int(10000, 99999).($attempt ?: '');
            if (! $this->findBySlug($candidate)) {
                return $candidate;
            }
        }

        return $base.'-'.random_int(100000, 999999);
    }

    /**
     * Create or update product.
     *
     * @param  Product  $product
     * @param  $data
     * @return mixed
     * @throws Throwable
     */
    private function createOrUpdate(Product $product, $data): mixed
    {
        $isUpdating = $product->id > 0;

        DB::beginTransaction();

        try {
            if ($isUpdating) {
                // Keep existing scalar values for fields absent from $data, so
                // handleProductData() defaults (null/0/false) don't wipe them on
                // a partial update(). (UI forms submit all fields; this guards
                // programmatic callers. Use patch() for true partial updates.)
                foreach (['slug', 'images', 'video', 'price', 'link', 'spu_code', 'position', 'viewed', 'active'] as $field) {
                    if (! array_key_exists($field, $data)) {
                        $data[$field] = $product->{$field};
                    }
                }
            }

            $productData = $this->handleProductData($data);
            $product->fill($productData);
            $product->updated_at = now();
            $product->saveOrFail();

            // Translations: only delete+rebuild when explicitly provided, so a
            // partial update() that omits translations keeps existing rows.
            if (array_key_exists('translations', $data)) {
                if ($isUpdating) {
                    $product->translations()->delete();
                }
                $translations = $this->handleTranslations($data['translations']);
                if ($translations) {
                    $product->translations()->createMany($translations);
                }
            }

            // Relations: handleBidirectionalRelations clears forward+reverse first.
            RelationRepo::getInstance()->handleBidirectionalRelations($product, $data['related_ids'] ?? []);

            $product->categories()->sync($data['categories'] ?? []);

            DB::commit();

            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Patch a product (partial update).
     *
     * @param  Product  $product
     * @param  $data
     * @return mixed
     * @throws Throwable
     */
    public function patch(Product $product, $data): mixed
    {
        DB::beginTransaction();

        try {
            $product->fill($data);
            $product->saveOrFail();

            if (isset($data['translations'])) {
                $translations = $this->handleTranslations($data['translations']);
                foreach ($translations as $translation) {
                    $existTranslation = $product->translations()->where('locale', $translation['locale'])->first();
                    if ($existTranslation) {
                        $existTranslation->update($translation);
                    } else {
                        $product->translations()->create($translation);
                    }
                }
            }

            if (isset($data['related_ids'])) {
                RelationRepo::getInstance()->handleBidirectionalRelations($product, $data['related_ids']);
            }

            if (isset($data['categories'])) {
                $product->categories()->sync($data['categories']);
            }

            DB::commit();

            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param  $data
     * @return array
     */
    public function handleProductData($data): array
    {
        $images = $data['images'] ?? null;
        if (is_string($images)) {
            $images = json_decode($images, true);
        }

        $video = $data['video'] ?? null;
        if (is_string($video)) {
            // A plain URL string ('https://.../x.mp4') is not valid JSON, so a
            // bare json_decode would null it out — treat it as ['url' => <string>].
            $decoded = json_decode($video, true);
            $video = is_array($decoded) ? $decoded : ['url' => $video];
        }

        $slug = $data['slug'] ?? null;
        if (is_string($slug) && empty($slug)) {
            $slug = null;
        }

        $spuCode = $data['spu_code'] ?? null;
        if (is_string($spuCode) && empty($spuCode)) {
            $spuCode = null;
        }

        return [
            'slug'     => $slug,
            'images'   => $images,
            'video'    => $video,
            'price'    => (float) ($data['price'] ?? 0),
            'link'     => $data['link'] ?? '',
            'spu_code' => $spuCode,
            'position' => (int) ($data['position'] ?? 0),
            'viewed'   => (int) ($data['viewed'] ?? 0),
            'active'   => (bool) ($data['active'] ?? false),
        ];
    }

    /**
     * @param  $translations
     * @return array
     * @throws Exception
     */
    private function handleTranslations($translations): array
    {
        if (empty($translations)) {
            return [];
        }

        $fieldMap = [
            'name' => ['summary', 'selling_point', 'content', 'meta_title', 'meta_description', 'meta_keywords'],
        ];

        return TranslationHandler::process($translations, $fieldMap);
    }

    /**
     * @return Builder
     */
    public function baseBuilder(): Builder
    {
        return Product::query();
    }

    /**
     * Build product query with filters.
     *
     * @param  array  $filters
     * @return Builder
     * @throws Exception
     */
    public function builder(array $filters = []): Builder
    {
        $relations = [
            'translation',
            'categories.translation',
        ];

        $relations = array_merge($this->relations, $relations);

        $builder = $this->baseBuilder()->with($relations);

        $filters = array_merge($this->filters, $filters);

        $queryBuilder = new ProductQueryBuilder;

        $builder = $queryBuilder->applyCategoryFilters($builder, $filters);
        $builder = $queryBuilder->applyPriceFilters($builder, $filters);
        $builder = $queryBuilder->applySearchFilters($builder, $filters);
        $builder = $queryBuilder->applyBasicFilters($builder, $filters);
        $builder = $queryBuilder->applyDateFilters($builder, $filters);

        return fire_hook_filter('repo.product.builder', $builder);
    }

    /**
     * @param  int  $limit
     * @return mixed
     * @throws Exception
     */
    public function getLatestProducts(int $limit = 8): mixed
    {
        return $this->withActive()->builder()
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get product list by IDs.
     *
     * @param  mixed  $productIDs
     * @return Collection
     */
    public function getListByProductIDs(mixed $productIDs): Collection
    {
        if (empty($productIDs)) {
            return collect();
        }
        if (is_string($productIDs)) {
            $productIDs = explode(',', $productIDs);
        }

        return Product::query()
            ->with(['translation'])
            ->whereIn('id', $productIDs)
            ->orderByRaw('FIELD(id, '.implode(',', $productIDs).')')
            ->get();
    }

    /**
     * @param  $spuCode
     * @return ?Product
     */
    public function findBySpuCode($spuCode): ?Product
    {
        if (empty($spuCode)) {
            return null;
        }

        return Product::query()->where('spu_code', $spuCode)->first();
    }

    /**
     * @param  $slug
     * @return ?Product
     */
    public function findBySlug($slug): ?Product
    {
        if (empty($slug)) {
            return null;
        }

        return Product::query()->where('slug', $slug)->first();
    }

    /**
     * @param  $keyword
     * @param  int  $limit
     * @return mixed
     */
    public function autocomplete($keyword, int $limit = 10): mixed
    {
        $keyword = trim((string) $keyword);
        $builder = Product::query()->with(['translation']);
        if ($keyword !== '') {
            $builder->whereHas('translation', function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            });
        }

        return $builder->orderByDesc('id')->limit($limit)->get();
    }

    /**
     * @param  $id
     * @return string
     */
    public function getNameByID($id): string
    {
        return Product::query()->find($id)?->translation?->name ?? '';
    }

    /**
     * Get category options for cascader component.
     *
     * @return array
     */
    public static function getCategoryOptions(): array
    {
        // Load all active categories + translations in 3 queries, build the tree in memory
        $allCategories = Category::where('active', true)
            ->with(['translation', 'translations'])
            ->orderBy('position')
            ->get();

        $itemsById = $allCategories->keyBy('id');

        // Build tree: attach children to parents, collect roots
        $tree = collect();
        foreach ($allCategories as $category) {
            if ($category->parent_id && isset($itemsById[$category->parent_id])) {
                $parent = $itemsById[$category->parent_id];
                if (! isset($parent->inlineChildren)) {
                    $parent->inlineChildren = collect();
                }
                $parent->inlineChildren->push($category);
            } else {
                $tree->push($category);
            }
        }

        return CategoryRepo::formatCategoriesForCascaderInline($tree);
    }
}
