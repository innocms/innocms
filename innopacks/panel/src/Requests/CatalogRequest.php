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

class CatalogRequest extends FormRequest
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

            'translations.*.locale'           => 'required',
            'translations.*.title'            => 'nullable',
            'translations.*.summary'          => 'nullable|string|max:500',
            'translations.*.meta_title'       => 'nullable|string|max:500',
            'translations.*.meta_keywords'    => 'nullable|string|max:500',
            'translations.*.meta_description' => 'nullable|string|max:1000',
        ];

        $rules = $this->adjustTranslationRules($rules, 'title');

        if ($this->slug) {
            if ($this->catalog) {
                $slugRule = 'alpha_dash|unique:catalogs,slug,'.$this->catalog->id;
            } else {
                $slugRule = 'alpha_dash|unique:catalogs,slug';
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
            'parent_id'                       => __('panel/catalog.parent'),
            'position'                        => __('panel/common.position'),
            'translations.*.title'            => __('panel/catalog.title'),
            'translations.*.summary'          => __('panel/catalog.summary'),
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
        $field   = __('panel/catalog.title');

        return [
            "translations.{$primary}.title" => __('panel/common.primary_name_required', ['field' => $field, 'locale' => $primary]),
            'parent_id.integer'             => __('panel/catalog.parent_invalid'),
        ];
    }
}
