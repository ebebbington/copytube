<?php

namespace App\Http\Controllers;

use App\UserModel;
use App\SessionModel;
use http\Client\Curl\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use View;
use App\VideosModel;
use App\CommentsModel;
use Cookie;

class HomeController extends Controller
{

    public function index (Request $request)
    {
        $loggingPrefix = "[HomeController - ".__FUNCTION__.'] ';
        Log::info($loggingPrefix);
        // Authenticate the user

        // Old code before adding laravel auth
//        // Get a user by that cookie
//        $Session = new SessionModel;
//        $data = [
//            'query' => ['session_id' => $sessionId],
//            'selectOne' => true
//        ];
//        $found = $Session->SelectQuery($data);
//        if (empty($found)) {
//            Log::debug('No session was found with that session id e.g. it was never created in the db');
//            return View::make('login')->with('title', 'Login');
//        }

        $user = Auth::user();
        Log::info($loggingPrefix . 'Retrieved the user from Auth');
        // Old code before adding laravel auth
//        $User = new UserModel;
//        $data['query'] = ['id' => $Session->user_id];
//        $found = $User->SelectQuery($data);
//        if (empty($found) || !$User || $Session->user_id !== $User->id) {
//            Log::debug('No user was found with a matching user id in the sessions table');
//            return View::make('login')->with('title', 'Login');
//        }

        // Old code before adding laravel auth
//        // Set the user in the session
//        unset($User->password);
//        session(['user' => $User]); // $request->session()->get('user'); // [{...}]
//        Log::debug('Set the user inside the session object, returning home');

        // Get the videos
        $videoRequested = $request->query('requestedVideo') ?? 'Something More'; // default to some video if we are routing to /home
        $VideosModel = new VideosModel;
        $query = [
            'where' => "title = '$videoRequested'",
            'limit' => 1,
        ];
        $cacheKey = 'db:videos:title=' . $videoRequested;
        $mainVideo = $VideosModel->SelectQuery($query, $cacheKey);
        // Video requested could well be wrong or undefined e.g. '' or 'Something Moreee'
        if (empty($mainVideo) || !isset($mainVideo)) {
            Log::error($loggingPrefix . "Requested main video of $videoRequested was not found");
            $errorCode = 404;
            $errorData = ['title' => $errorCode, 'errorCode' => $errorCode,
                'errorMessage' => 'No video was found matching `'.$videoRequested . '`'
            ];
            return response()->view('errors.404', $errorData)->setStatusCode($errorCode);
        }
        Log::info($loggingPrefix . 'Successfully retrieved a main video of '.$videoRequested.':', [$mainVideo]);

        // Get rabbit hole videos that aren't main video
        $query = [
            'where' => "title != '$videoRequested'",
            'limit' => 2
        ];
        $cacheKey = 'db:videos:title!='.$videoRequested.'&limit=2';
        $rabbitHoleVideos = $VideosModel->SelectQuery($query, $cacheKey);
        Log::info($loggingPrefix . "Retrieved rabbit hole videos where ".$query['where'].' and limit is '.$query['limit'].':',[$rabbitHoleVideos]);

        // Get the comments for the main video
        $Comments = new CommentsModel;
        $query = [
            'where' => "video_posted_on = '$mainVideo->title'",
            'limit' => -1,
            'orderBy' => ['column' => 'date_posted', 'direction' => 'DESC']
        ];
        $cacheKey = "db:comments:videoTitle=".$mainVideo->title;
        $comments = $Comments->SelectQuery($query, $cacheKey);
        Log::info($loggingPrefix . 'Retrieved comments where '.$query['where'].', limit of '.$query['limit'].', length is: '.sizeof($comments));
        $comments = $Comments->formatDates($comments);

        $renderData = [
            'title' => 'Home',
            'username' => $user->username,
            'mainVideo' => $mainVideo,
            'rabbitHoleVideos' => $rabbitHoleVideos,
            'comments' => $comments
        ];
        Log::info($loggingPrefix . 'Return view of `home` with the following data:', $renderData);
        return View::make($renderData['title'])
            ->with('title', $renderData['title'])
            ->with('username', $renderData['username'])
            ->with('mainVideo', $renderData['mainVideo'])
            ->with('rabbitHoleVideos', $renderData['rabbitHoleVideos'])
            ->with('comments', $renderData['comments']);
    }
}
