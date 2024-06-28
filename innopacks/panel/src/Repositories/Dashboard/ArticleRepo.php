<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Repositories\Dashboard;

use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Support\Facades\DB;
use InnoCMS\Common\Repositories\ArticleRepo as CommonArticleRepo;
use InnoCMS\Panel\Repositories\BaseRepo;

class ArticleRepo extends BaseRepo
{
    /**
     * @return array
     * @throws Exception
     */
    public function getTopViewedArticles(): array
    {
        $articles = CommonArticleRepo::getInstance()->getTopViewedArticles();

        $items = [];
        foreach ($articles as $item) {
            if (empty($item->viewed)) {
                continue;
            }

            $name    = $item->translation->title;
            $items[] = [
                'id'      => $item->id,
                'image'   => image_resize($item->image),
                'name'    => $name,
                'summary' => sub_string($name, 50),
                'viewed'  => $item->viewed,
            ];
        }

        return $items;
    }

    /**
     * Retrieve the number of new articles added each day in the past week.
     *
     * @return array
     */
    public function getArticleTotalLatestWeek(): array
    {
        $filters = [
            'start' => today()->subWeek(),
            'end'   => today(),
        ];
        $articleTotals = CommonArticleRepo::getInstance()->builder($filters)
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
     * Retrieve the 7 articles with the highest views.
     *
     * @return array
     */
    public function getArticleViewedLatestWeek(): array
    {
        $articles = CommonArticleRepo::getInstance()->getTopViewedArticles(5);

        $names = $viewed = [];
        foreach ($articles as $article) {
            $names[]  = sub_string($article->translation->title, 8);
            $viewed[] = $article->viewed;
        }

        return [
            'period' => $names,
            'totals' => $viewed,
        ];
    }
}
