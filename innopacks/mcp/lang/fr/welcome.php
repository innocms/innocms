<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'subtitle'          => ':name expose à des agents IA externes, via MCP, les capacités de requête et de gestion du contenu du site.',
    'active'            => 'Actif',
    'tools_available'   => 'outils disponibles',
    'nav_overview'      => 'Présentation',
    'nav_architecture'  => 'Architecture',
    'nav_connect'       => 'Connexion',
    'nav_auth'          => 'Authentification',
    'nav_tools'         => 'Tous les outils',
    'overview_title'    => 'Présentation',
    'overview_desc'     => 'Ce point de terminaison implémente le <strong>Model Context Protocol</strong> (HTTP diffusable, JSON-RPC 2.0). Des outils d\'IA comme Claude Code, Cursor et Cline peuvent interroger et gérer le contenu du site en toute sécurité via l\'authentification administrateur. Tous les outils héritent du système de permissions du panneau d\'administration ; les opérations d\'écriture exigent une autorisation supplémentaire.',
    'endpoint_label'    => 'Point de terminaison :',
    'arch_desc'         => 'MCP est la couche d\'adaptation de protocole fournie par innopacks/mcp. Tous les outils sont définis et maintenus dans innopacks/aicore, les deux étant découplés par le contrat ToolInterface.',
    'arch_ai'           => 'innopacks/aicore',
    'arch_tools'        => 'Outils d\'IA<br>(requêtes/opérations sur les données)',
    'arch_mcp'          => 'innopacks/mcp',
    'arch_protocol'     => 'Adaptateur de protocole<br>(JSON-RPC + authentification)',
    'arch_bridge'       => 'ToolInterface',
    'arch_note_badge'   => 'Astuce',
    'arch_note'         => 'Le chat IA du panneau d\'administration (innopacks/aicore) utilise le même système d\'outils, mais interagit via l\'interface web sans MCP.',
    'connect_title'     => 'Connexion',
    'no_token'          => 'Aucun jeton d\'API trouvé sur cette page. Connectez-vous d\'abord au panneau d\'administration, puis ouvrez cette page via Paramètres système → IA → MCP pour qu\'il soit rempli automatiquement.',
    'auth_title'        => 'Authentification',
    'auth_desc'         => 'Obtenez un jeton Bearer avec un compte administrateur :',
    'auth_token_hint'   => 'Une fois le jeton obtenu, vous pouvez le consulter et le gérer dans le panneau d\'administration, sous',
    'system_settings'   => 'Paramètres système → Outils → IA',
    'auth_mcp_card'     => 'la carte du service MCP.',
    'tools_title'       => 'Tous les outils',
    'tools_plugin_hint' => 'Les plugins peuvent enregistrer leurs propres outils via le hook ai.tools : ils apparaissent automatiquement dans cette liste et via MCP :',
    'cat_content'       => 'Contenu',
    'cat_config'        => 'Configuration / Système',
    'cat_other'         => 'Autre',
];
