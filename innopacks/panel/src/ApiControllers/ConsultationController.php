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
use InnoCMS\Common\Models\Consultation;
use InnoCMS\Common\Repositories\ConsultationRepo;

class ConsultationController extends BaseApiController
{
    /**
     * @param  Request  $request
     * @return mixed
     */
    public function index(Request $request): mixed
    {
        $filters = $request->all();

        return ConsultationRepo::getInstance()->list($filters);
    }

    /**
     * Mark a consultation as read.
     *
     * @param  Consultation  $consultation
     * @return JsonResponse
     */
    public function markRead(Consultation $consultation): JsonResponse
    {
        try {
            ConsultationRepo::getInstance()->update($consultation, ['status' => true]);

            return json_success(trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Mark all consultations as read.
     *
     * @return JsonResponse
     */
    public function markAllRead(): JsonResponse
    {
        try {
            ConsultationRepo::getInstance()->markAllAsRead();

            return json_success(trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * @param  Consultation  $consultation
     * @return JsonResponse
     */
    public function destroy(Consultation $consultation): JsonResponse
    {
        try {
            ConsultationRepo::getInstance()->destroy($consultation);

            return json_success(trans('panel::common.deleted_success'));
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
