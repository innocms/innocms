<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Controllers;

use Illuminate\Http\JsonResponse;
use InnoCMS\Common\Repositories\ConsultationRepo;
use InnoCMS\Front\Requests\ConsultationRequest;

class ConsultationController
{
    /**
     * Submit a consultation.
     */
    public function store(ConsultationRequest $request): JsonResponse
    {
        try {
            ConsultationRepo::getInstance()->create($request->only(['name', 'email', 'phone', 'company', 'content']));

            return json_success('提交成功，我们将尽快联系您');
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
