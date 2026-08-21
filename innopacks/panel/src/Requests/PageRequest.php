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

class PageRequest extends FormRequest
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
            'viewed'          => 'nullable|integer',
            'position'        => 'nullable|integer',
            'show_breadcrumb' => 'nullable|bool',
            'active'          => 'nullable|bool',

            "translations.$panelLocale.locale"           => 'required',
            "translations.$panelLocale.title"            => 'required',
            "translations.$panelLocale.content"          => 'nullable|string',
            "translations.$panelLocale.template"         => 'nullable|string|max:65535',
            "translations.$panelLocale.meta_title"       => 'nullable|string|max:500',
            "translations.$panelLocale.meta_keywords"    => 'nullable|string|max:500',
            "translations.$panelLocale.meta_description" => 'nullable|string|max:1000',
        ];

        if ($this->slug) {
            if ($this->page) {
                $slugRule = 'alpha_dash|unique:pages,slug,'.$this->page->id;
            } else {
                $slugRule = 'alpha_dash|unique:pages,slug';
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
            "translations.$panelLocale.locale"           => __('panel/common.locale'),
            "translations.$panelLocale.title"            => __('panel/page.title'),
            "translations.$panelLocale.content"          => __('panel/common.content'),
            "translations.$panelLocale.template"         => __('panel/common.template'),
            "translations.$panelLocale.meta_title"       => __('panel/common.meta_title'),
            "translations.$panelLocale.meta_keywords"    => __('panel/common.meta_keywords'),
            "translations.$panelLocale.meta_description" => __('panel/common.meta_description'),
        ];
    }
}
