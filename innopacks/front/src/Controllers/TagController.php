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
use InnoCMS\Common\Repositories\ArticleRepo;
use InnoCMS\Common\Repositories\TagRepo;

class TagController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        return redirect()->to(route('articles.index'));
    }

    /**
     * @param  Request  $request
     * @return mixed
     * @throws \Exception
     */
    public function show(Request $request): mixed
    {
        $slug     = $request->slug;
        $tag      = TagRepo::getInstance()->builder(['active' => true])->where('slug', $slug)->firstOrFail();
        $tags     = TagRepo::getInstance()->list(['active' => true]);
        $articles = ArticleRepo::getInstance()->list(['active' => true, 'tag_id' => $tag->id]);

        $data = [
            'slug'     => $slug,
            'tag'      => $tag,
            'tags'     => $tags,
            'articles' => $articles,
        ];

        return view('tags.show', $data);
    }
}
