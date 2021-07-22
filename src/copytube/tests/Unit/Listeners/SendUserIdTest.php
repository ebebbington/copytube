<?php

namespace Tests\Unit\Listeners;

use App\Jobs\ProcessUserDeleted;
use Tests\Feature\TestUtilities;
use Tests\TestCase;

class SendUserIdTest extends TestCase
{
    public function testHandle()
    {
        // Make queue synchronous
        $defaultDriver = app("queue")->getDefaultDriver();
        app("queue")->setDefaultDriver("sync");

        // Setup data
        $userId = TestUtilities::createTestUserInDb();
        $Mockery = new \Mockery();
        $listener = $Mockery::mock("SendUserId");

        // Assertions
        $listener->shouldReceive("handle")->once();
        $this->app->instance(\App\Listeners\SendUserId::class, $listener);
        dispatch(new ProcessUserDeleted($userId));
        app("queue")->setDefaultDriver($defaultDriver);
        TestUtilities::removeTestUsersInDb();
    }
}
