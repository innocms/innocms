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
use InnoCMS\Common\Models\Comment;

class CommentRepo extends BaseRepo
{
    /**
     * Get comments list with pagination.
     *
     * @param  array  $filters
     * @return LengthAwarePaginator
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        return $this->builder($filters)->orderByDesc('id')->paginate();
    }

    /**
     * Build the query with filters.
     *
     * @param  array  $filters
     * @return Builder
     */
    public function builder(array $filters = []): Builder
    {
        $filters = array_merge($this->filters, $filters);
        $builder = Comment::query()->with(['commentable']);

        $status = $filters['status'] ?? '';
        if ($status) {
            $builder->where('status', $status);
        }

        $keyword = $filters['keyword'] ?? '';
        if ($keyword) {
            $builder->where(function ($query) use ($keyword) {
                $query->where('author_name', 'like', "%$keyword%")
                    ->orWhere('author_email', 'like', "%$keyword%")
                    ->orWhere('content', 'like', "%$keyword%");
            });
        }

        $commentableType = $filters['commentable_type'] ?? '';
        if ($commentableType) {
            $builder->where('commentable_type', $commentableType);
        }

        return $builder;
    }

    /**
     * Create a new comment.
     *
     * @param  array  $data
     * @return Comment
     * @throws \Throwable
     */
    public function create(array $data): Comment
    {
        $comment = new Comment($data);
        $comment->saveOrFail();

        return $comment;
    }

    /**
     * Update a comment.
     *
     * @param  Comment  $comment
     * @param  array  $data
     * @return Comment
     * @throws \Throwable
     */
    public function update(Comment $comment, array $data): Comment
    {
        $comment->fill($data);
        $comment->saveOrFail();

        return $comment;
    }

    /**
     * Delete a comment.
     *
     * @param  Comment  $comment
     * @return void
     */
    public function destroy(Comment $comment): void
    {
        // Delete replies first
        $comment->replies()->delete();
        $comment->delete();
    }

    /**
     * Approve a comment.
     *
     * @param  Comment  $comment
     * @return void
     */
    public function approve(Comment $comment): void
    {
        $comment->update(['status' => Comment::STATUS_APPROVED]);
    }

    /**
     * Mark comment as spam.
     *
     * @param  Comment  $comment
     * @return void
     */
    public function markAsSpam(Comment $comment): void
    {
        $comment->update(['status' => Comment::STATUS_SPAM]);
    }
}
