<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;

class ContentAgent implements Agent
{
    use Promptable;

    public function __construct(
        private readonly string $column = '',
        private readonly string $locale = '',
    ) {}

    public function instructions(): \Stringable|string
    {
        $basePrompt = 'You are an expert content writer for a CMS. Generate high-quality, SEO-optimized content for articles and pages.';

        if ($this->column) {
            $mappings = [
                'article_summary'     => 'ai_prompt_article_summary',
                'article_slug'        => 'ai_prompt_article_slug',
                'article_title'       => 'ai_prompt_article_seo_title',
                'article_description' => 'ai_prompt_article_seo_description',
                'article_keywords'    => 'ai_prompt_article_seo_keywords',
                'page_title'          => 'ai_prompt_page_seo_title',
                'page_description'    => 'ai_prompt_page_seo_description',
                'page_keywords'       => 'ai_prompt_page_seo_keywords',
            ];

            $systemPrompt = system_setting($mappings[$this->column] ?? '') ?: '';
            if ($systemPrompt) {
                $basePrompt .= "\n\n".$systemPrompt;
            }
        }

        if ($this->locale) {
            $basePrompt .= "\n\nRespond in {$this->locale}.";
        }

        return $basePrompt;
    }
}
