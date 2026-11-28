<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'subtitle'          => ':name через MCP открывает внешним ИИ-агентам возможности запроса и управления содержимым сайта.',
    'active'            => 'Работает',
    'tools_available'   => 'инструментов доступно',
    'nav_overview'      => 'Обзор',
    'nav_architecture'  => 'Архитектура',
    'nav_connect'       => 'Подключение',
    'nav_auth'          => 'Аутентификация',
    'nav_tools'         => 'Все инструменты',
    'overview_title'    => 'Обзор',
    'overview_desc'     => 'Этот endpoint реализует <strong>Model Context Protocol</strong> (Streamable HTTP, JSON-RPC 2.0). ИИ-инструменты, такие как Claude Code, Cursor и Cline, могут безопасно запрашивать содержимое сайта и управлять им через аутентификацию администратора. Все инструменты наследуют систему прав администратора; для операций записи требуется дополнительное разрешение.',
    'endpoint_label'    => 'Endpoint:',
    'arch_desc'         => 'MCP — слой адаптации протокола, предоставляемый innopacks/mcp. Все инструменты определяются и сопровождаются в innopacks/aicore; модули связаны только контрактом ToolInterface.',
    'arch_ai'           => 'innopacks/aicore',
    'arch_tools'        => 'ИИ-инструменты<br>(запрос/изменение данных)',
    'arch_mcp'          => 'innopacks/mcp',
    'arch_protocol'     => 'Адаптер протокола<br>(JSON-RPC + аутентификация)',
    'arch_bridge'       => 'ToolInterface',
    'arch_note_badge'   => 'Подсказка',
    'arch_note'         => 'ИИ-чат в панели управления (innopacks/aicore) использует ту же систему инструментов, но взаимодействует через веб-интерфейс без MCP.',
    'connect_title'     => 'Подключение',
    'no_token'          => 'На этой странице не найден API-токен. Сначала войдите в панель управления, затем откройте эту страницу через Настройки системы → ИИ → MCP, чтобы токен подставился автоматически.',
    'auth_title'        => 'Аутентификация',
    'auth_desc'         => 'Получите Bearer-токен с учётной записью администратора:',
    'auth_token_hint'   => 'После получения токена его можно посмотреть и изменить в панели управления:',
    'system_settings'   => 'Настройки системы → Инструменты → ИИ',
    'auth_mcp_card'     => 'карточка сервиса MCP.',
    'tools_title'       => 'Все инструменты',
    'tools_plugin_hint' => 'Плагины могут регистрировать собственные инструменты через хук ai.tools — они автоматически появляются в этом списке и в MCP:',
    'cat_content'       => 'Содержимое',
    'cat_config'        => 'Конфигурация / Система',
    'cat_other'         => 'Другое',
];
