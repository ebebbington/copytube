<?php

namespace App\Http\Controllers;

use App\Mail\AccountLocked;
use App\UserModel;
use App\SessionModel;

use http\Client\Curl\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use View;
use Cookie;
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
        $UserModel = new UserModel;
        $user = $UserModel->getByEmail($email);
        if ($user === false) {
            return response([
                'success' => false,
                'message' => 'This account does not exist with that email'
            ], 403);
        }
        // Disable their account if no login attempts are left
        if ($user->login_attempts === 0) {
            $token = $UserModel->lockAccount($user->id, $email);
            if (!$token) {
                return response([
                    'success' => false,
                    'message' => 'Failed to lock your account'
                ], 500);
            }
            $title = 'Account Locked';
            $message
                = 'Your account has been locked. Please reset your password using the following link: 127.0.0.1:9002/recover?token='
                . $token;
            Log::debug($user->email_address);
            Mail::to($user->email_address)->send(new AccountLocked($title, $message));
            //$Mail = new Mail($user->email_address, $user->username, 'Account Locked', $message);
            //$Mail->send();
            return response([
                'success' => false,
                'message' => 'This account has been locked.'
            ], 403);
        }
        // Auth
        if (Auth::attempt($credentials)) {
            // Set the user to logged in
            $updated = $UserModel->updateLoggedIn(0, $email);
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
            if ($user->login_attempts > 0) {
                $UserModel->updateLoginAttempts($email, $user->login_attempts -1);
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
