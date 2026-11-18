<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

return [
    'edition'      => 'community',
    'version'      => '0.5.0',
    'build'        => '20260826',
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
     *  - notification.model: the in-site notification model (default InnoCMS\Common\Models\Notification)
     */
    'audit' => [
        'model' => InnoCMS\Common\Models\AuditLog::class,
    ],

    'notification' => [
        'model' => InnoCMS\Common\Models\Notification::class,
    ],
];