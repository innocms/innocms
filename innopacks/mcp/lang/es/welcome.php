<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'subtitle'          => ':name expone a agentes de IA externos, mediante MCP, la consulta y la gestión del contenido del sitio.',
    'active'            => 'Activo',
    'tools_available'   => 'herramientas disponibles',
    'nav_overview'      => 'Descripción general',
    'nav_architecture'  => 'Arquitectura',
    'nav_connect'       => 'Conexión',
    'nav_auth'          => 'Autenticación',
    'nav_tools'         => 'Todas las herramientas',
    'overview_title'    => 'Descripción general',
    'overview_desc'     => 'Este endpoint implementa el <strong>Model Context Protocol</strong> (HTTP transmitible, JSON-RPC 2.0). Herramientas de IA como Claude Code, Cursor y Cline pueden consultar y gestionar de forma segura el contenido del sitio mediante la autenticación de administrador. Todas las herramientas heredan el sistema de permisos del panel; las operaciones de escritura requieren una autorización adicional.',
    'endpoint_label'    => 'Endpoint:',
    'arch_desc'         => 'MCP es la capa de adaptación de protocolo que proporciona innopacks/mcp. Todas las herramientas se definen y mantienen en innopacks/aicore, y ambas partes están desacopladas mediante el contrato ToolInterface.',
    'arch_ai'           => 'innopacks/aicore',
    'arch_tools'        => 'Herramientas de IA<br>(consulta/operaciones de datos)',
    'arch_mcp'          => 'innopacks/mcp',
    'arch_protocol'     => 'Adaptador de protocolo<br>(JSON-RPC + autenticación)',
    'arch_bridge'       => 'ToolInterface',
    'arch_note_badge'   => 'Consejo',
    'arch_note'         => 'El chat de IA del panel (innopacks/aicore) usa el mismo sistema de herramientas, pero interactúa mediante la interfaz web sin MCP.',
    'connect_title'     => 'Conexión',
    'no_token'          => 'No se ha encontrado ningún token de API en esta página. Inicia sesión en el panel y entra en Ajustes del sistema → IA → MCP para ver esta página con tu token.',
    'auth_title'        => 'Autenticación',
    'auth_desc'         => 'Obtén un token Bearer con una cuenta de administrador:',
    'auth_token_hint'   => 'Una vez obtenido el token, puedes verlo y gestionarlo en el panel, en',
    'system_settings'   => 'Ajustes del sistema → Herramientas → IA',
    'auth_mcp_card'     => 'la tarjeta del servicio MCP.',
    'tools_title'       => 'Todas las herramientas',
    'tools_plugin_hint' => 'Los plugins pueden registrar herramientas propias mediante el hook ai.tools: aparecen automáticamente en esta lista y a través de MCP:',
    'cat_content'       => 'Contenido',
    'cat_config'        => 'Configuración / Sistema',
    'cat_other'         => 'Otros',
];
