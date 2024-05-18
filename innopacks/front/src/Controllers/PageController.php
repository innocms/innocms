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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use InnoCMS\Common\Repositories\PageRepo;

class PageController extends Controller
{
    /**
     * @param  Request  $request
     * @return mixed
     * @throws \Exception
     */
    public function show(Request $request): mixed
    {
        $slug = $request->slug;
        $page = PageRepo::getInstance()->builder(['active' => true])->where('slug', $slug)->firstOrFail();
        $page->increment('viewed');
        $data = [
            'slug' => $slug,
            'page' => $page,
        ];

        $template = $page->translation->template ?? '';
        if ($template) {
            $result         = Blade::render($template, $data);
            $data['result'] = $result;
        }

        return view('front::pages.show', $data);
    }

    /**
     * @param  Request  $request
     * @return mixed
     * @throws \Exception
     */
    public function showOfficialDemoPage(Request $request): mixed
    {
        $slug = $request->slug;
        switch ($slug){
            case 'services':
                return view('front::official_static_pages.services',);
                break;
            case 'about':
                return view('front::official_static_pages.about');
                break;
            case 'productions':
                return view('front::official_static_pages.productions');
                break;
            default:
                return redirect()->route('home.index');
        }
    }
}
