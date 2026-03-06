<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Request;
use InnoCMS\Common\Models\Visit;
use InnoCMS\Common\Repositories\BaseRepo;

class AnalyticsRepo extends BaseRepo
{
    /**
     * Get dashboard data
     *
     * @param  Request  $request
     * @return array
     */
    public function getDashboardData(Request $request): array
    {
        $period = $request->get('period', '7days');
        $dates  = $this->getPeriodDates($period);

        return [
            'period'        => $period,
            'total_visits'  => $this->getTotalVisits($dates),
            'unique_visits' => $this->getUniqueVisits($dates),
            'top_countries' => $this->getTopCountries($dates),
            'top_devices'   => $this->getTopDevices($dates),
            'top_browsers'  => $this->getTopBrowsers($dates),
            'daily_visits'  => $this->getDailyVisits($dates),
        ];
    }

    /**
     * Get chart data
     *
     * @param  string  $period
     * @return array
     */
    public function getChartData(string $period): array
    {
        $dates = $this->getPeriodDates($period);

        return [
            'daily_visits' => $this->getDailyVisits($dates),
        ];
    }

    /**
     * Get period dates
     *
     * @param  string  $period
     * @return array
     */
    private function getPeriodDates(string $period): array
    {
        return match ($period) {
            '30days' => [
                'start' => Carbon::now()->subDays(30)->startOfDay(),
                'end'   => Carbon::now()->endOfDay(),
            ],
            '90days' => [
                'start' => Carbon::now()->subDays(90)->startOfDay(),
                'end'   => Carbon::now()->endOfDay(),
            ],
            default => [
                'start' => Carbon::now()->subDays(7)->startOfDay(),
                'end'   => Carbon::now()->endOfDay(),
            ],
        };
    }

    /**
     * Get total visits
     *
     * @param  array  $dates
     * @return int
     */
    private function getTotalVisits(array $dates): int
    {
        return Visit::whereBetween('first_visited_at', [$dates['start'], $dates['end']])->count();
    }

    /**
     * Get unique visits
     *
     * @param  array  $dates
     * @return int
     */
    private function getUniqueVisits(array $dates): int
    {
        return Visit::whereBetween('first_visited_at', [$dates['start'], $dates['end']])
            ->distinct('ip_address')
            ->count('ip_address');
    }

    /**
     * Get top countries
     *
     * @param  array  $dates
     * @return array
     */
    private function getTopCountries(array $dates): array
    {
        return Visit::whereBetween('first_visited_at', [$dates['start'], $dates['end']])
            ->whereNotNull('country_name')
            ->selectRaw('country_name, count(*) as count')
            ->groupBy('country_name')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * Get top devices
     *
     * @param  array  $dates
     * @return array
     */
    private function getTopDevices(array $dates): array
    {
        return Visit::whereBetween('first_visited_at', [$dates['start'], $dates['end']])
            ->selectRaw('device_type, count(*) as count')
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->get()
            ->toArray();
    }

    /**
     * Get top browsers
     *
     * @param  array  $dates
     * @return array
     */
    private function getTopBrowsers(array $dates): array
    {
        return Visit::whereBetween('first_visited_at', [$dates['start'], $dates['end']])
            ->whereNotNull('browser')
            ->selectRaw('browser, count(*) as count')
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->toArray();
    }

    /**
     * Get daily visits
     *
     * @param  array  $dates
     * @return array
     */
    private function getDailyVisits(array $dates): array
    {
        return Visit::whereBetween('first_visited_at', [$dates['start'], $dates['end']])
            ->selectRaw('DATE(first_visited_at) as date, count(*) as visits')
            ->groupByRaw('DATE(first_visited_at)')
            ->orderBy('date')
            ->get()
            ->toArray();
    }
}
