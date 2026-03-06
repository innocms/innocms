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
use InnoCMS\Common\Models\Visit;
use InnoCMS\Panel\Repositories\AnalyticsRepo;

class AnalyticsController extends BaseController
{
    /**
     * Analytics dashboard
     *
     * @param  Request  $request
     * @return mixed
     */
    public function index(Request $request): mixed
    {
        $data = AnalyticsRepo::getInstance()->getDashboardData($request);

        return view('panel::analytics.index', $data);
    }

    /**
     * Get analytics data
     *
     * @param  Request  $request
     * @return mixed
     */
    public function data(Request $request): mixed
    {
        $period = $request->get('period', '7days');
        $data   = AnalyticsRepo::getInstance()->getChartData($period);

        return json_success('Success', $data);
    }
}
