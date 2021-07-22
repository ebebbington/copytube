<?php

namespace Tests\Unit\Events;

use App\CommentsModel;
use App\Events\CommentAdded;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentAddedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testEventFired()
    {
        $Event = new Event();
        $Event::fake();
        $CommentsModel = new CommentsModel();
        $comment = $CommentsModel->CreateQuery(
            [
            "comment" => "Test",
            "author" => "Test",
            "date_posted" => "2020-02-02",
            "user_id" => 2,
            "video_posted_on" => "test",
            ]
        );
        // Send event
        $Event::dispatch(new CommentAdded($comment));
        $Event::assertDispatched(CommentAdded::class, 1);
    }
}
