<?php


namespace App\Http\Controllers;


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
use Cookie;

class HomeController extends Controller
{
    public function index (Request $request)
    {
        $a = $request->session();
        $b = Cookie::get('sessionId');

        // Ensure our cookie is set, if not then send to login
        $sessionId = Cookie::get('sessionId');
        if (empty($sessionId)) {
            Log::debug('Session id is empty');
            return View::make('login')->with('title', 'Login');
        }

        // Get a user by that cookie
        $SessionModel = new SessionModel;
        $Session = $SessionModel->SelectQuery(['session_id' => $sessionId]);
        if (empty($Session) || !$Session) {
            Log::debug('No session was found with that session id e.g. it was never created in the db');
            return View::make('login')->with('title', 'Login');
        }
        $UserModel = new UserModel;
        $User = $UserModel->SelectQuery(['id' => $Session->user_id]);
        if (empty($User) || !$User || $Session->user_id !== $User->id) {
            Log::debug('No user was found with a matching user id in the sessions table');
            return View::make('login')->with('title', 'Login');
        }

        // Set the user in the session
        unset($User->password);
        session(['user' => $User]); // $request->session()->get('user'); // [{...}]
        Log::debug('Set the user inside the session object, returning home');

        return View::make('home')->with('title', 'Home');
    }
}
