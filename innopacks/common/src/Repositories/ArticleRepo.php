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
use InnoCMS\Common\Models\Article;

class ArticleRepo extends BaseRepo
{
    /**
     * @return array
     */
    public static function getSearchFieldOptions(): array
    {
        return [
            ['value' => '', 'label' => trans('panel::common.all_fields')],
            ['value' => 'keyword', 'label' => trans('panel::common.name')],
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
        return $this->builder($filters)->orderByDesc('id')->paginate();
    }

    /**
     * @param  array  $filters
     * @return Builder
     */
    public function builder(array $filters = []): Builder
    {
        $filters = array_merge($this->filters, $filters);
        $builder = Article::query()->with([
            'translation',
            'catalog.translation',
            'tags.translation',
        ]);

        $slug = $filters['slug'] ?? '';
        if ($slug) {
            $builder->where('slug', 'like', "%$slug%");
        }

        $catalogId = $filters['catalog_id'] ?? '';
        if ($catalogId) {
            $builder->where('catalog_id', $catalogId);
        }

        $searchField = $filters['search_field'] ?? '';
        $keyword     = $filters['keyword'] ?? '';
        if ($keyword) {
            if ($searchField === 'slug') {
                $builder->where('slug', 'like', "%$keyword%");
            } else {
                $builder->whereHas('translation', function (Builder $query) use ($keyword) {
                    $query->where('title', 'like', "%$keyword%")
                        ->orWhere('summary', 'like', "%$keyword%")
                        ->orWhere('content', 'like', "%$keyword%");
                });
            }
        }

        $tagId = $filters['tag_id'] ?? 0;
        if ($tagId) {
            $builder->whereHas('tags', function (Builder $query) use ($tagId) {
                if (is_array($tagId)) {
                    $query->whereIn('tag_id', $tagId);
                } else {
                    $query->where('tag_id', $tagId);
                }
            });
        }

        return $builder;
    }

    /**
     * @param  $data
     * @return Article
     * @throws \Exception|\Throwable
     */
    public function create($data): Article
    {
        $item = new Article($data);
        $item->saveOrFail();

        $translations = array_values($data['translations']);
        $item->translations()->createMany($translations);

        $tagIds = $data['tag_ids'] ?? [];
        $item->tags()->sync($tagIds);

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

        $translations = array_values($data['translations']);
        if ($translations) {
            $item->translations()->delete();
            $item->translations()->createMany($translations);
        }

        $tagIds = $data['tag_ids'] ?? [];
        $item->tags()->sync($tagIds);

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
     * @param  int  $limit
     * @return Collection
     */
    public function getTopViewedArticles(int $limit = 8): Collection
    {
        return $this->withActive()
            ->builder()
            ->orderByDesc('viewed')
            ->limit($limit)
            ->get();
    }
}
