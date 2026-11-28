<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'mcp_service'       => 'MCP 서비스',
    'mcp_service_desc'  => '사이트 콘텐츠(게시글, 카테고리, 페이지, 태그)를 Claude, Cursor 등 MCP 클라이언트에 공개합니다. 관리자 토큰이 필요합니다.',
    'enable_mcp'        => 'MCP 엔드포인트 사용',
    'endpoint_url'      => '엔드포인트 URL',
    'auth_header'       => '인증 방식',
    'token_hint'        => '관리자 계정으로 :url에 POST 요청해 토큰을 발급받은 뒤 Bearer 토큰으로 전달하세요.',
    'usage_title'       => '사용 방법',
    'usage_cursor'      => 'Cursor: 다음 내용을 ~/.cursor/mcp.json에 추가하세요(또는 설정 → MCP → 새 서버 추가):',
    'usage_claude_code' => 'Claude Code: 다음 명령을 실행하세요:',
];
