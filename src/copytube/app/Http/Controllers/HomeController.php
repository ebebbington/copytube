<?php

namespace App\Http\Controllers;

use App\UserModel;
use App\SessionModel;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use View;
use Auth;
use App\VideosModel;
use App\CommentsModel;
use Cookie;

class HomeController extends Controller
{
    public function index (Request $request)
    {
        $a = $request->session(); // whole session object
        $b = Cookie::get('sessionId'); // get cookie value
        $a = $request->requestedVideo; // method 1 of getting query strings .e.g GET /home?requestedVideo="dr"
        $b = $request->query('requestedVideo'); // method 2 of getting query stirngs

        // Ensure our cookie is set, if not then send to login
        $sessionId = Cookie::get('sessionId');
        if (empty($sessionId)) {
            // and if the session has expired and the user still exists, update the db e.g. a logged in user acessedd the home page after session has expired
            $User = $request->session()->get('user');
            if (!empty($User)) {
                Log::debug('user in session isnt empty so we are going to log them out');
                session(['user' => null]);
                $UserModel = new UserModel;
                $UserModel->UpdateQuery(['id' => $User->id], ['logged_in' => 1]);
                $SessionModel = new SessionModel;
                $SessionModel->DeleteQuery(['user_id' => $User->id]);
            }
            Log::debug(json_encode($User));
            Log::debug('Session id is empty');
            return View::make('login')->with('title', 'Login');
        }

        // Get a user by that cookie
        $SessionModel = new SessionModel;
        $data = [
            'query' => ['session_id' => $sessionId],
            'selectOne' => true
        ];
        $Session = $SessionModel->SelectQuery($data);
        if (empty($Session) || !$Session) {
            Log::debug('No session was found with that session id e.g. it was never created in the db');
            return View::make('login')->with('title', 'Login');
        }
        $UserModel = new UserModel;
        $data['query'] = ['id' => $Session->user_id];
        $User = $UserModel->SelectQuery($data);
        if (empty($User) || !$User || $Session->user_id !== $User->id) {
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
        $CommentsModel = new CommentsModel;
        $data = [
            'query' => ['video_posted_on' => $mainVideo->title],
            'selectOne' => false,
            'count' => null,
            'orderBy' => ['column' => 'date_posted', 'direction' => 'DESC']
        ];
        $Comments = $CommentsModel->SelectQuery($data);
        for ($i = 0; $i < sizeof($Comments); $i++) {
            list($year, $month, $day) = explode('-', $Comments[$i]->date_posted);
            $formattedDate = $day . '/' . $month . '/' . $year;
            $Comments[$i]->date_posted = $formattedDate;
        }

        return View::make('home')
            ->with('title', 'Home')
            ->with('username', $User->username)
            ->with('mainVideo', $mainVideo)
            ->with('rabbitHoleVideos', $rabbitHoleVideos)
            ->with('comments', $Comments);
    }
}
