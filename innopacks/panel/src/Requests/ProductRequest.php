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

class ProductRequest extends FormRequest
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
     * Display-only product: no SKU / brand / attribute / option rules.
     * link / video.url constrained to http(s) or site-relative paths to block
     * javascript:/data: scheme XSS.
     *
     * @return array
     */
    public function rules(): array
    {
        $panelLocale = panel_locale_code();

        $rules = [
            'position'    => 'nullable|integer',
            'viewed'      => 'nullable|integer',
            'active'      => 'nullable|bool',
            'price'       => 'nullable|numeric|min:0',
            'link'        => ['nullable', 'string', 'max:500', 'regex:#^https?://#i'],
            'spu_code'    => 'nullable|string|max:128',
            'images'      => 'nullable|array',
            'video'       => 'nullable|array',
            'video.url'   => ['nullable', 'string', 'max:500', 'regex:#^(https?:\/\/|\/)#i'],
            'categories'  => 'nullable|array',
            'related_ids' => 'nullable|array',

            "translations.$panelLocale.locale"           => 'required',
            "translations.$panelLocale.name"             => 'required',
            "translations.$panelLocale.summary"          => 'nullable|string|max:1000',
            "translations.$panelLocale.selling_point"    => 'nullable|string|max:1000',
            "translations.$panelLocale.content"          => 'nullable|string',
            "translations.$panelLocale.meta_title"       => 'nullable|string|max:500',
            "translations.$panelLocale.meta_keywords"    => 'nullable|string|max:500',
            "translations.$panelLocale.meta_description" => 'nullable|string|max:1000',
        ];

        if ($this->slug) {
            if ($this->product) {
                $slugRule = 'alpha_dash|unique:products,slug,'.$this->product->id;
            } else {
                $slugRule = 'alpha_dash|unique:products,slug';
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
            'slug' => __('panel/common.slug'),

            "translations.$panelLocale.locale"           => __('panel/common.locale'),
            "translations.$panelLocale.name"             => __('panel/product.name'),
            "translations.$panelLocale.summary"          => __('panel/product.summary'),
            "translations.$panelLocale.selling_point"    => __('panel/product.selling_point'),
            "translations.$panelLocale.content"          => __('panel/product.content'),
            "translations.$panelLocale.meta_title"       => __('panel/common.meta_title'),
            "translations.$panelLocale.meta_keywords"    => __('panel/common.meta_keywords'),
            "translations.$panelLocale.meta_description" => __('panel/common.meta_description'),
        ];
    }
}
