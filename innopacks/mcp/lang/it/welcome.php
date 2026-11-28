<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'subtitle'          => ':name espone agli agenti IA esterni, tramite MCP, le funzioni di interrogazione e gestione dei contenuti del sito.',
    'active'            => 'Attivo',
    'tools_available'   => 'strumenti disponibili',
    'nav_overview'      => 'Panoramica',
    'nav_architecture'  => 'Architettura',
    'nav_connect'       => 'Connessione',
    'nav_auth'          => 'Autenticazione',
    'nav_tools'         => 'Tutti gli strumenti',
    'overview_title'    => 'Panoramica',
    'overview_desc'     => 'Questo endpoint implementa il <strong>Model Context Protocol</strong> (HTTP streamable, JSON-RPC 2.0). Strumenti IA come Claude Code, Cursor e Cline possono interrogare e gestire in sicurezza i contenuti del sito tramite l\'autenticazione amministratore. Tutti gli strumenti ereditano il sistema di permessi del pannello; le operazioni di scrittura richiedono un\'autorizzazione aggiuntiva.',
    'endpoint_label'    => 'Endpoint:',
    'arch_desc'         => 'MCP è il livello di adattamento del protocollo fornito da innopacks/mcp. Tutti gli strumenti sono definiti e mantenuti in innopacks/aicore; i due moduli sono disaccoppiati tramite il contratto ToolInterface.',
    'arch_ai'           => 'innopacks/aicore',
    'arch_tools'        => 'Strumenti IA<br>(interrogazione/operazioni sui dati)',
    'arch_mcp'          => 'innopacks/mcp',
    'arch_protocol'     => 'Adattatore di protocollo<br>(JSON-RPC + autenticazione)',
    'arch_bridge'       => 'ToolInterface',
    'arch_note_badge'   => 'Suggerimento',
    'arch_note'         => 'La chat IA nel pannello (innopacks/aicore) usa lo stesso sistema di strumenti, ma interagisce tramite l\'interfaccia web senza MCP.',
    'connect_title'     => 'Connessione',
    'no_token'          => 'In questa pagina non è stato trovato alcun token API. Accedi prima al pannello, poi apri questa pagina da Impostazioni di sistema → IA → MCP per visualizzarla con il tuo token.',
    'auth_title'        => 'Autenticazione',
    'auth_desc'         => 'Ottieni un token Bearer con un account amministratore:',
    'auth_token_hint'   => 'Dopo aver ottenuto un token, puoi visualizzarlo e gestirlo nel pannello, in',
    'system_settings'   => 'Impostazioni di sistema → Strumenti → IA',
    'auth_mcp_card'     => 'la scheda del servizio MCP.',
    'tools_title'       => 'Tutti gli strumenti',
    'tools_plugin_hint' => 'I plugin possono registrare strumenti personalizzati tramite l\'hook ai.tools: compaiono automaticamente in questo elenco e via MCP:',
    'cat_content'       => 'Contenuti',
    'cat_config'        => 'Configurazione / Sistema',
    'cat_other'         => 'Altro',
];
