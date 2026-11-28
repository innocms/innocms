<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'subtitle'          => ':name stellt externen KI-Agenten über MCP Abfrage- und Verwaltungsfunktionen für die Websiteinhalte bereit.',
    'active'            => 'Aktiv',
    'tools_available'   => 'Tools verfügbar',
    'nav_overview'      => 'Übersicht',
    'nav_architecture'  => 'Architektur',
    'nav_connect'       => 'Anbindung',
    'nav_auth'          => 'Authentifizierung',
    'nav_tools'         => 'Alle Tools',
    'overview_title'    => 'Übersicht',
    'overview_desc'     => 'Dieser Endpunkt implementiert das <strong>Model Context Protocol</strong> (Streamable HTTP, JSON-RPC 2.0). KI-Tools wie Claude Code, Cursor und Cline können darüber mit Admin-Authentifizierung sicher Websiteinhalte abfragen und verwalten. Alle Tools erben das Berechtigungssystem des Admin-Panels; Schreibvorgänge erfordern eine zusätzliche Freigabe.',
    'endpoint_label'    => 'Endpunkt:',
    'arch_desc'         => 'MCP ist die von innopacks/mcp bereitgestellte Protokoll-Adapterschicht. Alle Tools werden in innopacks/aicore definiert und gepflegt; beide sind über den ToolInterface-Vertrag entkoppelt.',
    'arch_ai'           => 'innopacks/aicore',
    'arch_tools'        => 'KI-Tools<br>(Datenabfrage/Aktionen)',
    'arch_mcp'          => 'innopacks/mcp',
    'arch_protocol'     => 'Protokoll-Adapter<br>(JSON-RPC + Auth)',
    'arch_bridge'       => 'ToolInterface',
    'arch_note_badge'   => 'Hinweis',
    'arch_note'         => 'Der KI-Chat im Admin-Panel (innopacks/aicore) nutzt dasselbe Tool-System, interagiert aber über die Web-Oberfläche ohne MCP.',
    'connect_title'     => 'Anbindung',
    'no_token'          => 'Auf dieser Seite wurde kein API-Token gefunden. Bitte melde dich zuerst im Admin-Panel an und öffne diese Seite dann über Systemeinstellungen → KI → MCP, damit dein Token automatisch eingetragen wird.',
    'auth_title'        => 'Authentifizierung',
    'auth_desc'         => 'Rufe mit einem Admin-Konto ein Bearer-Token ab:',
    'auth_token_hint'   => 'Nach dem Erhalt eines Tokens kannst du es im Admin-Panel unter',
    'system_settings'   => 'Systemeinstellungen → Tools → KI',
    'auth_mcp_card'     => 'in der MCP-Dienstkarte einsehen und verwalten.',
    'tools_title'       => 'Alle Tools',
    'tools_plugin_hint' => 'Plugins können über den Hook ai.tools eigene Tools registrieren; sie erscheinen automatisch in dieser Liste und über MCP:',
    'cat_content'       => 'Inhalt',
    'cat_config'        => 'Konfiguration / System',
    'cat_other'         => 'Sonstiges',
];
