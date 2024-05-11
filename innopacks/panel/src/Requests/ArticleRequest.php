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

class ArticleRequest extends FormRequest
{
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
        if ($this->article) {
            $slugRule = 'alpha_dash|unique:articles,slug,'.$this->article->id;
        } else {
            $slugRule = 'alpha_dash|unique:articles,slug';
        }

        return [
            'catalog_id' => 'integer',
            'slug'       => $slugRule,
            'position'   => 'integer',
            'viewed'     => 'integer',
            'active'     => 'bool',

            'descriptions.*.locale'  => 'required',
            'descriptions.*.title'   => 'required',
            'descriptions.*.content' => 'required',
        ];
    }
}
