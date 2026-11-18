<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Auth;

use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Crypt;
use InnoCMS\Common\Models\Admin;
use PragmaRX\Google2FA\Google2FA;

/**
 * Admin panel 2FA (TOTP) management (panel base capability).
 *
 * - The generated secret first travels through the session and is only stored encrypted in the database
 *   after the user validation passes (confirm)
 * - Storage format: Crypt::encryptString (APP_KEY derived AES), column admins.totp_secret_enc
 * - Enabled = totp_secret_enc is not empty
 */
class TotpManager
{
    protected static ?Google2FA $engine = null;

    /**
     * Generate a new secret (not stored in the database, waiting for confirm).
     */
    public static function generateSecret(): string
    {
        return static::engine()->generateSecretKey(32);
    }

    /**
     * otpauth:// URI.
     */
    public static function provisioningUri(string $secret, string $account): string
    {
        return static::engine()->getQRCodeUrl(
            static::appName(),
            $account,
            $secret
        );
    }

    /**
     * QR code of the otpauth URI (SVG base64 data URI, rendered on the server side).
     *
     * The secret never leaves the server: no external JS library / online QR code API is required.
     */
    public static function qrDataUri(string $otpauthUri): string
    {
        $options = new QROptions([
            'outputType' => QRMarkupSVG::class,
            'eccLevel'   => 'L',
            'scale'      => 4,
        ]);

        return (string) (new QRCode($options))->render($otpauthUri);
    }

    /**
     * Verify the 6-digit dynamic code (±1 of the 30s window tolerance, guarding against clock drift).
     */
    public static function verify(string $secret, ?string $code): bool
    {
        $code = preg_replace('/[\s-]/', '', trim((string) $code)) ?? '';

        if (! preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        try {
            return (bool) static::engine()->verifyKey($secret, $code, (int) config('panel.security.totp_window', 1));
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Confirm enabling: store encrypted in the database after validation passes. Returns whether it succeeded.
     */
    public static function confirm(Admin $admin, string $secret, ?string $code): bool
    {
        if (! static::verify($secret, $code)) {
            return false;
        }

        $admin->totp_secret_enc = Crypt::encryptString($secret);
        $admin->save();

        return true;
    }

    /**
     * Read the administrator's enabled secret (decrypted); returns null when not enabled.
     */
    public static function secretOf(Admin $admin): ?string
    {
        $enc = $admin->totp_secret_enc;

        if (! $enc) {
            return null;
        }

        try {
            return Crypt::decryptString($enc);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function isEnabled(Admin $admin): bool
    {
        return (bool) $admin->totp_secret_enc;
    }

    protected static function engine(): Google2FA
    {
        return static::$engine ??= new Google2FA;
    }

    protected static function appName(): string
    {
        return (string) (system_setting('panel_name') ?: config('app.name') ?: 'InnoAdmin');
    }
}
