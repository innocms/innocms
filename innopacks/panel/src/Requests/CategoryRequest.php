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

class CategoryRequest extends FormRequest
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
            'parent_id' => 'nullable|integer',
            'position'  => 'nullable|integer',
            'active'    => 'bool',
            'image'     => 'nullable|string|max:500',

            'translations.*.locale'           => 'required',
            'translations.*.name'             => 'nullable',
            'translations.*.summary'          => 'nullable|string|max:500',
            'translations.*.content'          => 'nullable|string',
            'translations.*.meta_title'       => 'nullable|string|max:500',
            'translations.*.meta_keywords'    => 'nullable|string|max:500',
            'translations.*.meta_description' => 'nullable|string|max:1000',
        ];

        $rules = $this->adjustTranslationRules($rules, 'name');

        if ($this->slug) {
            if ($this->category) {
                $slugRule = 'alpha_dash|unique:categories,slug,'.$this->category->id;
            } else {
                $slugRule = 'alpha_dash|unique:categories,slug';
            }
            $rules['slug'] = $slugRule;
        }

        if ($this->isMethod('PATCH')) {
            $rules = $this->applySometimesToRules($rules);
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        $primary = $this->primaryLocaleCode();
        $field   = __('panel/category.name');

        return [
            "translations.{$primary}.name" => __('panel/common.primary_name_required', ['field' => $field, 'locale' => $primary]),
            'parent_id.integer'            => __('panel/category.parent_invalid'),
        ];
    }
}
