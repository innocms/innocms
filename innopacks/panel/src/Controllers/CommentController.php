<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InnoCMS\Common\Models\Comment;
use InnoCMS\Common\Repositories\CommentRepo;

class CommentController extends BaseController
{
    /**
     * Display a listing of comments.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function index(Request $request): mixed
    {
        $filters   = $request->all();
        $comments  = CommentRepo::getInstance()->list($filters);

        return view('panel::comments.index', compact('comments'));
    }

    /**
     * Show the form for editing the specified comment.
     *
     * @param  Comment  $comment
     * @return mixed
     */
    public function edit(Comment $comment): mixed
    {
        return view('panel::comments.edit', compact('comment'));
    }

    /**
     * Update the specified comment.
     *
     * @param  Request  $request
     * @param  Comment  $comment
     * @return JsonResponse
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        try {
            $data = $request->validate([
                'author_name'  => 'required|string|max:100',
                'author_email' => 'required|email|max:100',
                'content'      => 'required|string',
                'status'       => 'required|in:pending,approved,spam',
            ]);

            CommentRepo::getInstance()->update($comment, $data);

            return json_success(trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Remove the specified comment.
     *
     * @param  Comment  $comment
     * @return JsonResponse
     */
    public function destroy(Comment $comment): JsonResponse
    {
        try {
            CommentRepo::getInstance()->destroy($comment);

            return json_success(trans('panel::common.deleted_success'));
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Approve a comment.
     *
     * @param  Comment  $comment
     * @return JsonResponse
     */
    public function approve(Comment $comment): JsonResponse
    {
        try {
            CommentRepo::getInstance()->approve($comment);

            return json_success(trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Mark comment as spam.
     *
     * @param  Comment  $comment
     * @return JsonResponse
     */
    public function spam(Comment $comment): JsonResponse
    {
        try {
            CommentRepo::getInstance()->markAsSpam($comment);

            return json_success(trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
