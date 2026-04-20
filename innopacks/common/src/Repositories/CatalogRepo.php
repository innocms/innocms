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
use InnoCMS\Common\Models\Catalog;

class CatalogRepo extends BaseRepo
{
    /**
     * @return array
     */
    public static function getSearchFieldOptions(): array
    {
        return [
            ['value' => '', 'label' => trans('panel::common.all_fields')],
            ['value' => 'title', 'label' => trans('panel::common.name')],
            ['value' => 'slug', 'label' => trans('panel::common.slug')],
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
                'label'   => trans('panel::common.status'),
                'type'    => 'button',
                'options' => [
                    ['value' => '', 'label' => trans('panel::common.all')],
                    ['value' => '1', 'label' => trans('panel::common.active')],
                    ['value' => '0', 'label' => trans('panel::common.inactive')],
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
            'parent.translation',
            'children',
            'children.translation',
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
     * @param  $data
     * @return Catalog
     * @throws \Exception|\Throwable
     */
    public function create($data): Catalog
    {
        $item = new Catalog($this->handleData($data));
        $item->saveOrFail();
        $item->translations()->createMany($data['translations']);

        return $item;
    }

    /**
     * @param  $item
     * @param  $data
     * @return mixed
     */
    public function update($item, $data): mixed
    {
        $item->fill($this->handleData($data));
        $item->saveOrFail();
        $item->translations()->delete();
        $item->translations()->createMany($data['translations']);

        return $item;
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
            'parent_id' => $requestData['parent_id'] ?? 0,
            'slug'      => $requestData['slug'] ?? '',
            'position'  => $requestData['position'] ?? 0,
            'active'    => $requestData['active'] ?? true,
        ];
    }
}
