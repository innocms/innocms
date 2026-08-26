<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Panel\Interfaces;

interface Translator
{
    public function translate(string $source, string $target, string $text): string;

    public function batchTranslate(string $source, string $target, array $text): array;
}
