<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Tests\Feature;

use Database\Seeders\LocaleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InnoCMS\Common\Repositories\CategoryRepo;
use InnoCMS\Common\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * Regression: panel image upload widgets sometimes submit a COS-style signed
 * URL (relative path + "?content_id=&ts=&p=&cid=&sig=") into the categories.image
 * VARCHAR(255) column. handleData() must normalize to the relative path before
 * save, otherwise MySQL throws 1406 "Data too long for column 'image'".
 */
class CategoryImageNormalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LocaleSeeder::class);
    }

    private function makeCategory(string $image): array
    {
        return [
            'parent_id'    => 0,
            'slug'         => 'img-norm-'.uniqid(),
            'image'        => $image,
            'position'     => 0,
            'active'       => true,
            'translations' => [
                ['locale' => locale_code(), 'name' => 'Norm Cat', 'summary' => '', 'meta_title' => '', 'meta_keywords' => '', 'meta_description' => ''],
            ],
        ];
    }

    #[Test]
    public function test_create_strips_query_string_from_signed_url(): void
    {
        // Mirrors the actual failure mode: COS-style signed path with query
        // params appended (the `?` may already be stripped by the upload widget,
        // leaving `_content_id=...&ts=...&p=...&cid=...&sig=...`). Use realistic
        // sig length so the input is genuinely wider than what the upload widget
        // emits on prod.
        $relative   = 'static/media/墙贴/1/23f08b7e16583aabbaa09278756c3aed';
        $signedTail = '?content_id=file_00000000883081fd967d2926da6b5e83&ts=497044&p=fs&cid=1'
            .'&sig=04e4722d8431571f66196eba7b8d3037b9fc053ddc603ddd04e4722d8431571f6619'
            .'6eba7b8d3037b9fc053ddc603ddd04e4722d8431571f66196eba7b8d3037b9fc053ddc603d'
            .'dd04e4722d8431571f66196eba7b8d3037b9fc053ddc603ddd';
        $withQuery = $relative.$signedTail;

        $category = CategoryRepo::getInstance()->create($this->makeCategory($withQuery));

        $this->assertSame($relative, $category->fresh()->image);
        $this->assertStringNotContainsString('?', $category->fresh()->image);
        $this->assertStringNotContainsString('&', $category->fresh()->image);
        $this->assertStringNotContainsString('sig=', $category->fresh()->image);
    }

    #[Test]
    public function test_create_strips_underscore_separated_signed_payload(): void
    {
        // Real captured value: query params were concatenated with `_` instead of `?`
        // (upload widget stripped the `?` before submission).
        $relative = 'static/media/墙贴/1/23f08b7e16583aabbaa09278756c3aed';
        $dirty    = $relative.'_content_id=file_00000000883081fd967d2926da6b5e83&ts=497044&p=fs&cid=1'
            .'&sig=04e4722d8431571f66196eba7b8d3037b9fc053ddc603ddd04e4722d8431571f6619'
            .'6eba7b8d3037b9fc053ddc603ddd04e4722d8431571f66196eba7b8d3037b9fc053ddc603d'
            .'dd04e4722d8431571f66196eba7b8d3037b9fc053ddc603ddd';

        $category = CategoryRepo::getInstance()->create($this->makeCategory($dirty));

        $this->assertSame($relative, $category->fresh()->image);
        $this->assertStringNotContainsString('&', $category->fresh()->image);
        $this->assertStringNotContainsString('sig=', $category->fresh()->image);
    }

    #[Test]
    public function test_create_strips_absolute_url_to_path(): void
    {
        $category = CategoryRepo::getInstance()->create($this->makeCategory(
            'https://cdn.example.com/static/media/cat/abc123_content_id=file_xxx&ts=1&p=fs&cid=1&sig=deadbeef'
        ));

        // Whatever the URL was, only the path survives — no host, no query.
        $stored = $category->fresh()->image;
        $this->assertStringStartsWith('static/media/', $stored);
        $this->assertStringNotContainsString('cdn.example.com', $stored);
        $this->assertStringNotContainsString('?', $stored);
        $this->assertStringNotContainsString('&', $stored);
        $this->assertStringNotContainsString('sig=', $stored);
        $this->assertLessThanOrEqual(255, mb_strlen($stored));
    }

    #[Test]
    public function test_create_preserves_clean_relative_path(): void
    {
        $clean = 'static/media/cat/abc123.jpg';

        $category = CategoryRepo::getInstance()->create($this->makeCategory($clean));

        $this->assertSame($clean, $category->fresh()->image);
    }

    #[Test]
    public function test_update_normalizes_overlong_image(): void
    {
        $category = CategoryRepo::getInstance()->create($this->makeCategory('static/media/cat/clean.jpg'));

        $relative   = 'static/media/墙贴/1/23f08b7e16583aabbaa09278756c3aed';
        $signedTail = '?content_id=file_00000000883081fd967d2926da6b5e83&ts=497044&p=fs&cid=1'
            .'&sig=04e4722d8431571f66196eba7b8d3037b9fc053ddc603ddd04e4722d8431571f6619'
            .'6eba7b8d3037b9fc053ddc603ddd04e4722d8431571f66196eba7b8d3037b9fc053ddc603d'
            .'dd04e4722d8431571f66196eba7b8d3037b9fc053ddc603ddd';
        $withQuery = $relative.$signedTail;

        CategoryRepo::getInstance()->update($category->fresh(), $this->makeCategory($withQuery));

        $this->assertSame($relative, $category->fresh()->image);
    }
}
