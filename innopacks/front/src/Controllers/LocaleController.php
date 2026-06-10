<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Front\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller
{
    /**
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function switch(Request $request): RedirectResponse
    {
        $currentCode = App::getLocale();
        $destCode    = $request->code;
        $refererUrl  = $request->headers->get('referer');
        $baseUrl     = url('/').'/';

        $newUrl = str_replace($baseUrl.$currentCode, $baseUrl.$destCode, $refererUrl);
        App::setLocale($destCode);
        session(['locale' => $destCode]);

        return redirect()->to($newUrl);
    }
}
