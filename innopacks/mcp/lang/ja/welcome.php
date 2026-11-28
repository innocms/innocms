<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'subtitle'          => ':nameはMCPを通じて、サイトコンテンツの検索・管理機能を外部のAIエージェントに公開します。',
    'active'            => '稼働中',
    'tools_available'   => '個のツールが利用可能',
    'nav_overview'      => '概要',
    'nav_architecture'  => 'アーキテクチャ',
    'nav_connect'       => '接続ガイド',
    'nav_auth'          => '認証',
    'nav_tools'         => 'すべてのツール',
    'overview_title'    => '概要',
    'overview_desc'     => 'このエンドポイントは<strong>Model Context Protocol</strong>（Streamable HTTP、JSON-RPC 2.0）を実装しています。Claude Code、Cursor、ClineなどのAIツールは、管理者認証を通じてサイトコンテンツを安全に検索・管理できます。すべてのツールは管理者権限システムを継承し、書き込み操作には追加の認可が必要です。',
    'endpoint_label'    => 'エンドポイント：',
    'arch_desc'         => 'MCPは innopacks/mcp が提供するプロトコルアダプタ層です。すべてのツールは innopacks/aicore で定義・管理されており、両者はToolInterface契約によって分離されています。',
    'arch_ai'           => 'innopacks/aicore',
    'arch_tools'        => 'AIツール<br>（データ検索・操作）',
    'arch_mcp'          => 'innopacks/mcp',
    'arch_protocol'     => 'プロトコルアダプタ層<br>（JSON-RPC + 認証）',
    'arch_bridge'       => 'ToolInterface',
    'arch_note_badge'   => 'ヒント',
    'arch_note'         => '管理画面内のAIチャット（innopacks/aicore）は同じツール体系を使いますが、MCPを介さずWeb UIで直接やり取りします。',
    'connect_title'     => '接続ガイド',
    'no_token'          => 'このページではAPIトークンが見つかりませんでした。先に管理画面にログインし、システム設定 → AI → MCP からこのページを開いてください。',
    'auth_title'        => '認証',
    'auth_desc'         => '管理者アカウントでBearerトークンを取得します：',
    'auth_token_hint'   => 'トークン取得後、管理画面の',
    'system_settings'   => 'システム設定 → ツール → AI',
    'auth_mcp_card'     => 'MCPサービスカードで確認・管理できます。',
    'tools_title'       => 'すべてのツール',
    'tools_plugin_hint' => 'プラグインは ai.tools フックで独自のツールを登録できます。登録したツールはこの一覧とMCPに自動的に反映されます：',
    'cat_content'       => 'コンテンツ',
    'cat_config'        => '設定 / システム',
    'cat_other'         => 'その他',
];
