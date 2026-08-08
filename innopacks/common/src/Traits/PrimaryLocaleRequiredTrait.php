<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Traits;

/**
 * Adjusts translation validation rules so that only the primary (admin)
 * locale is required; all other locales are nullable.
 *
 * Usage in a FormRequest:
 *
 *     public function rules(): array
 *     {
 *         $rules = [
 *             // ... non-translation rules ...
 *             'translations.*.locale'           => 'required',
 *             'translations.*.title'            => 'required',  // will be adjusted
 *             'translations.*.content'          => 'nullable',
 *         ];
 *
 *         return $this->adjustTranslationRules($rules, 'title');
 *     }
 */
trait PrimaryLocaleRequiredTrait
{
    use PanelLocaleHelper;

    /**
     * For every `translations.*.<field>` rule in $rules, replace `required`
     * with `nullable` unless the wildcard matches the primary locale.
     *
     * @param  array  $rules
     * @param  string  $field  The translation field key (e.g. 'title', 'name')
     * @return array
     */
    protected function adjustTranslationRules(array $rules, string $field): array
    {
        $primary = $this->primaryLocaleCode();

        foreach ($rules as $key => $rule) {
            // Match translations.{locale}.{field} patterns
            if (preg_match('#^translations\.\*\.'.preg_quote($field, '#').'$#', $key)) {
                $rules[$key] = 'nullable';
            }
        }

        // Add an explicit required rule for the primary locale
        $rules["translations.{$primary}.{$field}"] = 'required';

        return $rules;
    }
}
