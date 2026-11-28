<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'mcp_service'       => 'MCPサービス',
    'mcp_service_desc'  => 'サイトのコンテンツ（記事、カタログ、ページ、タグ）をClaudeやCursorなどのMCPクライアントに公開します。管理者トークンが必要です。',
    'enable_mcp'        => 'MCPエンドポイントを有効化',
    'endpoint_url'      => 'エンドポイントURL',
    'auth_header'       => '認証',
    'token_hint'        => '管理者アカウントで :url にPOSTしてトークンを取得し、Bearerトークンとして渡してください。',
    'usage_title'       => '使い方',
    'usage_cursor'      => 'Cursor：以下を ~/.cursor/mcp.json に追加してください（または 設定 → MCP → 新しいサーバーを追加）：',
    'usage_claude_code' => 'Claude Code：次のコマンドを実行してください：',
];
