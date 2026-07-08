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
        $rules = [
            'position'    => 'integer',
            'viewed'      => 'integer',
            'active'      => 'bool',
            'price'       => 'nullable|numeric|min:0',
            'link'        => ['nullable', 'string', 'max:500', 'regex:#^https?://#i'],
            'spu_code'    => 'nullable|string|max:128',
            'images'      => 'nullable|array',
            'video'       => 'nullable|array',
            'video.url'   => ['nullable', 'string', 'max:500', 'regex:#^(https?:\/\/|\/)#i'],
            'categories'  => 'nullable|array',
            'related_ids' => 'nullable|array',

            'translations.*.locale'           => 'required',
            'translations.*.name'             => 'required',
            'translations.*.summary'          => 'nullable|string|max:1000',
            'translations.*.selling_point'    => 'nullable|string|max:1000',
            'translations.*.content'          => 'nullable',
            'translations.*.meta_title'       => 'nullable|string|max:500',
            'translations.*.meta_keywords'    => 'nullable|string|max:500',
            'translations.*.meta_description' => 'nullable|string|max:1000',
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
}
