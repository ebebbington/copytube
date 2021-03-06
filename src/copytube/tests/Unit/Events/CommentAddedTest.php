<?php

namespace Tests\Unit\Events;

use App\CommentsModel;
use App\Events\CommentAdded;
use App\Jobs\ProcessNewComment;
use Illuminate\Support\Facades\Event;
use Tests\Feature\TestUtilities;
use Tests\TestCase;

class CommentAddedTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testEventFired()
    {
        TestUtilities::removeTestUsersInDb();
        $Event = new Event();
        $Event::fake();
        $CommentsModel = new CommentsModel();
        $comment = $CommentsModel->CreateQuery([
            "comment" => "Test",
            "author" => "Test",
            "date_posted" => "2020-02-02",
            "user_id" => 2,
            "video_posted_on" => "test",
        ]);
        // Send event
        $Event::dispatch(new CommentAdded($comment));
        $Event::assertDispatched(CommentAdded::class, 1);
    }
}
