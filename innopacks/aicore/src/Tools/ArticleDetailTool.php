<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Aicore\Tools;

use InnoCMS\Common\Models\Article;
use InvalidArgumentException;

class ArticleDetailTool extends BaseTool
{
    public function name(): string
    {
        return 'article_detail';
    }

    public function description(): string
    {
        return 'Get full details of a single blog article by ID, including content, SEO metadata, and tags.';
    }

    public function inputSchema(): array
    {
        return [
            'type'       => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'Article ID'],
            ],
            'required' => ['id'],
        ];
    }

    public function requiredPermission(): ?string
    {
        return 'articles_index';
    }

    public function execute(array $arguments): mixed
    {
        $article = Article::query()->with([
            'translation',
            'catalog.translation',
            'tags',
        ])->find((int) ($arguments['id'] ?? 0));

        if (! $article) {
            throw new InvalidArgumentException("Article [{$arguments['id']}] not found.");
        }

        $t = $article->translation;

        return [
            'id'               => $article->id,
            'title'            => $t->title ?? '',
            'slug'             => $article->slug,
            'summary'          => $t->summary ?? '',
            'content'          => $t->content ?? '',
            'catalog_id'       => $article->catalog_id,
            'catalog_name'     => $article->catalog->translation->title ?? '',
            'image'            => $article->image,
            'author'           => $article->author,
            'active'           => (bool) $article->active,
            'viewed'           => $article->viewed,
            'meta_title'       => $t->meta_title ?? '',
            'meta_description' => $t->meta_description ?? '',
            'meta_keywords'    => $t->meta_keywords ?? '',
            'tags'             => $article->tags->map(fn ($tag) => $tag->name)->values()->all(),
            'created_at'       => (string) $article->created_at,
            'updated_at'       => (string) $article->updated_at,
        ];
    }
}
