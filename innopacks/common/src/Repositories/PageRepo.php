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
use InnoCMS\Common\Models\Page;

class PageRepo extends BaseRepo
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
     * Get page builder.
     *
     * @param  array  $filters
     * @return Builder
     */
    public function builder(array $filters = []): Builder
    {
        $builder = Page::query()->with(['translation']);

        $filters = array_merge($this->filters, $filters);

        $slug = $filters['slug'] ?? '';
        if ($slug) {
            $builder->where('slug', 'like', "%$slug%");
        }

        if (isset($filters['active'])) {
            $builder->where('active', (bool) $filters['active']);
        }

        return $builder;
    }

    /**
     * @param  $data
     * @return Page
     * @throws \Exception|\Throwable
     */
    public function create($data): Page
    {
        $item = new Page($data);
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
