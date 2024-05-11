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
use InnoCMS\Common\Models\Page;
use InnoCMS\Common\Repositories\PageRepo;
use InnoCMS\Panel\Requests\PageRequest;

class PageController extends BaseApiController
{
    /**
     * @param  Request  $request
     * @return mixed
     * @throws \Exception
     */
    public function index(Request $request): mixed
    {
        $filters = $request->all();

        return PageRepo::getInstance()->list($filters);
    }

    /**
     * @param  PageRequest  $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function store(PageRequest $request): JsonResponse
    {
        try {
            $data = $request->all();
            $page = PageRepo::getInstance()->create($data);

            return json_success(trans('panel::common.updated_success'), $page);
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * @param  PageRequest  $request
     * @param  Page  $page
     * @return JsonResponse
     */
    public function update(PageRequest $request, Page $page): JsonResponse
    {
        try {
            $data = $request->all();
            PageRepo::getInstance()->update($page, $data);

            return json_success(trans('panel::common.updated_success'), $page);
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * @param  Page  $page
     * @return JsonResponse
     */
    public function destroy(Page $page): JsonResponse
    {
        try {
            PageRepo::getInstance()->destroy($page);

            return json_success(trans('panel::common.deleted_success'));
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
