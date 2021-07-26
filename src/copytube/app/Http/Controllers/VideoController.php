<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessNewComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use View;
use App\CommentsModel;
use App\VideosModel;

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

        $VideosModel = new VideosModel();
        $mainVideo = $VideosModel->getVideoByTitle($videoNameRequested);
        Log::debug(
            "THE VIDEO of req title " .
                $videoNameRequested .
                ": " .
                json_encode($mainVideo)
        );

        // Video requested could well be wrong or undefined e.g. '' or 'Something Moreee'
        if (empty($mainVideo) || !isset($mainVideo)) {
            Log::error(
                $loggingPrefix .
                    "Requested main video of $videoNameRequested was not found"
            );
            abort(404);
        }

        $CommentsModel = new CommentsModel;
        $comments = $CommentsModel->getAllByVideoIdJoinUserProfilePic($mainVideo->id);
        Log::debug($comments);

        Log::info(
            $loggingPrefix .
                "Successfully retrieved a main video of " .
                $videoNameRequested .
                ":",
            [$mainVideo]
        );

        // Get rabbit hole videos that aren't main video
        $rabbitHoleVideos = $VideosModel->getRabbitHoleVideos(
            $videoNameRequested
        );
        Log::info($loggingPrefix . "Retrieved rabbit hole videos");

        $user = Auth::user();
        $renderData = [
            "title" => $mainVideo->title,
            "username" => $user->username,
            "email" => $user->email_address,
            "profilePicture" => $user->profile_picture,
            "mainVideo" => $mainVideo,
            "rabbitHoleVideos" => $rabbitHoleVideos,
            "comments" => $comments,
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
        $Videos = new VideosModel();
        $foundVideo = $Videos->getVideoByTitle($videoPostedOn);
        if (empty($foundVideo) || $foundVideo === false) {
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
        $Comments = new CommentsModel();
        $newComment = [
            "comment" => $comment,
            "author" => $username,
            "date_posted" => $datePosted,
            "video_id" => $foundVideo->id,
            "user_id" => $user->id,
        ];
        $validated = $Comments->validate($newComment);
        if ($validated !== true) {
            return response(
                [
                    "success" => false,
                    "message" => $validated,
                ],
                406
            );
        }
        $row = $Comments->createComment($newComment);
        dispatch(
            new ProcessNewComment(
                $row,
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
            $Videos = new VideosModel();
            $query = [
                "select" => "title",
                "where" => "title LIKE '%$title%'",
                "limit" => 10,
            ];
            $videos = $Videos->SelectQuery($query); // dont want to cache as we dont want a fixed list of titles
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
        $CommentsModel = new CommentsModel();
        $user = Auth::user();
        $comment = $CommentsModel->SelectQuery([
            "where" => "id = $commentId AND user_id = $user->id",
            "limit" => 1,
        ]);
        if (
            !$comment ||
            !isset($comment) ||
            $comment->author !== $user->username
        ) {
            return response()->json([
                "success" => false,
                "message" =>
                    "Unauthenticated. Not allowed to delete other peoples comments",
            ]);
        }
        $success = $CommentsModel->DeleteQuery([
            "id" => $commentId,
        ]);
        $cacheKey = str_replace(
            " ",
            "+",
            "db:comments:videoId=" . $comment->video_id
        );
        Cache::forget($cacheKey);
        return response()->json([
            "success" => $success,
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
        $CommentsModel = new CommentsModel();
        $user = Auth::user();
        $comment = $CommentsModel->SelectQuery([
            "where" => "id = $commentId AND user_id = $user->id",
            "limit" => 1,
        ]);
        if (
            !$comment ||
            !isset($comment) ||
            $comment->author !== $user->username
        ) {
            return response()->json([
                "success" => false,
                "message" =>
                    "Unauthenticated. Not allowed to delete other peoples comments",
            ]);
        }
        $success = $CommentsModel->UpdateQuery(
            [
                "id" => $commentId,
            ],
            [
                "comment" => $commentText,
            ]
        );
        $cacheKey = str_replace(
            " ",
            "+",
            "db:comments:videoId=" . $comment->video_id
        );
        Cache::forget($cacheKey);
        return response()->json([
            "success" => $success,
            "message" => "Successfully updated",
        ]);
    }
}
