<?php

namespace Tests\Unit\Events;

use Illuminate\Support\Facades\Event;
use Tests\Feature\TestUtilities;
use Tests\TestCase;
use App\Events\UserDeleted;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserDeletedTest extends TestCase
{
    use RefreshDatabase;

    public function testEventFired()
    {
        $Event = new Event();
        $Event::fake();
        $userId = TestUtilities::createTestUserInDb();
        // Send event
        $Event::dispatch(new UserDeleted($userId));
        $Event::assertDispatched(UserDeleted::class, 1);
    }
}
