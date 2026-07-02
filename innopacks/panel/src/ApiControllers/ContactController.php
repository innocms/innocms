<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\ApiControllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InnoCMS\Common\Models\Contact;
use InnoCMS\Common\Repositories\ContactRepo;

class ContactController extends BaseApiController
{
    /**
     * @param  Request  $request
     * @return mixed
     */
    public function index(Request $request): mixed
    {
        $filters = $request->all();

        return ContactRepo::getInstance()->list($filters);
    }

    /**
     * Mark a contact as read.
     *
     * @param  Contact  $contact
     * @return JsonResponse
     */
    public function markRead(Contact $contact): JsonResponse
    {
        try {
            ContactRepo::getInstance()->update($contact, ['status' => true]);

            return json_success(trans('panel/common.updated_success'));
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Mark all contacts as read.
     *
     * @return JsonResponse
     */
    public function markAllRead(): JsonResponse
    {
        try {
            ContactRepo::getInstance()->markAllAsRead();

            return json_success(trans('panel/common.updated_success'));
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * @param  Contact  $contact
     * @return JsonResponse
     */
    public function destroy(Contact $contact): JsonResponse
    {
        try {
            ContactRepo::getInstance()->destroy($contact);

            return json_success(trans('panel/common.deleted_success'));
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
