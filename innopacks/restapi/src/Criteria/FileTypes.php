<?php

/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Restapi\Criteria;

/**
 * Media manager file-type groups — extension map for the `type` filter
 * dimension of FileListCriteria (cmspacks/restapi#1).
 *
 * Empty / 'all' / unknown values normalize to '' (no filtering), matching the
 * lenient keyword behavior. matches() is only called with a non-empty,
 * normalized group key.
 */
final class FileTypes
{
    public const GROUPS = [
        'image'    => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'ico', 'avif'],
        'video'    => ['mp4', 'm4v', 'mov', 'avi', 'mkv', 'webm', 'wmv', 'flv', 'ts'],
        'audio'    => ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'wma'],
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'md', 'csv'],
    ];

    /**
     * Normalize a requested type into a known group key, '' = no filtering.
     */
    public static function normalize(string $type): string
    {
        $type = strtolower(trim($type));

        return isset(self::GROUPS[$type]) ? $type : '';
    }

    /**
     * Whether the file name belongs to the given type group (extension based).
     */
    public static function matches(string $fileName, string $type): bool
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        return $extension !== '' && in_array($extension, self::GROUPS[$type] ?? [], true);
    }
}
