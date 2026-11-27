<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innocms.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Feature;

use Illuminate\Cache\FileStore;
use Illuminate\Cache\RateLimiter;
use ReflectionClass;
use Tests\TestCase;

/**
 * 限流器必须落在 file 缓存上。
 *
 * front_api / panel_api 中间件组挂了裸 ThrottleRequests（bootstrap/app.php），
 * 每个已登录 API 请求都会往限流缓存写 :timer + 计数两行。database store 下
 * 这是 insert ignore + lockForUpdate 事务，并发请求在 REPEATABLE-READ 下会
 * 撞死锁：SQLSTATE 40001 / 1213（icms_cache 表）。
 * config/cache.php 的 limiter 槽位把限流器指到 file store，从根上消除该死锁。
 *
 * 与 innoshop/enterprise PR #297、innoshop/factory PR #158 同源修复。
 */
class CacheRateLimiterStoreTest extends TestCase
{
    public function test_limiter_store_configured_as_file(): void
    {
        $this->assertSame('file', config('cache.limiter'));
    }

    public function test_rate_limiter_singleton_resolves_file_store(): void
    {
        $limiter = app(RateLimiter::class);

        $prop       = (new ReflectionClass($limiter))->getProperty('cache');
        $repository = $prop->getValue($limiter);

        $this->assertInstanceOf(FileStore::class, $repository->getStore());
    }

    public function test_limiter_store_independent_of_default_store(): void
    {
        // 测试环境 CACHE_STORE=array：默认缓存与限流缓存应各走各的驱动，
        // 修复只迁移限流器，不牵连全局缓存。
        $this->assertSame('array', config('cache.default'));
        $this->assertSame('file', config('cache.limiter'));
    }
}
