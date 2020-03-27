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
        // check its an ajax call
        if ($request->ajax() === false) {
            Log::debug('Request is not an ajax call');
            return response([
              'success' => false,
            ], 403);
        }
        Log::debug('Request to POST video is an ajax');

        // get data
        $comment = $request->input('comment');
        $datePosted = $request->input('datePosted');
        $videoPostedOn = $request->input('videoPostedOn');
        $user = Auth::user();
        $username = $user->username;
        if (empty($comment) || empty($datePosted) || empty($user) || empty($username) || empty($videoPostedOn)) {
            Log::debug('Some data wasn\'t provided');
            return response([
                'success' => false,
                'message' => 'Some data wasn\'t provided'
            ]);
        }

        // check the video actually exist
        $Videos = new VideosModel;
        $foundVideo = $Videos->getVideoByTitle($videoPostedOn);
        if (empty($foundVideo) || $foundVideo === false) {
            Log::debug('Video title or user does not exist');
            return reponse([
                'success' => false,
                'message' => 'Video does not exist'
            ]);
        }

        // Create the new comment
        $Comments = new CommentsModel;
        $cacheKey = 'db:comments:videoTitle='.$videoPostedOn;
        log::debug('GOING TO CREATE COMMENT WITH CACHE KEY OF: ' . $cacheKey);
        // TODO :: Validate
        $row = $Comments->createComment(['comment' => $comment, 'author' => $username, 'date_posted' => $datePosted, 'video_posted_on' => $videoPostedOn]);
        dispatch(new ProcessNewComment($row));
        $resData = [
            'success' => true,
            'data' => $username
            // image: $userImg
        ];
        return response()->json($resData);
    }

    public function getAllVideoTitles (Request $request)
    {
        $loggingPrefix = "[VideoController - ".__FUNCTION__.'] ';
        $title = $request->input('title');
        $Videos = new VideosModel;
        $query = [
            'limit' => -1,
        ];
        $cacheKey = 'db:videos:all';
        $videos = $Videos->SelectQuery($query, $cacheKey);
        $matchingTitles = [];
        foreach ($videos as $video) {
            if (strpos(strtolower($video->title), strtolower($title)) !== false) {
                array_push($matchingTitles, $video->title);
            }
        }
        return response()->json(['success' => true, 'data' => $matchingTitles]);
    }
}
