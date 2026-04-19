<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Repositories;

use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use InnoCMS\Common\Models\Article;
use InnoCMS\Common\Repositories\ArticleRepo;
use InnoCMS\Common\Repositories\VisitRepo;

class DashboardRepo
{
    /**
     * Get card instance.
     *
     * @return DashboardRepo
     */
    public static function getInstance(): DashboardRepo
    {
        return new self;
    }

    /**
     * @return array[]
     */
    public function getCards(): array
    {
        $visitStats = $this->getTodayVisitStats();

        return [
            [
                'title'    => '今日 PV',
                'icon'     => 'bi bi-bar-chart-line',
                'quantity' => $visitStats['pv'],
            ],
            [
                'title'    => '今日 UV',
                'icon'     => 'bi bi-people',
                'quantity' => $visitStats['uv'],
            ],
            [
                'title'    => '今日 IP',
                'icon'     => 'bi bi-globe',
                'quantity' => $visitStats['ip'],
            ],
            [
                'title'    => '文章数量',
                'icon'     => 'bi bi-file-earmark-text',
                'quantity' => Article::query()->count(),
            ],
        ];
    }

    /**
     * 获取今日 PV/UV/IP 统计
     */
    private function getTodayVisitStats(): array
    {
        try {
            $stats = VisitRepo::getInstance()->getStatistics([
                'start_date' => today()->toDateString(),
                'end_date'   => today()->toDateString(),
            ]);

            return [
                'pv' => $stats['total_visits'] ?? 0,
                'uv' => $stats['unique_sessions'] ?? 0,
                'ip' => $stats['unique_visitors'] ?? 0,
            ];
        } catch (\Exception $e) {
            return ['pv' => 0, 'uv' => 0, 'ip' => 0];
        }
    }

    /**
     * 获取最近30天的 PV/UV/IP 趋势数据
     */
    public function getVisitTrendLatestMonth(): array
    {
        try {
            $dailyStats = VisitRepo::getInstance()->getDailyStatistics([
                'start_date' => today()->subDays(29)->toDateString(),
                'end_date'   => today()->toDateString(),
            ]);

            $dates = $pvData = $uvData = [];
            foreach ($dailyStats as $stat) {
                $dates[]  = $stat['date'];
                $pvData[] = $stat['page_views'];
                $uvData[] = $stat['unique_visitors'];
            }

            return [
                'period' => $dates,
                'pv'     => $pvData,
                'uv'     => $uvData,
            ];
        } catch (\Exception $e) {
            return ['period' => [], 'pv' => [], 'uv' => []];
        }
    }

    /**
     * 获取最近一周每日新增文章数量
     */
    public function getArticleTotalLatestWeek(): array
    {
        $filters = [
            'start' => today()->subWeek(),
            'end'   => today(),
        ];
        $articleTotals = ArticleRepo::getInstance()->builder($filters)
            ->select(DB::raw('DATE(created_at) as date, count(*) as total'))
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $dates  = $totals = [];
        $period = CarbonPeriod::create(today()->subWeek(), today()->subDay())->toArray();
        foreach ($period as $date) {
            $dateFormat   = $date->format('Y-m-d');
            $articleTotal = $articleTotals[$dateFormat] ?? null;

            $dates[]  = $dateFormat;
            $totals[] = $articleTotal ? $articleTotal->total : 0;
        }

        return [
            'period' => $dates,
            'totals' => $totals,
        ];
    }

    /**
     * 统计 public/static/media 目录下的文件数量
     */
    private function getMediaFileCount(): int
    {
        $path = public_path('static/media');
        if (! is_dir($path)) {
            return 0;
        }

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::FILES
            );

            return iterator_count($iterator);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * 获取浏览量最高的7篇文章
     *
     * @return array
     */
    public function getArticleViewedLatestWeek(): array
    {
        $topArticleArticles = ArticleRepo::getInstance()->builder()->orderByDesc('viewed')->limit(5)->get();
        $names              = $viewed = [];
        foreach ($topArticleArticles as $article) {
            $names[]  = sub_string($article->translation->title, 8);
            $viewed[] = $article->viewed;
        }

        return [
            'period' => $names,
            'totals' => $viewed,
        ];
    }
}
