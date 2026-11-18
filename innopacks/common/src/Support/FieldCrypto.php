<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Support;

use Illuminate\Support\Facades\Crypt;
use RuntimeException;

/**
 * PII field encryption / lookup hash (ported down from the platform package).
 *
 * Convention: the _enc / _hash column pair.
 *  - encrypt: Laravel Crypt (AES-256-GCM, the key follows APP_KEY), the ciphertext contains IV+tag, so the same plaintext yields a different ciphertext every time
 *  - hash: HMAC-SHA256 (the key is independent of APP_KEY, see config('innocms.pii_hmac_key'); when it is not
 *    configured it is derived from APP_KEY), used for equality lookups (such as searching orders by phone number), it is not reversible
 *
 * Red line: a decrypted view action must be recorded with InnoCMS\Common\Audit\AuditLogger::logPiiView();
 * page display must always go through mask(), and without the plaintext showing up it does not count as a "decrypted view".
 */
class FieldCrypto
{
    /**
     * Encrypt plaintext (returns base64 text, stored directly in the _enc column).
     */
    public static function encrypt(string $plain): string
    {
        return Crypt::encryptString($plain);
    }

    /**
     * Decrypt (every call site must write an audit record before and after).
     */
    public static function decrypt(?string $enc): string
    {
        if ($enc === null || $enc === '') {
            return '';
        }

        return Crypt::decryptString($enc);
    }

    /**
     * Lookup hash (HMAC-SHA256 hex). The input is trimmed and has inner whitespace removed before being hashed.
     */
    public static function hash(?string $plain): string
    {
        return hash_hmac('sha256', static::normalize($plain), static::hmacKey());
    }

    /**
     * Plaintext normalization: trim + remove all whitespace (phone numbers / ID numbers are often entered in space-separated groups).
     */
    public static function normalize(?string $plain): string
    {
        return preg_replace('/\s+/', '', trim((string) $plain)) ?? '';
    }

    /**
     * Generate the values of the two fields _enc + _hash in one step.
     *
     * @return array{enc: string, hash: string}
     */
    public static function pack(?string $plain): array
    {
        return [
            'enc'  => static::encrypt((string) $plain),
            'hash' => static::hash($plain),
        ];
    }

    /**
     * Masked display (138****1234). The server masks it immediately after decrypting.
     */
    public static function mask(?string $enc): string
    {
        $plain = static::decrypt($enc);

        if ($plain === '') {
            return '';
        }

        $len = mb_strlen($plain);

        if ($len <= 7) {
            return str_repeat('*', max($len - 2, 0)).mb_substr($plain, -2);
        }

        return mb_substr($plain, 0, 3).str_repeat('*', $len - 7).mb_substr($plain, -4);
    }

    protected static function hmacKey(): string
    {
        static $key = null;

        if ($key === null) {
            $configured = (string) config('innocms.pii_hmac_key');
            $key        = $configured !== '' ? $configured : hash('sha256', 'inno-pii-hmac|'.config('app.key'));

            if ($key === '') {
                throw new RuntimeException('PII HMAC key 未配置且 APP_KEY 为空');
            }
        }

        return $key;
    }
}
