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
        $sessionId = Cookie::get('sessionId');
        if (empty($sessionId)) {
            // they need a session id e.g. need to login
            Log::debug('Session id is empty');
            $user = $request->session()->get('user');
            if (!empty($user)) {
                // Clean out the user object as well
                Log::debug('user in session isnt empty so we are going to log them out');
                $User = new UserModel;
                $User->logout($user->id);
            }
            return View::make('login')->with('title', 'Login');
        }

        // Get a user by that cookie
        $Session = new SessionModel;
        $data = [
            'query' => ['session_id' => $sessionId],
            'selectOne' => true
        ];
        $found = $Session->SelectQuery($data);
        if (empty($found)) {
            Log::debug('No session was found with that session id e.g. it was never created in the db');
            return View::make('login')->with('title', 'Login');
        }
        $User = new UserModel;
        $data['query'] = ['id' => $Session->user_id];
        $found = $User->SelectQuery($data);
        if (empty($found) || !$User || $Session->user_id !== $User->id) {
            Log::debug('No user was found with a matching user id in the sessions table');
            return View::make('login')->with('title', 'Login');
        }

        // Set the user in the session
        unset($User->password);
        session(['user' => $User]); // $request->session()->get('user'); // [{...}]
        Log::debug('Set the user inside the session object, returning home');

        // Get the videos
        $videoRequested = $request->query('requestedVideo') ?? 'Something More'; // default to some video
        $VideosModel = new VideosModel;
        $data = [
            'query' => ['title' => $videoRequested],
            'selectOne' => true
        ];
        $mainVideo = $VideosModel->SelectQuery($data);
        // override
        if ($mainVideo === false) {
            $videoRequested = 'Something More';
            $data['query'] = ['title' => $videoRequested];
            $mainVideo = $VideosModel->SelectQuery($data);
        }
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
        // format the date
        for ($i = 0; $i < sizeof($comments); $i++) {
            list($year, $month, $day) = explode('-', $comments[$i]->date_posted);
            $formattedDate = $day . '/' . $month . '/' . $year;
            $comments[$i]->date_posted = $formattedDate;
        }

        return View::make('home')
            ->with('title', 'Home')
            ->with('username', $User->username)
            ->with('mainVideo', $mainVideo)
            ->with('rabbitHoleVideos', $rabbitHoleVideos)
            ->with('comments', $comments);
    }
}
