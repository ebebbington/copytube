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
        $User = $request->session()->get('user');
        $sessionId = Cookie::get('sessionId');
        session(['user' => null]);
        Cookie::forget('sessionId');
        if (empty($User) || empty($sessionId)) {
            Log::debug('User or session id is empty');
            return redirect('/login');
        }

        $UserModel = new UserModel;
        $UserModel->UpdateQuery(['email_address' => $User->email_address], ['logged_in' => 1]);
        $SessionModel = new SessionModel;
        $SessionModel->DeleteQuery(['user_id' => $User->id]);

        return redirect('/login');
    }
}