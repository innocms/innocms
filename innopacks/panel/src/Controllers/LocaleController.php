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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use InnoCMS\Common\Models\Locale;
use InnoCMS\Common\Repositories\LocaleRepo;
use InnoCMS\Panel\Requests\LocaleRequest;

class LocaleController extends BaseController
{
    /**
     * @return mixed
     * @throws Exception
     */
    public function index(): mixed
    {
        $data = [
            'locales' => LocaleRepo::getInstance()->getFrontListWithPath(),
        ];

        return view('panel::locales.index', $data);
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function switch(Request $request): RedirectResponse
    {
        $admin      = current_admin();
        $destCode   = $request->code;
        $refererUrl = $request->headers->get('referer');

        $admin->locale = $destCode;
        $admin->save();
        App::setLocale($destCode);

        return redirect()->to($refererUrl);
    }

    /**
     * @param  LocaleRequest  $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function install(Request $request): RedirectResponse
    {
        try {
            $code   = $request->get('code');
            $list   = LocaleRepo::getInstance()->getFrontListWithPath();
            $data   = collect($list)->where('code', $code)->first();
            $locale = LocaleRepo::getInstance()->create($data);

            return redirect(panel_route('locales.index'))
                ->with('instance', $locale)
                ->with('success', trans('panel/common.install_success'));
        } catch (Exception $e) {
            return redirect(panel_route('locales.index'))->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Locale  $locale
     * @return mixed
     */
    public function edit(Locale $locale): mixed
    {
        $data = [
            'locale' => $locale,
        ];

        return view('panel::locales.form', $data);
    }

    /**
     * @param  LocaleRequest  $request
     * @param  Locale  $locale
     * @return RedirectResponse
     */
    public function update(LocaleRequest $request, Locale $locale): RedirectResponse
    {
        try {
            $data = $request->all();
            LocaleRepo::getInstance()->update($locale, $data);

            return back()->with('success', trans('panel/common.updated_success'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function uninstall(Request $request): mixed
    {
        try {
            $code   = $request->code;
            $locale = LocaleRepo::getInstance()->builder(['code' => $code])->firstOrFail();
            if ($locale->code == system_setting('front_locale')) {
                throw new Exception(trans('panel/locale.cannot_uninstall_default_locale'));
            }
            LocaleRepo::getInstance()->destroy($locale);

            if (session('locale') == $code) {
                session()->forget('locale');
            }

            return json_success(trans('panel/common.updated_success'));
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
