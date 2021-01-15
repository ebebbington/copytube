<?php

namespace Tests\Unit\Listeners;

use App\CommentsModel;
use App\Events\CommentAdded;
use App\Jobs\ProcessNewComment;
use App\Jobs\RedisQueueTest;
use App\Listeners\SendComment;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\Listener;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class SendCommentTest extends TestCase
{
    public function testHandle()
    {
        // Make queue synchronous
        $defaultDriver = app("queue")->getDefaultDriver();
        app("queue")->setDefaultDriver("sync");

        // Setup data
        $CommentsModel = new CommentsModel();
        $comment = $CommentsModel->CreateQuery([
            "comment" => "Test",
            "author" => "Test",
            "date_posted" => "2020-02-02",
            "user_id" => 2,
            "video_posted_on" => "test",
        ]);
        $listener = \Mockery::mock("SendComment");
        $job = new ProcessNewComment($comment, "img/test");

        // Assertions
        Redis::shouldReceive("publish");
        dispatch($job)->onConnection("sync");
        $listener->shouldReceive("handle")->once();
        $this->app->instance(SendComment::class, $listener);
        dispatch($job)->onConnection("sync");
        app("queue")->setDefaultDriver($defaultDriver);
    }
}
