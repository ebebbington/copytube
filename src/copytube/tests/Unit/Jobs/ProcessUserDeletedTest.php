<?php

namespace Tests\Unit\Jobs;

use App\CommentsModel;
use App\Jobs\ProcessNewComment;
use App\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\TestUtilities;
use Tests\TestCase;
use App\Jobs\ProcessUserDeleted;

class ProcessUserDeletedTest extends TestCase
{
    public function testProcessUserDeleted()
    {
        $Queue = new Queue();

        $Queue::fake();

        // Assert that no jobs were pushed...
        $Queue::assertNothingPushed();

        // Get data
        $userId = TestUtilities::createTestUserInDb();

        //        Queue::assertPushed(ProcessNewComment::class, function ($job) {
        //            return 1 === 10;
        //        });

        // Run the faked job
        $job = (new ProcessUserDeleted($userId))->onQueue("users");
        dispatch($job);

        // Expect it was called

        $Queue::assertPushedOn("users", \App\Jobs\ProcessUserDeleted::class);

        TestUtilities::removeTestUsersInDb();
    }
}
