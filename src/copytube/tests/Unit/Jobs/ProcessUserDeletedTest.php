<?php

namespace Tests\Unit\Jobs;

use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use App\Jobs\ProcessUserDeleted;
use App\UserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProcessUserDeletedTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testProcessUserDeleted()
    {
        $Queue = new Queue();

        $Queue::fake();

        // Assert that no jobs were pushed...
        $Queue::assertNothingPushed();

        // Get data
        $user = UserModel::factory()->create();

        //        Queue::assertPushed(ProcessNewComment::class, function ($job) {
        //            return 1 === 10;
        //        });

        // Run the faked job
        $job = (new ProcessUserDeleted($user["id"]))->onQueue("users");
        dispatch($job);

        // Expect it was called

        $Queue::assertPushedOn("users", \App\Jobs\ProcessUserDeleted::class);
    }
}
