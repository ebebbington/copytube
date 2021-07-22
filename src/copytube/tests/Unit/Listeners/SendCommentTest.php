<?php

namespace Tests\Unit\Listeners;

use App\CommentsModel;
use App\Jobs\ProcessNewComment;
use App\Listeners\SendComment;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;
use Mockery;

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
        $Mockery = new Mockery();
        $listener = $Mockery::mock("SendComment");
        $job = new ProcessNewComment($comment, "img/test");

        // Assertions
        $Redis = new Redis();
        $Redis::shouldReceive("publish");
        dispatch($job)->onConnection("sync");
        $listener->shouldReceive("handle")->once();
        $this->app->instance(SendComment::class, $listener);
        dispatch($job)->onConnection("sync");
        app("queue")->setDefaultDriver($defaultDriver);
    }
}
