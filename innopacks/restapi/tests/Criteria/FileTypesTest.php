<?php

/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Restapi\Tests\Criteria;

use InnoCMS\Restapi\Criteria\FileTypes;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the media file-type extension groups (cmspacks/restapi#1).
 */
class FileTypesTest extends TestCase
{
    #[Test]
    public function it_matches_video_group_by_extension_case_insensitively(): void
    {
        $this->assertTrue(FileTypes::matches('intro.mp4', 'video'));
        $this->assertTrue(FileTypes::matches('CLIP.MOV', 'video'));
        $this->assertTrue(FileTypes::matches('lecture-01.m4v', 'video'));
        $this->assertFalse(FileTypes::matches('notes.txt', 'video'));
    }

    #[Test]
    public function it_matches_image_audio_document_groups(): void
    {
        $this->assertTrue(FileTypes::matches('cover.png', 'image'));
        $this->assertTrue(FileTypes::matches('song.mp3', 'audio'));
        $this->assertTrue(FileTypes::matches('spec.pdf', 'document'));
        $this->assertFalse(FileTypes::matches('cover.png', 'video'));
    }

    #[Test]
    public function it_rejects_files_without_extension(): void
    {
        $this->assertFalse(FileTypes::matches('README', 'video'));
        $this->assertFalse(FileTypes::matches('.gitignore', 'image'));
    }

    #[Test]
    public function it_normalizes_known_groups_and_lowercases(): void
    {
        $this->assertSame('video', FileTypes::normalize('video'));
        $this->assertSame('video', FileTypes::normalize('VIDEO'));
        $this->assertSame('image', FileTypes::normalize(' image '));
    }

    #[Test]
    public function it_normalizes_all_empty_and_unknown_to_no_filter(): void
    {
        $this->assertSame('', FileTypes::normalize('all'));
        $this->assertSame('', FileTypes::normalize(''));
        $this->assertSame('', FileTypes::normalize('banana'), '未知 type 宽松处理为不过滤');
    }
}
