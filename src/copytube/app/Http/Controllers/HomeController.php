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
        $videoRequested = $request->query('requestedVideo'); // default to some video
        $VideosModel = new VideosModel;
        $data = [
            'query' => ['title' => $videoRequested],
            'selectOne' => true
        ];
        $mainVideo = $VideosModel->SelectQuery($data);
        // Video requested could well be wrong or undefined
        if (empty($mainVideo) || !isset($mainVideo)) {
            $errorData = [
                'title' => 404,
                'errorCode' => 404,
                'errorMessage' => 'No video was found matching `'.$videoRequested . '`'
            ];
            return response()->view('errors.404', $errorData)->setStatusCode(404);
        }

        // Get rabbit hole videos that aren't main video
        $data = [
            'query' => 'title',
            'conditionalOperator' => '!=',
            'conditionalValue' => $videoRequested,
            'selectOne' => false,
            'count' => 2
        ];
        $rabbitHoleVideos = $VideosModel->SelectQuery($data);

        // Get the comments for the main video
        $Comments = new CommentsModel;
        $data = [
            'query' => ['video_posted_on' => $mainVideo->title],
            'selectOne' => false,
            'count' => null,
            'orderBy' => ['column' => 'date_posted', 'direction' => 'DESC']
        ];
        $comments = $Comments->SelectQuery($data);
        $comments = $Comments->formatDates($comments);

        return View::make('home')
            ->with('title', 'Home')
            ->with('username', $user->username)
            ->with('mainVideo', $mainVideo)
            ->with('rabbitHoleVideos', $rabbitHoleVideos)
            ->with('comments', $comments);
    }
}
