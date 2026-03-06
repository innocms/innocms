<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InnoCMS\Common\Models\Article;
use InnoCMS\Common\Models\Page;
use InnoCMS\Common\Repositories\CommentRepo;

class CommentController
{
    /**
     * Store a new comment.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'commentable_type' => 'required|string',
                'commentable_id'   => 'required|integer',
                'author_name'      => 'required|string|max:100',
                'author_email'     => 'required|email|max:255',
                'content'          => 'required|string|max:2000',
                'rating'           => 'nullable|integer|min:1|max:5',
                'parent_id'        => 'nullable|integer|exists:comments,id',
            ]);

            if ($validator->fails()) {
                return json_fail($validator->errors()->first());
            }

            $commentableType = $request->get('commentable_type');
            $commentableId   = $request->get('commentable_id');

            $commentable = $this->getCommentable($commentableType, $commentableId);
            if (! $commentable) {
                return json_fail('Invalid comment target');
            }

            $data = [
                'commentable_type' => get_class($commentable),
                'commentable_id'   => $commentableId,
                'author_name'      => $request->get('author_name'),
                'author_email'     => $request->get('author_email'),
                'content'          => strip_tags($request->get('content')),
                'rating'           => $request->get('rating'),
                'parent_id'        => $request->get('parent_id'),
                'status'           => Comment::STATUS_PENDING,
                'ip_address'       => $request->ip(),
                'user_agent'       => $request->userAgent(),
            ];

            CommentRepo::getInstance()->create($data);

            return json_success(trans('front/comment.submitted_success'));
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Get comments for a specific entity.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        try {
            $commentableType = $request->get('commentable_type');
            $commentableId   = $request->get('commentable_id');

            $commentable = $this->getCommentable($commentableType, $commentableId);
            if (! $commentable) {
                return json_fail('Invalid comment target');
            }

            $comments = CommentRepo::getInstance()->builder([
                'commentable_type' => get_class($commentable),
                'commentable_id'   => $commentableId,
                'status'           => Comment::STATUS_APPROVED,
            ])
                ->whereNull('parent_id')
                ->with('replies')
                ->orderByDesc('created_at')
                ->get();

            return json_success('Success', ['comments' => $comments]);
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Get the commentable entity.
     *
     * @param  string  $type
     * @param  int  $id
     * @return mixed
     */
    private function getCommentable(string $type, int $id): mixed
    {
        return match ($type) {
            'article' => Article::find($id),
            'page'    => Page::find($id),
            default   => null,
        };
    }
}
