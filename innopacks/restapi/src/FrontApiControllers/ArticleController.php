<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Restapi\FrontApiControllers;

use Exception;
use Illuminate\Http\Request;
use InnoCMS\Common\Models\Article;
use InnoCMS\Common\Repositories\ArticleRepo;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Unauthenticated;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('Front - Articles')]
class ArticleController extends BaseController
{
    /**
     * Maximum number of articles one request may ask for.
     */
    private const MAX_PER_PAGE = 100;

    /**
     * List the published articles.
     *
     * @param  Request  $request
     * @return mixed
     * @throws Exception
     */
    #[Endpoint('List articles')]
    #[Unauthenticated]
    #[QueryParam('keyword', 'string', required: false)]
    #[QueryParam('catalog_id', 'integer', required: false)]
    #[QueryParam('tag_id', 'integer', required: false)]
    #[QueryParam('per_page', 'integer', required: false, example: 15)]
    public function index(Request $request): mixed
    {
        $filters = $request->all();

        // The front end only ever sees published articles.
        $filters['active'] = true;

        $perPage  = min((int) $request->get('per_page', 15), self::MAX_PER_PAGE);
        $articles = ArticleRepo::getInstance()->builder($filters)->paginate($perPage);

        return read_json_success($articles);
    }

    /**
     * Get one published article.
     *
     * @param  Article  $article
     * @return mixed
     * @throws Exception
     */
    #[Endpoint('Get article detail')]
    #[Unauthenticated]
    #[UrlParam('article', 'integer', description: 'Article ID')]
    public function show(Article $article): mixed
    {
        abort_unless((bool) $article->active, 404);

        $article->load(['translations', 'catalog.translations', 'tags.translations']);

        return read_json_success($article);
    }
}
