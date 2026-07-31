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

        $data['locale'] = $locale;
        $data['themes'] = $this->discoverDemoThemes($locale);

        return view('install::installer.index', $data);
    }

    /**
     * Themes bundled with the release that ship a runnable demo seeder.
     * Each entry exposes code + localized name/description for the installer UI.
     *
     * @return array<int, array{code:string,name:string,description:string}>
     */
    private function discoverDemoThemes(string $locale): array
    {
        $dirs = glob(base_path('themes/*'), GLOB_ONLYDIR) ?: [];
        $out  = [];
        foreach ($dirs as $dir) {
            if (! is_file($dir.'/config.json') || ! is_file($dir.'/demo/Seeder.php')) {
                continue;
            }
            $config = json_decode((string) file_get_contents($dir.'/config.json'), true);
            if (! is_array($config) || empty($config['code']) || empty($config['offer_on_install'])) {
                continue;
            }
            $code  = (string) $config['code'];
            $name  = $config['name'] ?? $code;
            $desc  = $config['description'] ?? '';
            $out[] = [
                'code'        => $code,
                'name'        => is_array($name) ? ($name[$locale] ?? $name['en'] ?? reset($name)) : $name,
                'description' => is_array($desc) ? ($desc[$locale] ?? $desc['en'] ?? reset($desc)) : $desc,
            ];
        }

        return $out;
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
