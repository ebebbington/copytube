<?php

namespace Tests\Unit\Listeners;

use App\Events\CommentAdded;
use App\Listeners\SendComment;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class SendCommentTest extends TestCase
{
    public function testHandle ()
    {
        //$listener = new SendComment();
        //$comment = 'Hello';
        //$listener->handle(new CommentAdded($comment));
        // FIXME :: The callback below never fires. The above works but the connection
        //          just hangs. So what's needed is to assert the correct data
        //          that redis should receive
//        Redis::subscribe('realtime.comments.new', function ($message) {
//            print_r($message);
//        });
    }
}
