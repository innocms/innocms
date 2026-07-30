<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'title'             => 'MCP Server',
    'subtitle'          => ':name exposes site content query and management capabilities to external AI agents via MCP.',
    'active'            => 'Active',
    'tools_available'   => 'tools available',
    'nav_overview'      => 'Overview',
    'nav_architecture'  => 'Architecture',
    'nav_connect'       => 'Connect',
    'nav_auth'          => 'Auth',
    'nav_tools'         => 'All Tools',
    'overview_title'    => 'Overview',
    'overview_desc'     => 'This endpoint implements the <strong>Model Context Protocol</strong> (Streamable HTTP, JSON-RPC 2.0). AI tools like Claude Code, Cursor, and Cline can securely query and manage site content through admin authentication. All tools inherit the admin permission system; write operations require additional authorization.',
    'endpoint_label'    => 'Endpoint:',
    'arch_desc'         => 'MCP is the protocol adapter layer provided by innopacks/mcp. All tools are defined and maintained in innopacks/aicore, with the two decoupled through the ToolInterface contract.',
    'arch_ai'           => 'innopacks/aicore',
    'arch_tools'        => 'AI Tools<br>(data query/ops)',
    'arch_mcp'          => 'innopacks/mcp',
    'arch_protocol'     => 'Protocol Adapter<br>(JSON-RPC + Auth)',
    'arch_bridge'       => 'ToolInterface',
    'arch_note_badge'   => 'Tip',
    'arch_note'         => 'The in-panel AI chat (innopacks/aicore) uses the same Tool system but interacts via Web UI without MCP.',
    'connect_title'     => 'Connect',
    'your_token'        => 'your-admin-token',
    'no_token'          => 'No API token found on this page. Please log in to the admin panel first, then navigate to System Settings → AI → MCP to view this page with your token.',
    'auth_title'        => 'Authentication',
    'auth_desc'         => 'Obtain a Bearer token with an admin account:',
    'auth_token_hint'   => 'After obtaining a token, you can view and manage it in the admin panel under',
    'system_settings'   => 'System Settings → Tools → AI',
    'auth_mcp_card'     => 'MCP service card.',
    'tools_title'       => 'All Tools',
    'tools_plugin_hint' => 'Plugins can register custom tools via the ai.tools hook — they automatically appear in this list and over MCP:',
    'cat_content'       => 'Content',
    'cat_config'        => 'Config / System',
    'cat_other'         => 'Other',
];
