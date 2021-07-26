<?php

namespace Tests\Unit\Events;

use Event;
use Tests\Feature\TestUtilities;
use Tests\TestCase;
use App\Events\UserDeleted;

class UserDeletedTest extends TestCase
{
    public function testEventFired()
    {
        $Event = new Event();
        $Event::fake();
        $userId = TestUtilities::createTestUserInDb();
        // Send event
        $Event::dispatch(new UserDeleted($userId));
        $Event::assertDispatched(UserDeleted::class, 1);
        TestUtilities::removeTestUsersInDb();
    }
}
