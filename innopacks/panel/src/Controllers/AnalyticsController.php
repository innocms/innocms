<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use InnoCMS\Common\Models\Visit\VisitEvent;
use InnoCMS\Common\Repositories\VisitRepo;
use InnoCMS\Common\Services\VisitStatisticsService;

class AnalyticsController extends BaseController
{
    /**
     * Analytics overview page.
     */
    public function index(Request $request): mixed
    {
        $dateFilter = $request->get('date_filter', 'last_30_days');
        $startDate  = $request->get('start_date', '');
        $endDate    = $request->get('end_date', '');

        $dateRange = $this->getDateRange($dateFilter, $startDate, $endDate);
        $filters   = [
            'start_date' => $dateRange['start']->toDateString(),
            'end_date'   => $dateRange['end']->toDateString(),
        ];

        $visitRepo = VisitRepo::getInstance();

        // Summary stats
        $statistics = $visitRepo->getStatistics($filters);

        // Daily trend
        $dailyStats = $visitRepo->getDailyStatistics($filters);

        // Device breakdown
        $deviceData = $visitRepo->getVisitsByDeviceType($filters);

        // Country distribution
        $countryData = $visitRepo->getVisitsByCountry($filters);

        // Browser distribution
        $browserData = $visitRepo->getVisitsByBrowser($filters);

        // OS distribution
        $osData = $visitRepo->getVisitsByOS($filters);

        $data = [
            'dateFilter'  => $dateFilter,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'statistics'  => $statistics,
            'dailyStats'  => $dailyStats,
            'deviceData'  => $deviceData,
            'browserData' => $browserData,
            'osData'      => $osData,
            'countryData' => $countryData,
        ];

        return view('panel::analytics.index', $data);
    }

    /**
     * Re-aggregate all visit statistics.
     */
    public function reaggregate(): mixed
    {
        $earliest = VisitEvent::orderBy('created_at')->value('created_at');

        if ($earliest) {
            $start = Carbon::parse($earliest)->startOfDay();
            $end   = Carbon::today();

            $service = new VisitStatisticsService;
            $service->aggregateRange($start, $end);
        }

        return response()->json([
            'success' => true,
            'message' => __('panel/analytics.reaggregate_success'),
        ]);
    }

    /**
     * Calculate date range from filter preset or custom dates.
     */
    private function getDateRange(string $filter, string $start, string $end): array
    {
        return match ($filter) {
            'today'        => ['start' => Carbon::today(), 'end' => Carbon::today()],
            'yesterday'    => ['start' => Carbon::yesterday(), 'end' => Carbon::yesterday()],
            'this_week'    => ['start' => Carbon::now()->startOfWeek(), 'end' => Carbon::now()->endOfWeek()],
            'this_month'   => ['start' => Carbon::now()->startOfMonth(), 'end' => Carbon::now()->endOfMonth()],
            'last_7_days'  => ['start' => Carbon::now()->subDays(6), 'end' => Carbon::now()],
            'last_30_days' => ['start' => Carbon::now()->subDays(29), 'end' => Carbon::now()],
            'custom'       => [
                'start' => $start ? Carbon::parse($start) : Carbon::now()->subDays(29),
                'end'   => $end ? Carbon::parse($end) : Carbon::now(),
            ],
            default => ['start' => Carbon::now()->subDays(29), 'end' => Carbon::now()],
        };
    }
}
