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
use Cookie;

class LogoutController extends Controller
{
    public function logout (Request $request)
    {
        // get and unset data
        $user = $request->session()->get('user');
        $sessionId = Cookie::get('sessionId');
        session(['user' => null]);
        Cookie::forget('sessionId');
        if (empty($user) || empty($sessionId)) {
            Log::debug('User or session id is empty');
            return redirect('/login');
        }

        // update db
        $User= new UserModel;
        $User->UpdateQuery(['email_address' => $user->email_address], ['logged_in' => 1]);
        $Session = new SessionModel;
        $Session->DeleteQuery(['user_id' => $user->id]);

        return redirect('/login');
    }
}