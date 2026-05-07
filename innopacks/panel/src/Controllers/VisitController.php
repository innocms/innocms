<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use Illuminate\Http\Request;
use InnoCMS\Common\Repositories\VisitRepo;

class VisitController extends BaseController
{
    /**
     * @param  Request  $request
     * @return mixed
     */
    public function index(Request $request): mixed
    {
        $filters = $request->all();
        $data    = [
            'searchFields'  => $this->getSearchFieldOptions(),
            'filterButtons' => $this->getFilterButtonOptions(),
            'visits'        => VisitRepo::getInstance()->builder($filters)->orderByDesc('id')->paginate(),
        ];

        return view('panel::visits.index', $data);
    }

    private function getSearchFieldOptions(): array
    {
        return [
            ['value' => '', 'label' => trans('panel::common.all_fields')],
            ['value' => 'ip_address', 'label' => trans('panel::visit.ip_address')],
            ['value' => 'country_code', 'label' => trans('panel::visit.country_code')],
        ];
    }

    private function getFilterButtonOptions(): array
    {
        return [
            [
                'name'    => 'device_type',
                'label'   => trans('panel::visit.device_type'),
                'type'    => 'button',
                'options' => [
                    ['value' => '', 'label' => trans('panel::common.all')],
                    ['value' => 'desktop', 'label' => trans('panel::visit.device_desktop')],
                    ['value' => 'mobile', 'label' => trans('panel::visit.device_mobile')],
                    ['value' => 'tablet', 'label' => trans('panel::visit.device_tablet')],
                ],
            ],
        ];
    }
}
