<?php

namespace Tests\Unit\Events;

use App\Comment;
use App\Events\CommentAdded;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentAddedTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testEventFired()
    {
        $Event = new Event();
        $Event::fake();
        $comment = new Comment();
        $comment->comment = "Test";
        $comment->author = "Test";
        $comment->date_posted = "2020-02-02";
        $comment->user_id = 21;
        $comment->video_id = 3;
        $comment->save();
        // Send event
        $Event::dispatch(new CommentAdded($comment));
        $Event::assertDispatched(CommentAdded::class, 1);
    }
}
