<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'mcp_service'       => 'Service MCP',
    'mcp_service_desc'  => 'Expose le contenu du site (articles, catalogues, pages, tags) aux clients MCP tels que Claude et Cursor. Nécessite un jeton d\'administrateur.',
    'enable_mcp'        => 'Activer le point de terminaison MCP',
    'endpoint_url'      => 'URL du point de terminaison',
    'auth_header'       => 'Authentification',
    'token_hint'        => 'Obtenez un jeton via POST :url avec un compte administrateur, puis transmettez-le comme jeton Bearer.',
    'usage_title'       => 'Utilisation',
    'usage_cursor'      => 'Cursor : ajoutez ceci à ~/.cursor/mcp.json (ou Paramètres → MCP → Add new server) :',
    'usage_claude_code' => 'Claude Code : exécutez cette commande :',
];
