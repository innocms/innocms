<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InnoCMS\Common\Models\Article;
use InnoCMS\Common\Repositories\ArticleRepo;
use InnoCMS\Common\Repositories\CatalogRepo;
use InnoCMS\Common\Resources\CatalogSimple;
use InnoCMS\Panel\Requests\ArticleRequest;

class ArticleController extends BaseController
{
    /**
     * @param  Request  $request
     * @return mixed
     * @throws \Exception
     */
    public function index(Request $request): mixed
    {
        $filters = $request->all();
        $data    = [
            'articles' => ArticleRepo::getInstance()->list($filters),
        ];

        return view('panel::articles.index', $data);
    }

    /**
     * Article creation page.
     *
     * @return mixed
     * @throws \Exception
     */
    public function create(): mixed
    {
        return $this->form(new Article);
    }

    /**
     * @param  ArticleRequest  $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function store(ArticleRequest $request): RedirectResponse
    {
        try {
            $data = $request->all();
            ArticleRepo::getInstance()->create($data);

            return redirect(panel_route('articles.index'))->with('success', trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return redirect(panel_route('articles.create'))->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Article  $article
     * @return mixed
     * @throws \Exception
     */
    public function edit(Article $article): mixed
    {
        return $this->form($article);
    }

    /**
     * @param  $article
     * @return mixed
     * @throws \Exception
     */
    public function form($article): mixed
    {
        $catalogs = CatalogSimple::collection(CatalogRepo::getInstance()->all(['active' => 1]))->jsonSerialize();
        $data     = [
            'article'  => $article,
            'catalogs' => $catalogs,
        ];

        return view('panel::articles.form', $data);
    }

    /**
     * @param  ArticleRequest  $request
     * @param  Article  $article
     * @return RedirectResponse
     */
    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        try {
            $data = $request->all();
            ArticleRepo::getInstance()->update($article, $data);

            return redirect(panel_route('articles.index'))
                ->with('success', trans('panel::common.updated_success'));
        } catch (\Exception $e) {
            return redirect(panel_route('articles.edit', $article))
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param  Article  $article
     * @return RedirectResponse
     */
    public function destroy(Article $article): RedirectResponse
    {
        try {
            ArticleRepo::getInstance()->destroy($article);

            return back()->with('success', trans('panel::common.deleted_success'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
