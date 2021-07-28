<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessNewComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use View;
use App\Comment;
use App\Video;

class VideoController extends Controller
{
    private function getLoggingPrefix(string $functionName): string
    {
        return "[VideoController - " . $functionName . "] ";
    }

    /**
     * Ex: AJAX GET /video?title=dhddhh
     * Gets a video to watch and it's comments, and extra rabbit hold vids
     * @param Request $request
     */
    public function index(Request $request)
    {
        $loggingPrefix = $this->getLoggingPrefix(__FUNCTION__);

        $videoNameRequested = $request->input("requestedVideo");
        if (
            !$videoNameRequested ||
            !isset($videoNameRequested) ||
            empty($videoNameRequested)
        ) {
            abort(403);
        }

        $mainVideo = Video::where("title", $videoNameRequested)->first();
        // Video requested could well be wrong or undefined e.g. '' or 'Something Moreee'
        if (empty($mainVideo) || !isset($mainVideo)) {
            Log::error(
                $loggingPrefix .
                    "Requested main video of $videoNameRequested was not found"
            );
            abort(404);
        }

        Log::info(
            $loggingPrefix .
                "Successfully retrieved a main video of " .
                $videoNameRequested .
                ":",
            [$mainVideo]
        );

        // Get rabbit hole videos that aren't main video
        $rabbitHoleVideos = Video::where("id", "!=", $mainVideo["id"])
            ->limit(2)
            ->get();
        Log::info($loggingPrefix . "Retrieved rabbit hole videos");

        $user = Auth::user();
        $renderData = [
            "title" => $mainVideo["title"],
            "username" => $user->username,
            "email" => $user->email_address,
            "profilePicture" => $user->profile_picture,
            "mainVideo" => $mainVideo,
            "rabbitHoleVideos" => $rabbitHoleVideos,
            "comments" => $mainVideo->comments,
        ];
        Log::info(
            $loggingPrefix . "Return view of `home` with the following data:",
            $renderData
        );
        return View::make("watch")
            ->with("title", $renderData["title"])
            ->with("username", $renderData["username"])
            ->with("mainVideo", $renderData["mainVideo"])
            ->with("rabbitHoleVideos", $renderData["rabbitHoleVideos"])
            ->with("comments", $renderData["comments"])
            ->with("profilePicture", $renderData["profilePicture"])
            ->with("email", $renderData["email"])
            ->with("loggedInUserId", $user->id);
    }

    public function postComment(Request $request)
    {
        $loggingPrefix = $this->getLoggingPrefix(__FUNCTION__);

        Log::info($loggingPrefix . "Start");

        // get data
        $comment = $request->input("comment");
        $datePosted = $request->input("datePosted");
        $videoPostedOn = $request->input("videoPostedOn");
        $user = Auth::user();
        $username = $user->username;
        if (empty($videoPostedOn)) {
            Log::debug('Some data wasn\'t provided');
            return response(
                [
                    "success" => false,
                    "message" => 'Some data wasn\'t provided',
                ],
                403
            );
        }

        // check the video actually exist
        $foundVideo = Video::where("title", $videoPostedOn)->first();
        if (empty($foundVideo) || !$foundVideo) {
            Log::debug("Video title or user does not exist");
            return response(
                [
                    "success" => false,
                    "message" => "Video does not exist",
                ],
                404
            );
        }

        // Create the new comment
        $newComment = [
            "comment" => $comment,
            "author" => $username,
            "date_posted" => $datePosted,
            "video_id" => $foundVideo->id,
            "user_id" => $user->id,
        ];
        $validated = Comment::validate($newComment, Comment::$rules);
        if ($validated !== true) {
            return response(
                [
                    "success" => false,
                    "message" => $validated,
                ],
                406
            );
        }
        $Comment = Comment::create([
            "comment" => $comment,
            "author" => $username,
            "date_posted" => $datePosted,
            "video_id" => $foundVideo->id,
            "user_id" => $user->id,
        ]);
        dispatch(
            new ProcessNewComment(
                $Comment,
                $user->profile_picture,
                $foundVideo->title
            )
        );
        $resData = [
            "success" => true,
        ];
        return response()->json($resData);
    }

    public function autocomplete(Request $request)
    {
        $loggingPrefix = $this->getLoggingPrefix(__FUNCTION__);
        Log::info($loggingPrefix . "Start");
        $title = $request->input("title");
        $titles = [];
        if (!empty($title)) {
            $videos = Video::where("title", "LIKE", "%$title%")
                ->take(10)
                ->get();
            if (!empty($videos)) {
                $titles = array_column($videos->toArray(), "title");
            }
        } else {
            $titles = [];
        }
        return response()->json(["success" => true, "data" => $titles]);
    }

    public function deleteComment(Request $request)
    {
        $commentId = $request->input("id");
        if (!$commentId) {
            return response()->json([
                "success" => false,
                "message" => "Failed to delete. Id must be provided",
            ]);
        }
        $user = Auth::user();
        $comment = Comment::where("id", $commentId)->first();
        if (!$comment || ($comment && $comment->user_id !== $user->id)) {
            return response()->json([
                "success" => false,
                "message" =>
                    "Unauthenticated. Not allowed to delete other peoples comments",
            ]);
        }
        $comment->delete();
        return response()->json([
            "success" => true,
            "message" => "Successfully deleted",
        ]);
    }

    public function updateComment(Request $request)
    {
        $commentId = $request->input("id");
        $commentText = $request->input("newComment");
        if (!$commentId || !$commentText) {
            return response()->json([
                "success" => false,
                "message" =>
                    "Failed to delete. The id and text must be provided",
            ]);
        }
        $user = Auth::user();
        $comment = Comment::where("id", "=", $commentId)->first();
        if (!$comment || ($comment && $comment->user_id !== $user->id)) {
            return response()->json([
                "success" => false,
                "message" =>
                    "Unauthenticated. Not allowed to delete other peoples comments",
            ]);
        }
        $comment->comment = $commentText;
        $comment->save();
        return response()->json([
            "success" => true,
            "message" => "Successfully updated",
        ]);
    }
}
