<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use Exception;
use InnoCMS\Panel\Repositories\Dashboard\ArticleRepo;
use InnoCMS\Panel\Repositories\DashboardRepo;

class DashboardController extends BaseController
{
    /**
     * Dashboard for panel home page.
     *
     * @return mixed
     * @throws Exception
     */
    public function index(): mixed
    {
        $dashboard = DashboardRepo::getInstance();
        $data      = [
            'cards'   => $dashboard->getCards(),
            'article' => [
                'latest_week' => $dashboard->getArticleTotalLatestWeek(),
            ],
            'top_viewed_articles' => ArticleRepo::getInstance()->getTopViewedArticles(),
        ];

        return view('panel::dashboard', $data);
    }
}
