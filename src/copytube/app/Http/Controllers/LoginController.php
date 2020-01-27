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

class LoginController extends Controller
{

    public function post (Request $request)
    {
        // get data
        $email = $request->input('email');
        $password = $request->input('password');

        // Check if that user exists with the same email
        $UserModel = new UserModel;
        $User = $UserModel->SelectQuery(['email_address' => $email], true);
        if ($User === false) {
            Log::debug('User does not exist with that email');
            return response([
                'success' => false,
                'message' => 'That email does not exist in our system',
              ], 404);
        }
        Log::debug('User exists');

        // check if the passwords match
        $passwordsMatch = Hash::check($password, $User->password);
        if (empty($passwordsMatch)) {
            Log::debug('Passwords dont match');
            return response([
                'success' => false,
                'message' => 'Password does not match'
            ], 403);
        }
        Log::debug('Passwords match');

        // Check if they are already logged in, to just send them home
        if ($User->logged_in === 0) {
            return response([
                'success' => true
            ], 200);
        }

        // Create a session entry in the sessions table
        $SessionModel = new SessionModel;
        $sessionId = $request->session()->get('_token');
        $userId = $User->id;
        $Session = $SessionModel->CreateQuery(['session_id' => $sessionId, 'user_id' => $userId]);

        // Set the user to logged in
        $updated = $UserModel->UpdateQuery(['email_address' => $email], ['logged_in' => 0]);
        if ($updated === false) {
            return response([
                'success' => false,
                'message' => 'Failed to update the model'
            ]);
        }

        // Assign the user object into the sessions
        unset($User->password);
        session(['user' => $User]); // $request->session()->get('user'); // [{...}]
        session(['session' => $Session]);

        return response([
            'success' => true
        ]);
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
