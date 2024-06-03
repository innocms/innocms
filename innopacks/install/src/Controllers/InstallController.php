<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Install\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use InnoCMS\Install\Libraries\Checker;
use InnoCMS\Install\Libraries\Creator;

class InstallController extends Controller
{
    /**
     * @param  Request  $request
     * @return mixed
     */
    public function index(Request $request): mixed
    {
        if (installed()) {
            return redirect(front_route('home.index'));
        }

        $defaultLocale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $defaultLocale = ($defaultLocale == 'zh' ? 'zh_cn' : $defaultLocale);
        App::setLocale($request->get('locale', $defaultLocale));

        $data = (new Checker())->checkEnvironment();

        return view('install::installer.index', $data);
    }

    /**
     * @param  Request  $request
     * @return array
     */
    public function checkConnected(Request $request): array
    {
        return (new Checker())->checkConnection($request->all());
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function complete(Request $request): JsonResponse
    {
        try {
            $data      = $request->all();
            $outputLog = (new Creator())->setup($data)->getOutputLog();

            return json_success($outputLog->fetch());
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
