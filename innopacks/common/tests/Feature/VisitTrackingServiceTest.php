<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Common\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use InnoCMS\Common\Models\Visit\Visit;
use InnoCMS\Common\Services\VisitTrackingService;
use InnoCMS\Common\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * A request without a User-Agent header made the visit tracking fail: MobileDetect::setUserAgent()
 * only accepts a string and Request::userAgent() returns null then.
 */
class VisitTrackingServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_tracks_a_visit_without_a_user_agent(): void
    {
        $request = Request::create('/');
        $request->headers->remove('User-Agent');

        $this->assertNull($request->userAgent());

        $visit = (new VisitTrackingService)->trackVisit($request, 'session-without-agent');

        $this->assertNotNull($visit);
        $this->assertSame('session-without-agent', $visit->session_id);
        $this->assertSame('desktop', $visit->device_type);
    }

    #[Test]
    public function it_tracks_a_visit_with_a_user_agent(): void
    {
        $request = Request::create('/');
        $request->headers->set('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1');

        $visit = (new VisitTrackingService)->trackVisit($request, 'session-with-agent');

        $this->assertNotNull($visit);
        $this->assertSame('mobile', $visit->device_type);
        $this->assertSame('Safari', $visit->browser);
        $this->assertSame('iOS', $visit->os);
    }

    #[Test]
    public function it_records_the_visit_in_the_database(): void
    {
        $request = Request::create('/');
        $request->headers->remove('User-Agent');

        (new VisitTrackingService)->trackVisit($request, 'session-in-the-database');

        $this->assertDatabaseHas('visits', ['session_id' => 'session-in-the-database']);
        $this->assertSame(1, Visit::query()->count());
    }
}
