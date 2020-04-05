<?php

namespace App\Http\Controllers;

use App\Events\CommentAdded;
use App\Jobs\ProcessNewComment;
use App\UserModel;

use http\Client\Curl\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use View;
use App\CommentsModel;
use App\VideosModel;

class VideoController extends Controller
{
    public function postComment (Request $request)
    {
        $loggingPrefix = "[VideoController - ".__FUNCTION__.'] ';

        // get data
        $comment = $request->input('comment');
        $datePosted = $request->input('datePosted');
        $videoPostedOn = $request->input('videoPostedOn');
        $user = Auth::user();
        $username = $user->username;
        if (empty($videoPostedOn)) {
            Log::debug('Some data wasn\'t provided');
            return response([
                'success' => false,
                'message' => 'Some data wasn\'t provided'
            ], 403);
        }

        // check the video actually exist
        $Videos = new VideosModel;
        $foundVideo = $Videos->getVideoByTitle($videoPostedOn);
        if (empty($foundVideo) || $foundVideo === false) {
            Log::debug('Video title or user does not exist');
            return response([
                'success' => false,
                'message' => 'Video does not exist'
            ], 404);
        }

        // Create the new comment
        $Comments = new CommentsModel;
        $cacheKey = 'db:comments:videoTitle='.$videoPostedOn;
        $newComment = [
            'comment' => $comment,
            'author' => $username,
            'date_posted' => $datePosted,
            'video_posted_on' => $videoPostedOn,
            'user_id' => $user['id']
        ];
        $validated = $Comments->validate($newComment);
        if ($validated !== true) {
            return response([
                'success' => false,
                'message' => $validated
            ], 401);
        }
        $row = $Comments->createComment($newComment);
        dispatch(new ProcessNewComment($row, $user->profile_picture));
        $resData = [
            'success' => true
        ];
        return response()->json($resData);
    }

    public function autocomplete (Request $request)
    {
        $loggingPrefix = "[VideoController - ".__FUNCTION__.'] ';
        $title = $request->input('title');
        $titles = [];
        if (!empty($title)) {
            $Videos = new VideosModel;
            $query = [
                'select' => 'title',
                'where' => "title LIKE '%$title%'",
                'limit' => 10,
            ];
            $videos = $Videos->SelectQuery($query); // dont want to cache as we dont want a fixed list of titles
            if (!empty($videos)) {
                $titles = array_column($videos->toArray(), 'title');
            }
        } else { $titles = []; }
        return response()->json(['success' => true, 'data' => $titles]);
    }
}
