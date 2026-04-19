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
use InnoCMS\Common\Repositories\CatalogRepo;
use InnoCMS\Common\Repositories\PageRepo;
use InnoCMS\Common\Repositories\SettingRepo;
use InnoCMS\Panel\Repositories\ThemeRepo;
use InnoCMS\Panel\Services\ThemeDemoService;
use InnoCMS\Panel\Services\ThemeService;

class ThemeController extends BaseController
{
    public function __construct(
        protected ThemeService $themeService,
        protected ThemeDemoService $themeDemoService
    ) {}

    /**
     * @return mixed
     */
    public function index(): mixed
    {
        $result   = $this->themeService->getListFromPath();
        $themes   = $result['themes'];
        $selected = $themes->firstWhere('selected', true);

        $data = [
            'themes'                 => $themes,
            'themes_count'           => $themes->count(),
            'themes_with_demo_count' => $themes->where('has_demo', true)->count(),
            'selected_theme_name'    => data_get($selected, 'name'),
            'errors'                 => $result['errors'],
        ];

        return view('panel::themes.index', $data);
    }

    /**
     * @return mixed
     */
    public function settings(): mixed
    {
        $data = [
            'catalogs' => CatalogRepo::getInstance()->getTopCatalogs(),
            'pages'    => PageRepo::getInstance()->withActive()->builder()->get(),
        ];

        return view('panel::themes.settings', $data);
    }

    /**
     * @param  Request  $request
     * @return mixed
     * @throws \Throwable
     */
    public function updateSettings(Request $request): mixed
    {
        $settings   = $request->all();
        $settingUrl = panel_route('themes_settings.index');

        try {
            SettingRepo::getInstance()->updateValues($settings);

            return redirect($settingUrl)->with('success', trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return redirect($settingUrl)->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Toggle active theme (body { "status": 1|0 } — same as InnoShop Factory list_switch).
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function enable(Request $request, string $code): JsonResponse
    {
        try {
            $status = $request->input('status');
            if (empty($status)) {
                SettingRepo::getInstance()->updateSystemValue('theme', '');
            } else {
                SettingRepo::getInstance()->updateSystemValue('theme', $code);
            }

            return json_success(trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Import theme demo data from themes/{code}/demo/Seeder.php (and optional images).
     *
     * Body JSON: { "clear_default_catalog": 0|1 } clears CMS articles/catalogs/pages/tags before import when truthy.
     *
     * @throws \Throwable
     */
    public function importDemo(Request $request, string $code): JsonResponse
    {
        try {
            $dir = ThemeRepo::getInstance()->getThemeDirectory($code);
            if ($dir === null || ! is_dir($dir)) {
                return json_fail(trans('panel::themes.error_theme_directory'));
            }

            $config = ThemeRepo::getInstance()->readConfig($dir);
            if (($config['code'] ?? '') !== $code) {
                return json_fail(trans('panel::themes.error_code_mismatch', [
                    'folder' => basename($dir),
                    'code'   => $config['code'] ?? '',
                ]));
            }

            if (! $this->themeDemoService->hasDemo($dir)) {
                return json_fail(trans('panel::themes.error_demo_not_found'));
            }

            $clear = (bool) $request->input('clear_default_catalog', false);

            $this->themeDemoService->importDemo($code, $dir, $clear);

            return json_success(trans('panel::themes.demo_installed'));
        } catch (\Throwable $e) {
            return json_fail($e->getMessage());
        }
    }
}
