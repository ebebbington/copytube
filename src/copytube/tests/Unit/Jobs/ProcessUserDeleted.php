<?php

namespace Tests\Unit\Jobs;

use App\CommentsModel;
use App\Jobs\ProcessNewComment;
use App\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProcessUserDeleted extends TestCase
{
    public function testProcessUserDeleted()
    {
        Queue::fake();

        // Assert that no jobs were pushed...
        Queue::assertNothingPushed();

        // Get data
        $UserModel = new UserModel();
        $user = $UserModel->CreateQuery([
            "username" => "Test",
            "email_address" => "TestEmail@hotmail.com",
            "password" => UserModel::generateHash("ValidPassword1"),
            "login_attempts" => 3,
            "logged_in" => 0,
        ]);

        //        Queue::assertPushed(ProcessNewComment::class, function ($job) {
        //            return 1 === 10;
        //        });

        // Run the faked job
        $user = DB::table("users")
            ->whereRaw("username = 'Test'")
            ->first();
        print_r($user->id);
        $job = (new \App\Jobs\ProcessUserDeleted($user->id))->onQueue("users");
        dispatch($job);

        // Expect it was called
        Queue::assertPushedOn("users", \App\Jobs\ProcessUserDeleted::class);

        DB::table("users")
            ->whereRaw("email_address = 'TestEmail@hotmail.com'")
            ->delete();
    }
}
