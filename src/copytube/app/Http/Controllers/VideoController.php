<?php

namespace App\Http\Controllers;

use App\UserModel;

use http\Client\Curl\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        // check its an ajax call
        if ($request->ajax() === false) {
            Log::debug('Request is not an ajax call');
            return response([
              'success' => false,
            ], 403);
        }
        Log::debug('Request to POST register is an ajax');

        // get data
        $comment = $request->input('comment');
        $datePosted = $request->input('datePosted');
        $videoPostedOn = $request->input('videoPostedOn');
        $User = $request->session()->get('user');
        $username = $User->username;
        if (empty($comment) || empty($datePosted) || empty($User) || empty($username) || empty($videoPostedOn)) {
            Log::debug('Some data wasn\'t provided');
            return response([
                'success' => false,
                'message' => 'Some data wasn\'t provided'
            ]);
        }

        // check the video and user actually exist
        $VideosModel = new VideosModel;
        $UserModel = new UserModel;
        $data = [
            'query' => ['title' => $videoPostedOn],
            'selectOne' => true
        ];
        $video = $VideosModel->SelectQuery($data);
        $data['query'] = ['username' => $username];
        $user = $UserModel->SelectQuery($data);
        if (empty($video) || empty($user)) {
            Log::debug('Video title or user does not exist');
            return reponse([
                'success' => false,
                'message' => 'Video title or user does not exist'
            ]);
        }

        // Create the new comment
        $CommentsModel = new CommentsModel;
        $a = $CommentsModel->CreateQuery(['comment' => $comment, 'author' => $username, 'date_posted' => $datePosted, 'video_posted_on' => $videoPostedOn]);

        $resData = [
            'success' => true,
            'data' => $username
            // image: $userImg
        ];
        return response()->json($resData);
    }

    public function getAllVideoTitles (Request $request)
    {
        $title = $request->input('title');
        $VideosModel = new VideosModel;
        $data = [
            'query' => [],
            'selectOne' => false
        ];
        $Videos = $VideosModel->SelectQuery($data);
        $matchingTitles = [];
        foreach ($Videos as $video) {
            if (strpos(strtolower($video->title), strtolower($title)) !== false) {
                array_push($matchingTitles, $video->title);
            }
        }
        return response()->json(['success' => true, 'data' => $matchingTitles]);
    }
}