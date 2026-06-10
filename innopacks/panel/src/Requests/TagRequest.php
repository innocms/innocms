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

class TagRequest extends FormRequest
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
            'position' => 'integer',
            'active'   => 'bool',

            'translations.*.locale' => 'required',
            'translations.*.name'   => 'required',
        ];

        if ($this->slug) {
            if ($this->tag) {
                $slugRule = 'alpha_dash|unique:tags,slug,'.$this->tag->id;
            } else {
                $slugRule = 'alpha_dash|unique:tags,slug';
            }
            $rules['slug'] = $slugRule;
        }

        if ($this->isMethod('PATCH')) {
            $rules = $this->applySometimesToRules($rules);
        }

        return $rules;
    }
}
