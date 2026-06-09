<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'no_custom_theme'             => '시스템 디렉터리에 사용자 지정 템플릿이 없습니다. innopacks/front/resources의 기본 템플릿을 사용합니다',
    'no_preview'                  => '미리보기 없음',
    'available_themes_count'      => '발견된 테마: :count개',
    'themes_stat_demo'            => '데모 데이터 포함',
    'themes_stat_current'         => '현재 테마',
    'themes_stat_none'            => '(기본 패키지 뷰)',
    'theme_badge_demo'            => '데모',
    'view_detail'                 => '상세 보기',
    'import_demo_data'            => '데모 가져오기',
    'demo_import_cancel'          => '취소',
    'confirm_import_demo_title'   => '":name" 데모 데이터 가져오기',
    'confirm_import_demo_intro'   => '이 테마의 데모 패키지에서 샘플 CMS 콘텐츠를 불러오고 테마 이미지를 공개 폴더에 게시합니다.',
    'clear_before_import'         => '가져오기 전에 기존 게시글, 카탈로그, 페이지, 태그 및 페이지 모듈을 제거합니다 (언어 및 설정은 유지)',
    'clear_before_import_warning' => '이 작업은 해당 레코드를 영구적으로 삭제합니다. 주의해서 진행하세요.',
    'confirm_import_demo'         => '가져오기',
    'demo_installed'              => '데모 데이터를 성공적으로 가져왔습니다',
    'error_theme_validation'      => '일부 테마 폴더 유효성 검사 실패:',
    'error_config_not_found'      => '테마 설정 파일 없음: :file',
    'error_config_invalid'        => '테마 설정이 유효하지 않은 JSON: :file',
    'error_config_missing'        => '테마 설정에 필드 누락: :field',
    'error_code_mismatch'         => '테마 폴더 ":folder"는 설정 코드 ":code"와 일치해야 합니다',
    'error_code_not_lowercase'    => '테마 설정 코드 ":code"는 폴더 이름과 일치하는 소문자 snake_case여야 합니다',
    'error_theme_directory'       => '테마 디렉터리를 찾을 수 없습니다.',
    'error_demo_not_found'        => '이 테마에는 데모 패키지가 없습니다 (demo/Seeder.php 누락).',
    'error_demo_invalid_seeder'   => '데모 시더는 호출 가능한 클로저를 반환해야 합니다.',

    'current_theme'                     => '현재 테마',
    'author'                            => '작성자',
    'version'                           => '버전',
    'theme_description'                 => '테마 설명',
    'import_export_data'                => '데모 데이터',
    'no_demo_data'                      => '데모 데이터 없음',
    'no_demo_data_description'          => '이 테마는 데모 데이터가 없습니다. 데모 데이터를 추가하려면 <code>demo/Seeder.php</code> 파일을 생성하세요.',
    'demo_data_notice'                  => '이 테마에는 데모 데이터가 포함되어 있습니다',
    'demo_data_warning'                 => '데모 데이터를 가져오면 기존 데이터를 덮어씁니다. 중요한 데이터를 백업했는지 확인하세요.',
    'confirm_import'                    => '가져오기 확인',
    'confirm_import_button'             => '가져오기 확인',
    'confirm_import_warning'            => '데모 데이터를 가져오면 기존 데이터를 덮어씁니다. 이 작업은 되돌릴 수 없습니다. 계속하시겠습니까?',
    'import_clear_default_catalog'      => '가져오기 전 기존 콘텐츠 데이터 삭제',
    'import_clear_default_catalog_help' => '선택하면 데모 가져오기 전에 게시글, 카탈로그, 페이지, 태그 및 페이지 모듈이 제거됩니다. 기본값은 꺼짐입니다. 프로덕션에서는 주의해서 사용하세요.',
    'import_failed'                     => '가져오기 실패',
    'not_used'                          => '미사용',
    'preview'                           => '미리보기',
    'theme_guide_title'                 => '테마 개발 가이드',
    'theme_guide_desc'                  => '사용자 지정 테마를 개발할 때 다음 지침을 따르세요:',
    'theme_guide_preview'               => '권장 900x600 픽셀, 3:2 비율, public/images/ 디렉터리에 배치',
    'theme_guide_preview_title'         => '미리보기 이미지 (preview.png/jpg)',
    'theme_guide_icon'                  => '권장 60x60 픽셀, 정사각형',
    'theme_guide_icon_title'            => '테마 아이콘 (icon.png)',
    'theme_guide_config'                => '"code"는 폴더 이름과 일치해야 하며 모두 소문자여야 합니다',
    'theme_guide_config_title'          => 'config.json 설정',
];
