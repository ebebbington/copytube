<?php

namespace App\Listeners;

use App\Events\CommentAdded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SendComment
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CommentAdded  $event
     * @return void
     */
    public function handle(CommentAdded $event)
    {
        Log::info('[SendComment Listener - handle] Sending the comment to realtime.comments.new');
        Redis::publish('realtime.comments.new', json_encode($event->comment));
    }
}
