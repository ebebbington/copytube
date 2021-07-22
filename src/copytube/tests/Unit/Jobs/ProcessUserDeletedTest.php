<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessNewComment;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\TestUtilities;
use Tests\TestCase;
use App\Jobs\ProcessUserDeleted;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProcessUserDeletedTest extends TestCase
{
    use RefreshDatabase;
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
    }
}
