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
use InnoCMS\Common\Models\Tag;

class TagRepo extends BaseRepo
{
    /**
     * @return array
     */
    public static function getSearchFieldOptions(): array
    {
        return [
            ['value' => '', 'label' => trans('panel::common.all_fields')],
            ['value' => 'name', 'label' => trans('panel::common.name')],
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
     * @param  $name
     * @return Builder[]|Collection
     */
    public function searchByName($name): Collection|array
    {
        $filters = [
            'name' => $name,
        ];

        return $this->builder($filters)->limit(10)->get();
    }

    /**
     * Get query builder.
     *
     * @param  array  $filters
     * @return Builder
     */
    public function builder(array $filters = []): Builder
    {
        $builder = Tag::query()->with(['translation']);

        $slug = $filters['slug'] ?? '';
        if ($slug) {
            $builder->where('slug', 'like', "%$slug%");
        }

        $tagIds = $filters['tag_ids'] ?? [];
        if ($tagIds) {
            $builder->whereIn('id', $tagIds);
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
                    $query->where('name', 'like', "%$keyword%");
                });
            }
        }

        return $builder;
    }

    /**
     * @param  $data
     * @return Tag
     * @throws \Exception|\Throwable
     */
    public function create($data): Tag
    {
        $item = new Tag($data);
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
        $item->fill($data);
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
}
