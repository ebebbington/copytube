<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Log;

class CommentAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;

    public $type;

    public $channel;

    /**
     * Create a new event instance.
     *
     * @param $comment
     */
    public function __construct($comment)
    {
        Log::info("[CommentAdded Event - Constructor] Been called.");
        $this->comment = $comment;
        $this->type = "new";
        $this->channel = "realtime.comments.new";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @codeCoverageIgnore
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("channel-name");
    }
}
