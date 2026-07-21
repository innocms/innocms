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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InnoCMS\Common\Handlers\TranslationHandler;
use InnoCMS\Common\Models\Category;
use Throwable;

class CategoryRepo extends BaseRepo
{
    /**
     * Get search field options for data_search component.
     *
     * @return array
     */
    public static function getSearchFieldOptions(): array
    {
        return [
            ['value' => '', 'label' => trans('panel/common.all_fields')],
            ['value' => 'name', 'label' => trans('panel/common.name')],
            ['value' => 'slug', 'label' => trans('panel/common.slug')],
        ];
    }

    /**
     * Get filter button options for data_search component.
     *
     * @return array
     */
    public static function getFilterButtonOptions(): array
    {
        return [
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
    }

    /**
     * @param  int  $limit
     * @return Collection
     */
    public function getActiveCategories($limit = 10): Collection
    {
        $filters = ['active' => true];

        return $this->builder($filters)->limit($limit)->get();
    }

    /**
     * Format categories for cascader component (uses Eloquent children relation).
     *
     * @param  Collection  $categories
     * @return array
     */
    public static function formatCategoriesForCascader($categories): array
    {
        $result = [];
        foreach ($categories as $category) {
            $node = [
                'value' => $category->id,
                'label' => $category->fallbackName(),
            ];
            if ($category->children && ! $category->children->isEmpty()) {
                $node['children'] = self::formatCategoriesForCascader($category->children);
            }
            $result[] = $node;
        }

        return $result;
    }

    /**
     * Format categories for cascader using inline children (no extra queries).
     *
     * @param  Collection  $categories
     * @return array
     */
    public static function formatCategoriesForCascaderInline($categories): array
    {
        $result = [];
        foreach ($categories as $category) {
            $node = [
                'value' => $category->id,
                'label' => $category->fallbackName(),
            ];
            $children = $category->inlineChildren ?? collect();
            if ($children->isNotEmpty()) {
                $node['children'] = self::formatCategoriesForCascaderInline($children);
            }
            $result[] = $node;
        }

        return $result;
    }

    /**
     * @param  array  $filters
     * @return Builder
     */
    public function builder(array $filters = []): Builder
    {
        $filters = array_merge($this->filters, $filters);

        $builder = Category::query()->with([
            'translation',
            'translations',
            'parent.translation',
            'children.translation',
            'children.translations',
            'children.children.translation',
            'children.children.translations',
        ]);

        $slug = $filters['slug'] ?? '';
        if ($slug) {
            $builder->where('slug', $slug);
        }

        $parentSlug = $filters['parent_slug'] ?? '';
        if ($parentSlug) {
            $category = Category::query()->where('slug', $parentSlug)->first();
            if ($category) {
                $filters['parent_id'] = $category->id;
            }
        }

        if (isset($filters['parent_id'])) {
            $parentID = (int) $filters['parent_id'];
            if ($parentID == 0) {
                $builder->where(function (Builder $query) {
                    $query->where('parent_id', 0)->orWhereNull('parent_id');
                });
            } else {
                $builder->where('parent_id', $parentID);
            }
        }

        $excludeIDs = $filters['exclude_ids'] ?? [];
        if ($excludeIDs) {
            $builder->whereNotIn('id', $excludeIDs);
        }

        $categoryIDs = $filters['category_ids'] ?? [];
        if ($categoryIDs) {
            $builder->whereIn('id', $categoryIDs);
        }

        if (isset($filters['active'])) {
            $builder->where('active', (bool) $filters['active']);
        }

        $keyword = $filters['keyword'] ?? '';
        if ($keyword) {
            $builder->whereHas('translation', function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            });
        }

        return fire_hook_filter('repo.category.builder', $builder->orderBy('position')->orderBy('id'));
    }

    /**
     * Create category.
     *
     * @param  $data
     * @return mixed
     * @throws Throwable
     */
    public function create($data): mixed
    {
        $item = new Category;
        $this->createOrUpdate($item, $data);

        $children = $data['children'] ?? [];
        $this->handleChildren($item, $children);

        return $item;
    }

    /**
     * Update category.
     *
     * @param  $item
     * @param  $data
     * @return mixed
     * @throws Throwable
     */
    public function update($item, $data): mixed
    {
        $this->createOrUpdate($item, $data);

        $children = $data['children'] ?? [];
        $this->handleChildren($item, $children);

        return $item;
    }

    /**
     * Partial update for REST PATCH: merge validated fields onto current state,
     * then run the same pipeline as update().
     *
     * @param  array<string, mixed>  $data
     *
     * @throws Throwable
     */
    public function patch(Category $category, array $data): mixed
    {
        $category->loadMissing(['translations']);

        $merged = [
            'parent_id'    => $category->parent_id ?? 0,
            'slug'         => $category->slug,
            'image'        => $category->image,
            'position'     => $category->position,
            'active'       => $category->active,
            'translations' => [],
        ];

        foreach ($category->translations as $translation) {
            $merged['translations'][$translation->locale] = $translation->only($translation->getFillable());
        }

        foreach (['parent_id', 'slug', 'image', 'position', 'active'] as $key) {
            if (array_key_exists($key, $data)) {
                $merged[$key] = $data[$key];
            }
        }

        if (isset($data['translations']) && is_array($data['translations'])) {
            foreach ($data['translations'] as $locale => $fields) {
                if (! is_array($fields)) {
                    continue;
                }
                $merged['translations'][$locale] = array_merge(
                    $merged['translations'][$locale] ?? ['locale' => $locale],
                    $fields
                );
            }
        }

        if (array_key_exists('children', $data)) {
            $merged['children'] = $data['children'];
        }

        return $this->update($category, $merged);
    }

    /**
     * Create or update category.
     *
     * @param  Category  $category
     * @param  $data
     * @return void
     * @throws Throwable
     */
    private function createOrUpdate(Category $category, $data): void
    {
        DB::beginTransaction();

        try {
            $categoryData = $this->handleData($data);
            $category->fill($categoryData);
            $category->saveOrFail();

            $translations = $this->handleTranslations($data['translations'] ?? []);
            if ($translations) {
                $category->translations()->delete();
                $category->translations()->createMany($translations);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param  $data
     * @return array
     */
    private function handleData($data): array
    {
        return [
            'parent_id' => $data['parent_id'] ?? 0,
            'slug'      => $data['slug'] ?? null,
            'image'     => $data['image'] ?? null,
            'position'  => $data['position'] ?? 0,
            'active'    => (bool) ($data['active'] ?? true),
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
            'name' => ['content', 'meta_title', 'meta_description', 'meta_keywords'],
        ];

        return TranslationHandler::process($translations, $fieldMap);
    }

    /**
     * @param  $item
     * @param  $children
     * @return void
     * @throws Throwable
     */
    private function handleChildren($item, $children): void
    {
        if (empty($children)) {
            return;
        }

        foreach ($children as $childData) {
            $childCategory = new Category;

            $childId = $childData['id'] ?? 0;
            if ($childId) {
                $childCategory = Category::query()->find($childId);
            }

            $childData['parent_id'] = $item->id;
            if ($childCategory->id) {
                $this->update($childCategory, $childData);
            } else {
                $this->create($childData);
            }
        }
    }

    /**
     * @param  $keyword
     * @param  int  $limit
     * @return mixed
     */
    public function autocomplete($keyword, int $limit = 10): mixed
    {
        $keyword = trim((string) $keyword);
        $builder = Category::query()->with(['translation']);
        if ($keyword !== '') {
            $builder->whereHas('translation', function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            });
        }

        return $builder->orderBy('position')->orderBy('id')->limit($limit)->get();
    }

    /**
     * Get category list by IDs.
     *
     * @param  mixed  $categoryIDs
     * @return mixed
     */
    public function getListByCategoryIDs(mixed $categoryIDs): mixed
    {
        if (empty($categoryIDs)) {
            return [];
        }
        if (is_string($categoryIDs)) {
            $categoryIDs = explode(',', $categoryIDs);
        }

        return Category::query()
            ->with(['translation'])
            ->whereIn('id', $categoryIDs)
            ->orderByRaw('FIELD(id, '.implode(',', $categoryIDs).')')
            ->get();
    }

    /**
     * @param  $id
     * @return string
     */
    public function getNameByID($id): string
    {
        return Category::query()->find($id)?->translation?->name ?? '';
    }

    /**
     * @param  $name
     * @param  string  $locale
     * @return mixed
     */
    public function findByName($name, string $locale = ''): mixed
    {
        if (empty($locale)) {
            $locale = locale_code();
        }

        return Category::query()
            ->whereHas('translations', function (Builder $query) use ($name, $locale) {
                $query->where('name', $name)->where('locale', $locale);
            })
            ->first();
    }

    /**
     * @param  $name
     * @param  string  $locale
     * @return mixed
     * @throws Throwable
     */
    public function findOrCreateByName($name, string $locale = ''): mixed
    {
        $category = $this->findByName($name, $locale);
        if ($category) {
            return $category;
        }

        $data = [];
        foreach (locales() as $locale) {
            $data['translations'][] = [
                'locale' => $locale->code,
                'name'   => $name,
            ];
        }

        return $this->create($data);
    }

    /**
     * Get flat category options (id + name) for active categories.
     *
     * @return array
     */
    public function getCategoryOptions(): array
    {
        $options    = [];
        $categories = $this->getActiveCategories();
        foreach ($categories as $category) {
            $options[] = [
                'id'   => $category->id,
                'name' => $category->fallbackName(),
            ];
        }

        return $options;
    }

    /**
     * Reorder categories within the same parent by an ordered ID list.
     *
     * @param  array  $ids  Ordered category IDs within the same parent.
     * @return void
     * @throws Throwable
     */
    public function reorder(array $ids): void
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (empty($ids)) {
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($ids as $position => $id) {
                if ($id > 0) {
                    Category::query()->where('id', $id)->update(['position' => $position]);
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get hierarchical category list with breadcrumb style.
     *
     * @param  array  $filters
     * @return array
     */
    public function getHierarchicalCategories(array $filters = []): array
    {
        $allCategories = $this->builder($filters)
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $hierarchical = [];
        $this->buildHierarchy($allCategories, $hierarchical, 0, []);

        return $hierarchical;
    }

    /**
     * Recursively build hierarchy structure with breadcrumb paths.
     *
     * @param  Collection  $categories
     * @param  array  $result
     * @param  int  $parentId
     * @param  array  $parentPath
     * @return void
     */
    private function buildHierarchy($categories, &$result, $parentId = 0, $parentPath = [])
    {
        $children = $categories->where('parent_id', $parentId)->sortBy('position');

        foreach ($children as $category) {
            $currentPath    = array_merge($parentPath, [$category->fallbackName()]);
            $breadcrumbName = implode(' > ', $currentPath);

            $result[] = [
                'id'    => $category->id,
                'name'  => $breadcrumbName,
                'level' => count($currentPath) - 1,
            ];

            $this->buildHierarchy($categories, $result, $category->id, $currentPath);
        }
    }
}
