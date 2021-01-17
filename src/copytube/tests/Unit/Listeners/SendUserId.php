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
use Tests\TestCase;

class SendUserId extends TestCase
{
    public function testHandle()
    {
        // Make queue synchronous
        $defaultDriver = app("queue")->getDefaultDriver();
        app("queue")->setDefaultDriver("sync");

        // Setup data
        $UserModel = new UserModel();
        $UserModel->CreateQuery([
            "username" => "Test",
            "email_address" => "TestEmail@hotmail.com",
            "password" => $UserModel::generateHash("ValidPassword1"),
            "login_attempts" => 3,
            "logged_in" => 0,
        ]);
        $Database = new DB();
        $user = $Database
            ::table("users")
            ->whereRaw("username = 'Test'")
            ->first();
        $Mockery = new \Mockery();
        $listener = $Mockery::mock("SendUserId");

        // Assertions
        $listener->shouldReceive("handle")->once();
        $this->app->instance(\App\Listeners\SendUserId::class, $listener);
        dispatch(new ProcessUserDeleted($user->id));
        app("queue")->setDefaultDriver($defaultDriver);
        // TODO :: Assert redis got message
    }
}
