<?php

/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Restapi\Tests\Criteria;

use Illuminate\Http\Request;
use InnoCMS\Restapi\Criteria\FileListCriteria;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the media file listing criteria (cmspacks/restapi#1):
 * the new `type` dimension joins the existing listing dimensions.
 */
class FileListCriteriaTest extends TestCase
{
    private function criteria(string $query): FileListCriteria
    {
        return FileListCriteria::fromRequest(Request::create('/api/panel/media/files?'.$query));
    }

    #[Test]
    public function it_defaults_to_no_type_filter(): void
    {
        $this->assertSame('', $this->criteria('')->type);
        $this->assertSame('', $this->criteria('type=all')->type);
    }

    #[Test]
    public function it_reads_video_type_filter(): void
    {
        $this->assertSame('video', $this->criteria('type=video')->type);
        $this->assertSame('video', $this->criteria('type=VIDEO')->type);
    }

    #[Test]
    public function it_ignores_unknown_type_values_leniently(): void
    {
        $this->assertSame('', $this->criteria('type=banana')->type);
    }

    #[Test]
    public function it_keeps_other_dimensions_untouched(): void
    {
        $criteria = $this->criteria('type=image&page=3&per_page=50&keyword=abc&sort=name&order=asc&base_folder=%2Fmedia&include_directories=1');

        $this->assertSame('image', $criteria->type);
        $this->assertSame(3, $criteria->page);
        $this->assertSame(50, $criteria->perPage);
        $this->assertSame('abc', $criteria->keyword);
        $this->assertSame('name', $criteria->sort);
        $this->assertSame('asc', $criteria->order);
        $this->assertSame('/media', $criteria->baseFolder);
        $this->assertTrue($criteria->includeDirectories);
    }
}
