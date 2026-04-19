<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 */

return [
    'no_custom_theme'             => '系统目录下无自定义模板, 使用 innopacks/front/resources 下默认模板',
    'no_preview'                  => '无预览图',
    'available_themes_count'      => '已发现主题: :count',
    'themes_stat_demo'            => '含演示数据: :count',
    'themes_stat_current'         => '当前主题',
    'themes_stat_none'            => '（默认包内视图）',
    'theme_badge_demo'            => '演示数据',
    'view_detail'                 => '查看',
    'import_demo_data'            => '导入演示数据',
    'demo_import_cancel'          => '取消',
    'confirm_import_demo_title'   => '导入「:name」演示数据',
    'confirm_import_demo_intro'   => '将从该主题目录下的 demo 包导入页面、栏目等示例内容，并同步主题图片到公开目录。',
    'clear_before_import'         => '导入前清空现有文章、栏目、页面、标签与页面模块（不影响语言与系统设置）',
    'clear_before_import_warning' => '勾选后将删除上述内容且不可恢复，请谨慎操作。',
    'confirm_import_demo'         => '确认导入',
    'demo_installed'              => '演示数据已导入',
    'error_theme_validation'      => '下列主题文件夹未通过校验：',
    'error_config_not_found'      => '未找到主题配置: :file',
    'error_config_invalid'        => '主题配置 JSON 无效: :file',
    'error_config_missing'        => '主题配置缺少字段: :field',
    'error_code_mismatch'         => '主题目录 ":folder" 须与配置中的 code 一致（当前配置为 :code）',
    'error_code_not_lowercase'    => '主题 code ":code" 须为小写，并与文件夹名匹配',
    'error_theme_directory'       => '未找到主题目录。',
    'error_demo_not_found'        => '该主题不包含演示数据包（缺少 demo/Seeder.php）。',
    'error_demo_invalid_seeder'   => '演示数据 Seeder 须返回可调用的匿名函数。',
];
