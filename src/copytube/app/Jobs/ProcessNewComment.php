<?php

namespace App\Jobs;

use App\Comment;
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

    protected $videoPostedOn;

    /**
     * Create a new job instance.
     *
     * @param Comment $comment
     * @param string        $profilePicture
     */
    public function __construct(
        Comment $comment,
        string $profilePicture,
        string $videoPostedOn
    ) {
        Log::info(
            "[ProcessNewComment - constructor] Received: " .
                json_encode($comment)
        );
        $this->comment = $comment;
        $this->profilePicture = $profilePicture;
        $this->videoPostedOn = $videoPostedOn;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment["profile_picture"] = $this->profilePicture;
        $this->comment["video_posted_on"] = $this->videoPostedOn;
        Log::info(
            "[ProcessNewComment - handle] Sending following data to CommentAdded event: " .
                json_encode($this->comment)
        );
        event(new CommentAdded($this->comment));
    }
}
