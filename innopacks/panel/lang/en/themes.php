<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'no_custom_theme'             => 'There is no custom template in the system directory, use the default template in innopacks/front/resources',
    'no_preview'                  => 'No preview',
    'available_themes_count'      => 'Themes discovered: :count',
    'themes_stat_demo'            => 'With demo data: :count',
    'themes_stat_current'         => 'Current theme',
    'themes_stat_none'            => '(default package views)',
    'theme_badge_demo'            => 'Demo',
    'view_detail'                 => 'Details',
    'import_demo_data'            => 'Import demo',
    'demo_import_cancel'          => 'Cancel',
    'confirm_import_demo_title'   => 'Import demo for “:name”',
    'confirm_import_demo_intro'   => 'Loads sample CMS content from this theme\'s demo package and publishes theme images to the public folder.',
    'clear_before_import'         => 'Before import, remove existing articles, catalogs, pages, tags, and page modules (languages and settings are kept)',
    'clear_before_import_warning' => 'This permanently deletes those records. Proceed with care.',
    'confirm_import_demo'         => 'Import now',
    'demo_installed'              => 'Demo data imported successfully',
    'error_theme_validation'      => 'Some theme folders failed validation:',
    'error_config_not_found'      => 'Theme config missing: :file',
    'error_config_invalid'        => 'Theme config invalid JSON: :file',
    'error_config_missing'        => 'Theme config missing field: :field',
    'error_code_mismatch'         => 'Theme folder ":folder" must match config code ":code"',
    'error_code_not_lowercase'    => 'Theme config code ":code" must be lowercase snake_case matching the folder name',
    'error_theme_directory'       => 'Theme directory was not found.',
    'error_demo_not_found'        => 'This theme has no demo package (demo/Seeder.php missing).',
    'error_demo_invalid_seeder'   => 'The demo seeder must return a callable closure.',
];
