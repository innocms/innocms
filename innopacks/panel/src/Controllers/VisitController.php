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
use InnoCMS\Common\Models\Visit\Visit;
use InnoCMS\Common\Repositories\VisitRepo;
use InnoCMS\Common\Services\VisitEnrichService;

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
            'visits'        => VisitRepo::getInstance()->builder($filters)
                ->withCount('visitEvents')
                ->orderByDesc('id')
                ->paginate(),
        ];

        return view('panel::visits.index', $data);
    }

    /**
     * Show visit detail with all events.
     *
     * @param  Visit  $visit
     * @return mixed
     */
    public function show(Visit $visit): mixed
    {
        $visit->load(['visitEvents' => function ($query) {
            $query->orderByDesc('id');
        }]);

        return view('panel::visits.show', compact('visit'));
    }

    /**
     * Locate and update a visit record's geo data.
     */
    public function locate(Visit $visit): JsonResponse
    {
        if (! $visit->ip_address) {
            return response()->json(['error' => 'No IP address'], 400);
        }

        try {
            $result = app(VisitEnrichService::class)->locate($visit);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($result);
    }

    /**
     * Parse user_agent and update browser/os for a visit record.
     */
    public function parseUA(Visit $visit): JsonResponse
    {
        if (! $visit->user_agent) {
            return response()->json(['error' => 'No user agent'], 400);
        }

        $result = app(VisitEnrichService::class)->parseUA($visit);

        return response()->json($result);
    }

    /**
     * Batch locate and parse UA for all visits with missing data.
     */
    public function batchLocate(): JsonResponse
    {
        try {
            $result = app(VisitEnrichService::class)->batchLocate();
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json($result);
    }

    private function getSearchFieldOptions(): array
    {
        return [
            ['value' => '', 'label' => trans('panel/common.all_fields')],
            ['value' => 'ip_address', 'label' => trans('panel/visit.ip_address')],
            ['value' => 'country_code', 'label' => trans('panel/visit.country_code')],
        ];
    }

    private function getFilterButtonOptions(): array
    {
        return [
            [
                'name'    => 'device_type',
                'label'   => trans('panel/visit.device_type'),
                'type'    => 'button',
                'options' => [
                    ['value' => '', 'label' => trans('panel/common.all')],
                    ['value' => 'desktop', 'label' => trans('panel/visit.device_desktop')],
                    ['value' => 'mobile', 'label' => trans('panel/visit.device_mobile')],
                    ['value' => 'tablet', 'label' => trans('panel/visit.device_tablet')],
                ],
            ],
        ];
    }
}
