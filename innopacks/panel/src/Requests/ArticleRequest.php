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
use InnoCMS\Common\Traits\PrimaryLocaleRequiredTrait;

class ArticleRequest extends FormRequest
{
    use PatchRequestTrait, PrimaryLocaleRequiredTrait;

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
        $rules = [
            'catalog_id' => 'integer',
            'position'   => 'integer',
            'viewed'     => 'integer',
            'active'     => 'bool',
            'image'      => 'nullable|string|max:500',
            'author'     => 'nullable|string|max:60',
            'created_at' => 'nullable|date',

            'translations.*.locale'           => 'required',
            'translations.*.title'            => 'nullable',
            'translations.*.content'          => 'nullable',
            'translations.*.summary'          => 'nullable|string|max:500',
            'translations.*.image'            => 'nullable|string|max:500',
            'translations.*.meta_title'       => 'nullable|string|max:500',
            'translations.*.meta_keywords'    => 'nullable|string|max:500',
            'translations.*.meta_description' => 'nullable|string|max:1000',
        ];

        $rules = $this->adjustTranslationRules($rules, 'title');

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
        return [
            'translations.*.title'            => __('panel/article.title'),
            'translations.*.content'          => __('panel/article.content'),
            'translations.*.summary'          => __('panel/article.summary'),
            'translations.*.image'            => __('panel/article.image'),
            'translations.*.meta_title'       => __('panel/common.meta_title'),
            'translations.*.meta_keywords'    => __('panel/common.meta_keywords'),
            'translations.*.meta_description' => __('panel/common.meta_description'),
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        $primary = $this->primaryLocaleCode();
        $field   = __('panel/article.title');

        return [
            "translations.{$primary}.title" => __('panel/common.primary_name_required', ['field' => $field, 'locale' => $primary]),
        ];
    }
}
