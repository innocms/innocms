<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Services\AI;

/**
 * Trait for handling system prompts in AI services
 */
trait HasSystemPrompt
{
    /**
     * Handle prompt with system settings
     *
     * @param  string  $prompt  Original prompt
     * @param  array  $options  Options array containing column and language info
     * @return string Processed prompt
     */
    protected function handlePrompt(string $prompt, array $options = []): string
    {
        if (! isset($options['column'])) {
            return $prompt;
        }

        return $this->getSystemPrompt($prompt, $options['column'], $options['lang'] ?? '');
    }

    /**
     * Get system prompt for specific column type
     *
     * @param  string  $origin  Original content
     * @param  string  $column  Column type
     * @param  string  $locale  Language locale
     * @return string Combined prompt
     */
    protected function getSystemPrompt(string $origin, string $column, string $locale = ''): string
    {
        $mappings  = $this->getPromptMapping();
        $promptKey = $mappings[$column] ?? '';

        if (empty($promptKey)) {
            return $origin;
        }

        $systemPrompt = system_setting($promptKey);
        if (empty($systemPrompt)) {
            return $origin;
        }

        $suffix = '';
        if ($locale) {
            $suffix = "并用 $locale 返回";
        }

        return $origin."\r\n".$systemPrompt."\r\n".$suffix;
    }

    /**
     * Get prompt mapping for different content types
     *
     * @return array Mapping of column types to system setting keys
     */
    protected function getPromptMapping(): array
    {
        return [
            'article_summary'     => 'ai_prompt_article_summary',
            'article_slug'        => 'ai_prompt_article_slug',
            'article_title'       => 'ai_prompt_article_seo_title',
            'article_description' => 'ai_prompt_article_seo_description',
            'article_keywords'    => 'ai_prompt_article_seo_keywords',

            'page_title'       => 'ai_prompt_page_seo_title',
            'page_description' => 'ai_prompt_page_seo_description',
            'page_keywords'    => 'ai_prompt_page_seo_keywords',
        ];
    }
}
