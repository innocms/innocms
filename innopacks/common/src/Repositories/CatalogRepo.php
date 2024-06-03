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
     * @param  $filters
     * @return LengthAwarePaginator
     * @throws \Exception
     */
    public function list($filters = []): LengthAwarePaginator
    {
        return $this->builder($filters)->paginate();
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

        $title = $filters['title'] ?? '';
        if ($title) {
            $builder->whereHas('translation', function ($query) use ($title) {
                $query->where('title', 'like', "%$title%");
            });
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
            'slug'      => $requestData['slug']      ?? '',
            'position'  => $requestData['position']  ?? 0,
            'active'    => $requestData['active']    ?? true,
        ];
    }
}
