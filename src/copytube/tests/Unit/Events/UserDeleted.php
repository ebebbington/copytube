<?php

namespace Tests\Unit\Events;

use App\CommentsModel;
use App\Events\CommentAdded;
use App\Jobs\ProcessNewComment;
use App\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Events\UserDeleted;

class UserDeleted extends TestCase
{
    public function testEventFired()
    {
        $Event = new Event();
        $Event::fake();
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

        // Send event
        $Event = new Event();
        $Event::dispatch(new UserDeleted($user->id));
        $Event::assertDispatched(\App\Events\UserDeleted::class, 1);
    }
}
