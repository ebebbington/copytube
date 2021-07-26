<?php

namespace App\Listeners;

use App\Events\UserDeleted;
use Redis;

class SendUserId
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
     * @param UserDeleted $event
     *
     * @return void
     */
    public function handle(UserDeleted $event)
    {
        Redis::publish(
            $event->channel,
            json_encode([
                "channel" => $event->channel,
                "type" => $event->type,
                "userId" => $event->userId,
            ])
        );
    }
}
