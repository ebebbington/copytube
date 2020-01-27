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

class HomeController extends Controller
{
    public function index (Request $request)
    {
        // Check user session exists
        if (empty($request->session()->get('user'))) {
            return View::make('login')->with('title', 'Login');
        }

        // Check the sessions match
        $User = $request->session()->get('user');
        $SessionModel = new SessionModel();
        $Session = $SessionModel->SelectQuery(['user_id' => $User->id]);
        if ($Session === false) {
            return View::make('login')->with('title', 'Login');
        }

        // Check the session equals the laravel session
        // fixme :: i get the feeling _token doesnt refresh, so it will always equal the token...
        if ($Session->session_id !== $request->session()->get('_token')) {
            return View::make('login')->with('title', 'Login');
        }

        return View::make('home')->with('title', 'Home');
    }
}
