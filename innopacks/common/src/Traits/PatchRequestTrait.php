<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Traits;

trait PatchRequestTrait
{
    /**
     * Apply 'sometimes' to all rules for PATCH requests.
     * This allows partial updates where only provided fields are validated.
     *
     * @param  array  $rules
     * @return array
     */
    protected function applySometimesToRules(array $rules): array
    {
        foreach ($rules as $key => $rule) {
            $rules[$key] = 'sometimes|'.$rule;
        }

        return $rules;
    }
}
