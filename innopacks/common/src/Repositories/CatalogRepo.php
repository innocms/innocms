<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InnoCMS\Common\Handlers\TranslationHandler;
use InnoCMS\Common\Models\Catalog;

class CatalogRepo extends BaseRepo
{
    /**
     * @return array
     */
    public static function getSearchFieldOptions(): array
    {
        return [
            ['value' => '', 'label' => trans('panel/common.all_fields')],
            ['value' => 'title', 'label' => trans('panel/common.name')],
            ['value' => 'slug', 'label' => trans('panel/common.slug')],
        ];
    }

    /**
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
     * @param  $filters
     * @return LengthAwarePaginator
     * @throws \Exception
     */
    public function list($filters = []): LengthAwarePaginator
    {
        return $this->builder($filters)->paginate();
    }

    /**
     * @return Collection
     */
    public function getTopCatalogs(): Collection
    {
        $filters = [
            'parent_id' => 0,
        ];

        return $this->withActive()->builder($filters)->get();
    }

    /**
     * @param  $title
     * @return Builder[]|Collection
     */
    public function searchByTitle($title): Collection|array
    {
        $filters = [
            'title' => $title,
        ];

        return $this->builder($filters)->limit(10)->get();
    }

    /**
     * @param  array  $filters
     * @return Builder
     */
    public function builder(array $filters = []): Builder
    {
        $filters = array_merge($this->filters, $filters);
        $builder = Catalog::query()->with([
            'translation',
            'translations',
            'parent.translation',
            'parent.translations',
            'children',
            'children.translation',
            'children.translations',
        ]);

        $slug = $filters['slug'] ?? '';
        if ($slug) {
            $builder->where('slug', 'like', "%$slug%");
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

        if (isset($filters['active'])) {
            $builder->where('active', (bool) $filters['active']);
        }

        $searchField = $filters['search_field'] ?? '';
        $keyword     = $filters['keyword'] ?? '';
        if ($keyword) {
            if ($searchField === 'slug') {
                $builder->where('slug', 'like', "%$keyword%");
            } else {
                $builder->whereHas('translation', function ($query) use ($keyword) {
                    $query->where('title', 'like', "%$keyword%");
                });
            }
        }

        return $builder;
    }

    /**
     * Normalize + filter translations: drop optional-locale rows left blank and
     * cast nulls to '' (shared handler, same path as Category/Product).
     *
     * @param  array  $translations
     * @return array
     */
    private function handleTranslations(array $translations): array
    {
        if (empty($translations)) {
            return [];
        }

        $fieldMap = [
            'title' => ['summary', 'meta_title', 'meta_keywords', 'meta_description'],
        ];

        return TranslationHandler::process($translations, $fieldMap);
    }

    /**
     * @param  $data
     * @return Catalog
     * @throws \Exception|\Throwable
     */
    public function create($data): Catalog
    {
        $this->assertParentValid(null, (int) ($data['parent_id'] ?? 0));

        return DB::transaction(function () use ($data) {
            $item = new Catalog($this->handleData($data));
            $item->saveOrFail();
            $item->translations()->createMany($this->handleTranslations($data['translations'] ?? []));

            return $item;
        });
    }

    /**
     * @param  $item
     * @param  $data
     * @return mixed
     */
    public function update($item, $data): mixed
    {
        $this->assertParentValid($item->id, (int) ($data['parent_id'] ?? 0));

        return DB::transaction(function () use ($item, $data) {
            $item->fill($this->handleData($data));
            $item->saveOrFail();
            $translations = $this->handleTranslations($data['translations'] ?? []);
            if ($translations) {
                $item->translations()->delete();
                $item->translations()->createMany($translations);
            }

            return $item;
        });
    }

    /**
     * @param  Catalog  $catalog
     * @param  array  $data
     * @return mixed
     */
    public function patch(Catalog $catalog, array $data): mixed
    {
        $catalog->loadMissing(['translations']);

        $merged = [
            'parent_id'    => $catalog->parent_id ?? 0,
            'slug'         => $catalog->slug,
            'position'     => $catalog->position,
            'active'       => $catalog->active,
            'translations' => [],
        ];

        foreach ($catalog->translations as $translation) {
            $merged['translations'][$translation->locale] = $translation->only($translation->getFillable());
        }

        foreach (['parent_id', 'slug', 'position', 'active'] as $key) {
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

        return $this->update($catalog, $merged);
    }

    /**
     * @param  $item
     * @return void
     */
    public function destroy($item): void
    {
        $item->translations()->delete();
        $item->delete();
    }

    /**
     * @param  $requestData
     * @return array
     */
    private function handleData($requestData): array
    {
        return [
            'parent_id' => (int) ($requestData['parent_id'] ?? 0),
            'slug'      => $requestData['slug'] ?? '',
            'position'  => (int) ($requestData['position'] ?? 0),
            'active'    => $requestData['active'] ?? true,
        ];
    }

    /**
     * Fuzzy search catalogs by translated title for autocomplete picker.
     *
     * @param  $keyword
     * @param  int  $limit
     * @return mixed
     */
    public function autocomplete($keyword, int $limit = 10): mixed
    {
        $keyword = trim((string) $keyword);
        $builder = Catalog::query()->with(['translation']);
        if ($keyword !== '') {
            $builder->whereHas('translation', function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%");
            });
        }

        return $builder->orderByDesc('id')->limit($limit)->get();
    }

    /**
     * Catalogs in depth-first tree order, each model carrying a `level`
     * attribute (0 = root). Orphans whose parent is missing are appended
     * at root level so nothing vanishes from the panel list.
     *
     * @param  array  $filters
     * @return \Illuminate\Support\Collection
     */
    public function treeList(array $filters = []): \Illuminate\Support\Collection
    {
        $all = $this->builder($filters)->orderBy('position')->orderBy('id')->get();

        $sorted = collect();
        $walk   = function (int $parentId, int $level) use (&$walk, $all, $sorted) {
            foreach ($all->where('parent_id', $parentId)->sortBy('position') as $catalog) {
                $catalog->level = $level;
                $sorted->push($catalog);
                $walk($catalog->id, $level + 1);
            }
        };
        $walk(0, 0);

        foreach ($all as $catalog) {
            if (! $sorted->contains('id', $catalog->id)) {
                $catalog->level = 0;
                $sorted->push($catalog);
            }
        }

        return $sorted;
    }

    /**
     * Indented parent-select options for the panel form. When editing, the
     * catalog itself and its descendants are excluded to prevent cycles.
     *
     * @param  int|null  $excludeId
     * @return array
     */
    public function treeOptions(?int $excludeId = null): array
    {
        $skip = [];
        if ($excludeId) {
            $skip   = $this->descendantIds($excludeId);
            $skip[] = $excludeId;
        }

        $options = [
            ['id' => 0, 'name' => __('panel/catalog.top_level')],
        ];
        foreach ($this->treeList(['active' => 1]) as $catalog) {
            if (in_array($catalog->id, $skip, true)) {
                continue;
            }
            $options[] = [
                'id'   => $catalog->id,
                'name' => str_repeat('— ', $catalog->level).$catalog->fallbackName('title'),
            ];
        }

        return $options;
    }

    /**
     * Reorder catalogs by an ordered ID list (drag-and-drop).
     *
     * @param  array  $ids
     * @return void
     * @throws \Throwable
     */
    public function reorder(array $ids): void
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));
        if (empty($ids)) {
            return;
        }

        DB::transaction(function () use ($ids) {
            foreach ($ids as $position => $id) {
                if ($id > 0) {
                    Catalog::query()->where('id', $id)->update(['position' => $position]);
                }
            }
        });
    }

    /**
     * Guard against assigning a catalog as its own parent or under one of
     * its descendants.
     *
     * @param  int|null  $selfId
     * @param  int  $parentId
     * @return void
     *
     * @throws \Exception
     */
    private function assertParentValid(?int $selfId, int $parentId): void
    {
        if ($parentId <= 0 || ! $selfId) {
            return;
        }

        if ($parentId === $selfId) {
            throw new \Exception(trans('panel/category.parent_self'));
        }

        if (in_array($parentId, $this->descendantIds($selfId), true)) {
            throw new \Exception(trans('panel/category.circular_reference'));
        }
    }

    /**
     * @param  int  $id
     * @return int[]
     */
    private function descendantIds(int $id): array
    {
        $ids   = [];
        $queue = [$id];
        while ($queue) {
            $children = Catalog::query()->whereIn('parent_id', $queue)->pluck('id')->all();
            $ids      = array_merge($ids, $children);
            $queue    = $children;
        }

        return $ids;
    }
}
