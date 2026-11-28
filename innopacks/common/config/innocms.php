<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use InnoCMS\Common\Models\AuditLog;
use InnoCMS\Common\Models\SystemAlert;

return [
    'edition'      => 'community',
    'version'      => '0.6.0',
    'build'        => '20261128',
    'api_url'      => env('INNO_API_URL', 'https://www.innocms.com'),
    'locale_force' => env('APP_LOCALE_FORCE'),

    /*
     * Dedicated HMAC key for PII lookup hashes (FieldCrypto::hash).
     * Production must use a separate strong random value (different from APP_KEY): an
     * APP_KEY leak must not enable dictionary attacks against the _hash columns.
     * Falls back to APP_KEY when unset (fine for development and testing only).
     */
    'pii_hmac_key' => env('INNO_PII_HMAC_KEY', ''),

    /*
     * Model class extension points: downstream products may inject subclasses.
     *  - audit.model: the audit log model (default InnoCMS\Common\Models\AuditLog)
     *  - alert.model: the system alert model (default InnoCMS\Common\Models\SystemAlert)
     */
    'audit' => [
        'model' => AuditLog::class,
    ],

    'alert' => [
        'model' => SystemAlert::class,
    ],
];
