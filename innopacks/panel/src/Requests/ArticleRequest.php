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
        $rules = [
            'catalog_id' => 'integer',
            'position'   => 'integer',
            'viewed'     => 'integer',
            'active'     => 'bool',
            'image'      => 'nullable|string|max:500',
            'author'     => 'nullable|string|max:60',

            'translations.*.locale'           => 'required',
            'translations.*.title'            => 'required',
            'translations.*.content'          => 'nullable',
            'translations.*.summary'          => 'nullable|string|max:500',
            'translations.*.image'            => 'nullable|string|max:500',
            'translations.*.meta_title'       => 'nullable|string|max:500',
            'translations.*.meta_keywords'    => 'nullable|string|max:500',
            'translations.*.meta_description' => 'nullable|string|max:1000',
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
}
