<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    // Admin panel security (2FA, TOTP)
    'security' => [
        // Force two-factor: every admin account must bind and verify a TOTP code.
        // Defaults to false so existing deployments upgrade without any change;
        // products that need it enable it explicitly.
        'force_2fa' => env('PANEL_FORCE_2FA', false),

        // Dynamic code verification window (±N 30s windows, guards against clock drift)
        'totp_window' => 1,

        // IP allowlist for admin panel paths (comma separated: exact IP or IPv4 CIDR,
        // empty = disabled). The application guard and the nginx allow rule are two
        // layers of the same control; a rejected request returns 404 so the existence
        // of the admin panel is not disclosed.
        'panel_ip_allowlist' => array_values(array_filter(array_map('trim', explode(',', (string) env('PANEL_IP_ALLOWLIST', ''))))),

        // Admin panel path prefixes guarded by the IP allowlist (default: the base /panel;
        // downstream products may append their own admin panel prefixes)
        'guard_prefixes' => ['panel'],

        // Session lifetime (seconds) of a sensitive action re-confirmation: within the TTL the
        // password or dynamic code does not have to be entered again
        'sensitive_confirm_ttl' => (int) env('PANEL_SENSITIVE_CONFIRM_TTL', 600),

        // Look-back window (days) for "known IP / device" detection, audit_logs is the source of truth
        'known_login_days' => (int) env('PANEL_KNOWN_LOGIN_DAYS', 180),
    ],
];
