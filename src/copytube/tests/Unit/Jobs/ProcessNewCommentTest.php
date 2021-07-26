<?php

namespace Tests\Unit\Jobs;

use App\CommentsModel;
use App\Jobs\ProcessNewComment;
use Queue;
use Tests\TestCase;

class ProcessNewCommentTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testProcessNewComment()
    {
        $Queue = new Queue();
        $Queue::fake();

        // Assert that no jobs were pushed...
        $Queue::assertNothingPushed();

        // Get data
        $CommentsModel = new CommentsModel();
        $comment = $CommentsModel->CreateQuery([
            "comment" => "Test",
            "author" => "Test",
            "date_posted" => "2020-02-02",
            "user_id" => 2,
            "video_posted_on" => "test",
        ]);

        //        Queue::assertPushed(ProcessNewComment::class, function ($job) {
        //            return 1 === 10;
        //        });

        // Run the faked job
        $job = (new ProcessNewComment($comment, "img/test"))->onQueue(
            "comments"
        );
        dispatch($job);

        // Expect it was called
        $Queue::assertPushedOn("comments", ProcessNewComment::class);
    }
}
