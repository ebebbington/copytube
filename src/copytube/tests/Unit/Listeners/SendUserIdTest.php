<?php

namespace Tests\Unit\Listeners;

use App\CommentsModel;
use App\Events\CommentAdded;
use App\Jobs\ProcessNewComment;
use App\Jobs\ProcessUserDeleted;
use App\Jobs\RedisQueueTest;
use App\Listeners\SendComment;
use App\UserModel;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\Listener;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;
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
    }
}
