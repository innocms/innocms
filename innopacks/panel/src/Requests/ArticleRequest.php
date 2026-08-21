<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Requests;

use Illuminate\Foundation\Http\FormRequest;
use InnoCMS\Common\Traits\PatchRequestTrait;

class ArticleRequest extends FormRequest
{
    use PatchRequestTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $panelLocale = panel_locale_code();

        $rules = [
            'catalog_id' => 'nullable|integer',
            'position'   => 'nullable|integer',
            'viewed'     => 'nullable|integer',
            'active'     => 'nullable|bool',
            'image'      => 'nullable|string|max:500',
            'author'     => 'nullable|string|max:60',
            'created_at' => 'nullable|date',

            "translations.$panelLocale.locale"           => 'required',
            "translations.$panelLocale.title"            => 'required',
            "translations.$panelLocale.content"          => 'nullable|string',
            "translations.$panelLocale.summary"          => 'nullable|string|max:500',
            "translations.$panelLocale.image"            => 'nullable|string|max:500',
            "translations.$panelLocale.meta_title"       => 'nullable|string|max:500',
            "translations.$panelLocale.meta_keywords"    => 'nullable|string|max:500',
            "translations.$panelLocale.meta_description" => 'nullable|string|max:1000',
        ];

        if ($this->slug) {
            if ($this->article) {
                $slugRule = 'alpha_dash|unique:articles,slug,'.$this->article->id;
            } else {
                $slugRule = 'alpha_dash|unique:articles,slug';
            }
            $rules['slug'] = $slugRule;
        }

        if ($this->isMethod('PATCH')) {
            $rules = $this->applySometimesToRules($rules);
        }

        return $rules;
    }

    /**
     * Get custom attribute names for validator error messages.
     *
     * @return array
     */
    public function attributes(): array
    {
        $panelLocale = panel_locale_code();

        return [
            'catalog_id' => __('panel/article.catalog'),

            "translations.$panelLocale.locale"           => __('panel/common.locale'),
            "translations.$panelLocale.title"            => __('panel/article.title'),
            "translations.$panelLocale.content"          => __('panel/article.content'),
            "translations.$panelLocale.summary"          => __('panel/article.summary'),
            "translations.$panelLocale.image"            => __('panel/article.image'),
            "translations.$panelLocale.meta_title"       => __('panel/common.meta_title'),
            "translations.$panelLocale.meta_keywords"    => __('panel/common.meta_keywords'),
            "translations.$panelLocale.meta_description" => __('panel/common.meta_description'),
        ];
    }
}
