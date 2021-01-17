<?php

namespace App\Jobs;

use App\CommentsModel;
use App\Events\CommentAdded;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProcessNewComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $comment;

    protected $profilePicture;

    /**
     * Create a new job instance.
     *
     * @param CommentsModel $comment
     * @param string        $profilePicture
     */
    public function __construct(CommentsModel $comment, string $profilePicture)
    {
        Log::info(
            "[ProcessNewComment - constructor] Received: " .
                json_encode($comment)
        );
        $this->comment = $comment;
        $this->profilePicture = $profilePicture;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment["profile_picture"] = $this->profilePicture;
        Log::info(
            "[ProcessNewComment - handle] Sending following data to CommentAdded event: " .
                json_encode($this->comment)
        );
        event(new CommentAdded($this->comment));
    }
}
