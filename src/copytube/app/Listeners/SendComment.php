<?php

namespace App\Listeners;

use App\Events\CommentAdded;
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
        Log::info(
            "[SendComment Listener - handle] Sending the comment to realtime.comments.new. Below is the event:"
        );
        Log::info(
            json_encode([
                "channel" => $event->channel,
                "type" => $event->type,
                "comment" => $event->comment,
            ])
        );
        Redis::publish(
            $event->channel,
            json_encode([
                "channel" => $event->channel,
                "type" => $event->type,
                "comment" => $event->comment,
            ])
        );
    }
}
