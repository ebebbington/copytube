<?php

namespace App\Http\Controllers;

use App\UserModel;
use App\SessionModel;

use http\Client\Curl\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use View;
use Cookie;
use App\Mail\Mail;
use Illuminate\Support\Str;

class LoginController extends Controller
{

    public function post (Request $request)
    {
        $loggingPrefix = "[LoginController - ".__FUNCTION__.'] ';
        Log::debug($loggingPrefix . 'Return view of `chat`');
        // get data
        $email = $request->input('email');
        $password = $request->input('password');
        $credentials = [
            'email_address' => $email,
            'password' => $password
        ];
        // Get user
        $query = [
            'where' => "email_address = '$email'",
            'limit' => 1
        ];
        $cacheKey = 'db:users:email_address='.$email;
        $User = new UserModel;
        $User->SelectQuery($query, $cacheKey);
        // Disable their account if no login attempts are left
        if ($User->login_attempts === 0) {
            $recoverToken = Str::random(32);
            $User->recover_token = $recoverToken;
            $User->UpdateQuery(['id' => $User->id], ['recover_token' => $recoverToken], 'db:users:email_address='.$email);
            $message
                = 'Your account has been locked. Please reset your password using the following link: 127.0.0.1:9002/recover?token='
                . $recoverToken;
            $Mail = new Mail($User->email_address, $User->username, 'Account Locked', $message);
            $Mail->send();
            return response([
                'success' => false,
                'message' => 'This account has been locked.'
            ], 403);
        }
        // Auth
        if (Auth::attempt($credentials)) {
            // Set the user to logged in
            $updated = $User->UpdateQuery(['email_address' => $email], ['logged_in' => 0], 'db:users:email_address='.$email);
            if ($updated === false) {
                Log::debug('Failed to update the model when updating logged_in');
                return response([
                    'success' => false,
                    'message' => 'Failed to update the model'
                ]);
            }

            return response([
                'success' => true
            ], 200);
        } else {
            // Reduce login attempts
            if ($User->login_attempts > 0) {
                $User->UpdateQuery(['email_address' => $credentials['email_address']],
                    ['login_attempts' => $User->login_attempts - 1],
                    'db:users:email_address='.$credentials['email_address']
                );
            }
            return response([
                'success' => false,
                'message' => 'Failed to authenticate'
            ], 403);
        }
    }

    public function get (Request $request)
    {
        $loggingPrefix = "[LoginController - ".__FUNCTION__.'] ';
        // session(['hi' => 'hello']);
        // $var = 'hi';
        // echo $var;
        // print_r($request->session()->get('_token'));
        return View::make('login')->with('title', 'Login');
    }
}
