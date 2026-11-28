<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'subtitle'          => ':name expõe a agentes de IA externos, via MCP, as capacidades de consulta e gerenciamento do conteúdo do site.',
    'active'            => 'Ativo',
    'tools_available'   => 'ferramentas disponíveis',
    'nav_overview'      => 'Visão geral',
    'nav_architecture'  => 'Arquitetura',
    'nav_connect'       => 'Conexão',
    'nav_auth'          => 'Autenticação',
    'nav_tools'         => 'Todas as ferramentas',
    'overview_title'    => 'Visão geral',
    'overview_desc'     => 'Este endpoint implementa o <strong>Model Context Protocol</strong> (HTTP transmitível, JSON-RPC 2.0). Ferramentas de IA como Claude Code, Cursor e Cline podem consultar e gerenciar o conteúdo do site com segurança por meio da autenticação de administrador. Todas as ferramentas herdam o sistema de permissões do painel; operações de escrita exigem autorização adicional.',
    'endpoint_label'    => 'Endpoint:',
    'arch_desc'         => 'MCP é a camada de adaptação de protocolo fornecida por innopacks/mcp. Todas as ferramentas são definidas e mantidas em innopacks/aicore, e as duas partes são desacopladas pelo contrato ToolInterface.',
    'arch_ai'           => 'innopacks/aicore',
    'arch_tools'        => 'Ferramentas de IA<br>(consulta/operações de dados)',
    'arch_mcp'          => 'innopacks/mcp',
    'arch_protocol'     => 'Adaptador de protocolo<br>(JSON-RPC + autenticação)',
    'arch_bridge'       => 'ToolInterface',
    'arch_note_badge'   => 'Dica',
    'arch_note'         => 'O chat de IA do painel (innopacks/aicore) usa o mesmo sistema de ferramentas, mas interage pela interface web sem MCP.',
    'connect_title'     => 'Conexão',
    'no_token'          => 'Nenhum token de API encontrado nesta página. Faça login no painel e abra esta página em Configurações do sistema → IA → MCP para vê-la com o seu token.',
    'auth_title'        => 'Autenticação',
    'auth_desc'         => 'Obtenha um token Bearer com uma conta de administrador:',
    'auth_token_hint'   => 'Depois de obter o token, você pode vê-lo e gerenciá-lo no painel, em',
    'system_settings'   => 'Configurações do sistema → Ferramentas → IA',
    'auth_mcp_card'     => 'o cartão do serviço MCP.',
    'tools_title'       => 'Todas as ferramentas',
    'tools_plugin_hint' => 'Plugins podem registrar ferramentas próprias pelo hook ai.tools: elas aparecem automaticamente nesta lista e via MCP:',
    'cat_content'       => 'Conteúdo',
    'cat_config'        => 'Configuração / Sistema',
    'cat_other'         => 'Outros',
];
