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
use InnoCMS\Common\Models\Contact;

class ContactRepo extends BaseRepo
{
    /**
     * @return array
     */
    public static function getSearchFieldOptions(): array
    {
        return [
            ['value' => '', 'label' => trans('panel/common.all_fields')],
            ['value' => 'name', 'label' => trans('panel/contacts.contact_name')],
            ['value' => 'email', 'label' => trans('panel/common.email')],
            ['value' => 'company', 'label' => trans('panel/contacts.company')],
        ];
    }

    /**
     * @return array
     */
    public static function getFilterButtonOptions(): array
    {
        return [
            [
                'name'    => 'status',
                'label'   => trans('panel/common.status'),
                'type'    => 'button',
                'options' => [
                    ['value' => '', 'label' => trans('panel/common.all')],
                    ['value' => '0', 'label' => trans('panel/contacts.unread')],
                    ['value' => '1', 'label' => trans('panel/contacts.read')],
                ],
            ],
        ];
    }

    /**
     * @param  array  $filters
     * @return LengthAwarePaginator
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->builder($filters)->orderByDesc('id')->paginate();
    }

    /**
     * Get query builder.
     *
     * @param  array  $filters
     * @return Builder
     */
    public function builder(array $filters = []): Builder
    {
        $builder = Contact::query();

        $keyword     = $filters['keyword'] ?? '';
        $searchField = $filters['search_field'] ?? '';

        if ($keyword) {
            if ($searchField) {
                $builder->where($searchField, 'like', "%$keyword%");
            } else {
                $builder->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%$keyword%")
                        ->orWhere('email', 'like', "%$keyword%")
                        ->orWhere('company', 'like', "%$keyword%");
                });
            }
        }

        if (isset($filters['status']) && $filters['status'] !== '') {
            $builder->where('status', (bool) $filters['status']);
        }

        return $builder;
    }

    /**
     * Mark a contact as read.
     *
     * @param  int  $id
     * @return void
     */
    public function markAsRead(int $id): void
    {
        $item = $this->detail($id);
        if ($item) {
            $item->update(['status' => true]);
        }
    }

    /**
     * Mark all contacts as read.
     *
     * @return void
     */
    public function markAllAsRead(): void
    {
        Contact::where('status', false)->update(['status' => true]);
    }

    /**
     * Get unread count.
     *
     * @return int
     */
    public function getUnreadCount(): int
    {
        return Contact::where('status', false)->count();
    }
}
