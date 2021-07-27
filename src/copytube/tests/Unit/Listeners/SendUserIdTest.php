<?php

namespace Tests\Unit\Listeners;

use App\Jobs\ProcessUserDeleted;
use App\UserModel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendUserIdTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testHandle()
    {
        // Make queue synchronous
        $defaultDriver = app("queue")->getDefaultDriver();
        app("queue")->setDefaultDriver("sync");

        // Setup data
        $user = UserModel::factory()->create();
        $Mockery = new \Mockery();
        $listener = $Mockery::mock("SendUserId");

        // Assertions
        $listener->shouldReceive("handle")->once();
        $this->app->instance(\App\Listeners\SendUserId::class, $listener);
        dispatch(new ProcessUserDeleted($user["id"]));
        app("queue")->setDefaultDriver($defaultDriver);
    }
}
