<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'name'               => 'Category Name',
    'parent'             => 'Parent Category',
    'top_level'          => 'Top Level',
    'summary'            => 'Summary',
    'content'            => 'Content',
    'content_tab'        => 'Content',
    'extra_tab'          => 'Extra Info',

    // Validation errors thrown by Category model booted() hook
    'parent_self'        => 'A category cannot be its own parent',
    'circular_reference' => 'Cannot select itself or a descendant as parent',
];
