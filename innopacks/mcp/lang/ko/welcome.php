<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'subtitle'          => ':name은 MCP를 통해 사이트 콘텐츠 조회·관리 기능을 외부 AI 에이전트에 공개합니다.',
    'active'            => '실행 중',
    'tools_available'   => '개 도구 사용 가능',
    'nav_overview'      => '개요',
    'nav_architecture'  => '구조',
    'nav_connect'       => '연결 안내',
    'nav_auth'          => '인증',
    'nav_tools'         => '전체 도구',
    'overview_title'    => '개요',
    'overview_desc'     => '이 엔드포인트는 <strong>Model Context Protocol</strong>(Streamable HTTP, JSON-RPC 2.0)을 구현합니다. Claude Code, Cursor, Cline 등 AI 도구는 관리자 인증을 통해 사이트 콘텐츠를 안전하게 조회하고 관리할 수 있습니다. 모든 도구는 관리자 권한 체계를 따르며, 쓰기 작업에는 추가 인가가 필요합니다.',
    'endpoint_label'    => '엔드포인트:',
    'arch_desc'         => 'MCP는 innopacks/mcp가 제공하는 프로토콜 어댑터 계층입니다. 모든 도구는 innopacks/aicore에서 정의·관리되며, 두 모듈은 ToolInterface 계약으로 분리되어 있습니다.',
    'arch_ai'           => 'innopacks/aicore',
    'arch_tools'        => 'AI 도구<br>(데이터 조회/운영)',
    'arch_mcp'          => 'innopacks/mcp',
    'arch_protocol'     => '프로토콜 어댑터<br>(JSON-RPC + 인증)',
    'arch_bridge'       => 'ToolInterface',
    'arch_note_badge'   => '팁',
    'arch_note'         => '관리 화면의 AI 채팅(innopacks/aicore)은 같은 도구 체계를 사용하지만 MCP 없이 웹 UI로 직접 상호작용합니다.',
    'connect_title'     => '연결 안내',
    'no_token'          => '이 페이지에서 API 토큰을 찾지 못했습니다. 먼저 관리자 화면에 로그인한 뒤 시스템 설정 → AI → MCP에서 이 페이지를 여세요.',
    'auth_title'        => '인증',
    'auth_desc'         => '관리자 계정으로 Bearer 토큰을 발급받으세요:',
    'auth_token_hint'   => '토큰을 발급받으면 관리 화면의',
    'system_settings'   => '시스템 설정 → 도구 → AI',
    'auth_mcp_card'     => 'MCP 서비스 카드에서 확인하고 관리할 수 있습니다.',
    'tools_title'       => '전체 도구',
    'tools_plugin_hint' => '플러그인은 ai.tools 훅으로 사용자 지정 도구를 등록할 수 있으며, 등록된 도구는 이 목록과 MCP에 자동으로 표시됩니다:',
    'cat_content'       => '콘텐츠',
    'cat_config'        => '설정 / 시스템',
    'cat_other'         => '기타',
];
