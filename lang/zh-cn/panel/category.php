<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'name'        => '分类名称',
    'parent'      => '上级分类',
    'top_level'   => '顶级分类',
    'summary'     => '分类简介',
    'content'     => '分类内容',
    'content_tab' => '分类内容',
    'extra_tab'   => '其他信息',

    // Validation errors thrown by Category model booted() hook
    'parent_self'        => '上级分类不能是自身',
    'circular_reference' => '不能选择自身或下级分类作为上级',
];
