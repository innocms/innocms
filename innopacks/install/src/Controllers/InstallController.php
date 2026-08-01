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
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use InnoCMS\Install\Libraries\Checker;
use InnoCMS\Install\Libraries\Creator;
use InnoCMS\Install\Requests\CompleteRequest;
use Throwable;

class InstallController extends Controller
{
    /**
     * @param  Request  $request
     * @return mixed
     * @throws Exception
     */
    public function index(Request $request): mixed
    {
        if (installed()) {
            return redirect(front_route('home.index'));
        }

        $defaultLocale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
        $defaultLocale = ($defaultLocale == 'zh' ? 'zh-cn' : $defaultLocale);
        $locale        = $request->get('locale', $defaultLocale);
        App::setLocale($locale);

        $data = Checker::getInstance()->getEnvironment();

        // The installer's DB driver defaults to MySQL (see #db-driver in the
        // view), so the first environment render must hide the SQLite-only
        // extensions — otherwise a missing pdo_sqlite would show a red cross
        // and wrongly block the "next" button. Mirrors driverDetect() below.
        unset($data['extensions']['pdo_sqlite'], $data['extensions']['sqlite3']);

        $data['locale'] = $locale;

        return view('install::installer.index', $data);
    }

    /**
     * Re-render the environment/permission table scoped to the chosen DB driver,
     * so only the extensions that driver actually needs are checked.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function driverDetect(Request $request): mixed
    {
        $data   = Checker::getInstance()->getEnvironment();
        $locale = $request->get('locale', app()->getLocale());
        App::setLocale($locale);

        $dbCode = $request->get('db_code');
        if ($dbCode == 'mysql') {
            unset($data['extensions']['pdo_sqlite']);
            unset($data['extensions']['sqlite3']);
        } elseif ($dbCode == 'sqlite') {
            unset($data['extensions']['pdo_mysql']);
        }

        return view('install::installer._env_check', $data);
    }

    /**
     * @param  Request  $request
     * @return array
     */
    public function checkConnected(Request $request): array
    {
        return (new Checker)->checkConnection($request->all());
    }

    /**
     * @param  CompleteRequest  $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function complete(CompleteRequest $request): JsonResponse
    {
        try {
            $data      = $request->all();
            $outputLog = Creator::getInstance()->setup($data)->getOutputLog();

            return json_success($outputLog->fetch());
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
