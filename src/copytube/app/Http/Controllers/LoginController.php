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

class LoginController extends Controller
{

    public function post (Request $request)
    {
        // get data
        $email = $request->input('email');
        $password = $request->input('password');

        // Check if that user exists with the same email
        $UserModel = new UserModel;
        $data = [
            'query' => ['email_address' => $email],
            'selectOne' => true
        ];
        $User = $UserModel->SelectQuery($data);
        if ($User === false) {
            Log::debug('User does not exist with that email');
            // todo :: reduce the login attempts
            return response([
                'success' => false,
                'message' => 'That email does not exist in our system',
              ], 404);
        }
        Log::debug('User exists');

        // check if the passwords match
        $passwordsMatch = Hash::check($password, $User->password);
        if (empty($passwordsMatch)) {
            // Reduce the login attempts
            // todo
            Log::debug('Passwords dont match');
            return response([
                'success' => false,
                'message' => 'Password does not match'
            ], 403);
        }
        Log::debug('Passwords match');

        // Check if login_attempts is 0 to make them recover it
        // todo

        // Check if they are already logged in, to just send them home
        // if ($User->logged_in === 0) {
        //     return response([
        //         'success' => true
        //     ], 200);
        // }

        // Create a session entry in the sessions table only
        $SessionModel = new SessionModel;
        $sessionId = $request->session()->get('_token');
        $userId = $User->id;
        $Session = $SessionModel->CreateQuery(['session_id' => $sessionId, 'user_id' => $userId]);
        Log::debug('Created the session in the database with the user id ' . $userId . ' of and the session value of ' . $sessionId);

        // Set the user to logged in
        // TESTING just set logged in to 1
        $UserModel->UpdateQuery(['email_address' => $email], ['logged_in' => 1]);
        $updated = $UserModel->UpdateQuery(['email_address' => $email], ['logged_in' => 0]);
        if ($updated === false) {
            Log::debug('Failed to update the model when updating logged_in');
            return response([
                'success' => false,
                'message' => 'Failed to update the model'
            ]);
        }

        // Create the cookie
        Cookie::queue('sessionId', $sessionId, 3600);
        Log::debug('Queued the cookie and oing to respond with a success');

        // All goes well, send them to the home
        return response([
            'success' => true
        ], 200);
    }

    public function get (Request $request)
    {
        // session(['hi' => 'hello']);
        // $var = 'hi';
        // echo $var;
        // print_r($request->session()->get('_token'));
        return View::make('login')->with('title', 'Login');
    }
}
