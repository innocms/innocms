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
use InnoCMS\Common\Services\GeoLocationService;

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

    private function getSearchFieldOptions(): array
    {
        return [
            ['value' => '', 'label' => trans('panel/common.all_fields')],
            ['value' => 'ip_address', 'label' => trans('panel/visit.ip_address')],
            ['value' => 'country_code', 'label' => trans('panel/visit.country_code')],
        ];
    }

    /**
     * Locate and update a visit record's geo data.
     */
    public function locate(Visit $visit): JsonResponse
    {
        if (! $visit->ip_address) {
            return response()->json(['error' => 'No IP address'], 400);
        }

        $service = new GeoLocationService;
        $result  = $service->getLocation($visit->ip_address);

        $visit->update([
            'country_code' => $result['country_code'] ?? '',
            'country_name' => $result['country_name'] ?? '',
            'city'         => $result['city'] ?? '',
        ]);

        return response()->json([
            'success'      => true,
            'country_name' => $result['country_name'] ?? '',
            'city'         => $result['city'] ?? '',
        ]);
    }

    /**
     * Parse user_agent and update browser/os for a visit record.
     */
    public function parseUA(Visit $visit): JsonResponse
    {
        if (! $visit->user_agent) {
            return response()->json(['error' => 'No user agent'], 400);
        }

        $browser = $this->detectBrowser($visit->user_agent);
        $os      = $this->detectOS($visit->user_agent);

        $visit->update([
            'browser' => $browser,
            'os'      => $os,
        ]);

        return response()->json([
            'success' => true,
            'browser' => $browser,
            'os'      => $os,
        ]);
    }

    /**
     * Batch locate and parse UA for all visits with missing data.
     */
    public function batchLocate(): JsonResponse
    {
        $geoService = new GeoLocationService;

        $visits = Visit::where(function ($q) {
            $q->whereNull('country_name')
                ->orWhere('country_name', '')
                ->orWhereNull('city')
                ->orWhere('city', '')
                ->orWhereNull('browser')
                ->orWhere('browser', '')
                ->orWhereNull('os')
                ->orWhere('os', '');
        })
            ->limit(500)
            ->get();

        $updated = 0;

        foreach ($visits as $visit) {
            $fields = [];

            // Geo lookup (local + remote fallback via hook)
            if ($visit->ip_address && (empty($visit->country_name) || empty($visit->city))) {
                $result = $geoService->getLocation($visit->ip_address);
                if (! empty($result['country_name']) || ! empty($result['city'])) {
                    $fields['country_code'] = $result['country_code'] ?? '';
                    $fields['country_name'] = $result['country_name'] ?? '';
                    $fields['city']         = $result['city'] ?? '';
                }
            }

            // UA parsing
            if ($visit->user_agent && (empty($visit->browser) || empty($visit->os))) {
                $fields['browser'] = $this->detectBrowser($visit->user_agent);
                $fields['os']      = $this->detectOS($visit->user_agent);
            }

            if (! empty($fields)) {
                $visit->update($fields);
                $updated++;
            }
        }

        return response()->json([
            'success' => true,
            'updated' => $updated,
        ]);
    }

    private function detectBrowser(string $ua): string
    {
        $patterns = [
            'Edg/'            => 'Edge',
            'OPR/'            => 'Opera',
            'Opera'           => 'Opera',
            'Vivaldi/'        => 'Vivaldi',
            'Brave/'          => 'Brave',
            'SamsungBrowser/' => 'Samsung Browser',
            'UCBrowser/'      => 'UC Browser',
            'MicroMessenger/' => 'WeChat',
            'QQBrowser/'      => 'QQ Browser',
            'Firefox/'        => 'Firefox',
            'FxiOS/'          => 'Firefox',
            'Chrome/'         => 'Chrome',
            'CriOS/'          => 'Chrome',
            'Safari/'         => 'Safari',
            'MSIE'            => 'IE',
            'Trident/'        => 'IE',
        ];

        foreach ($patterns as $pattern => $name) {
            if (str_contains($ua, $pattern)) {
                return $name;
            }
        }

        return '';
    }

    private function detectOS(string $ua): string
    {
        $patterns = [
            'HarmonyOS'     => 'HarmonyOS',
            'Android'       => 'Android',
            'iPhone'        => 'iOS',
            'iPad'          => 'iPadOS',
            'iPod'          => 'iOS',
            'Windows Phone' => 'Windows Phone',
            'Windows NT'    => 'Windows',
            'Mac OS X'      => 'macOS',
            'Macintosh'     => 'macOS',
            'Linux'         => 'Linux',
            'CrOS'          => 'Chrome OS',
        ];

        foreach ($patterns as $pattern => $name) {
            if (str_contains($ua, $pattern)) {
                return $name;
            }
        }

        return '';
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
