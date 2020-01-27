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
        $user = DB::table('users')
            ->where('email_address', $email)
            ->first();
        if (empty($user)) {
            Log::debug('User does not exist with that email');
            return response([
                'success' => false,
                'message' => 'That email does not exist in our system',
              ], 404);
        }
        Log::debug('User exists');
        
        // check if the passwords match
        $passwordsMatch = Hash::check($password, $user->password);
        if (empty($passwordsMatch)) {
            Log::debug('Passwords dont match');
            return response([
                'success' => false,
                'message' => 'Password does not match'
            ], 403);
        }
        Log::debug('Passwords match');

        // Assign the user object into the sessions
        unset($user->password);
        session(['user' => $user]); // $request->session()->get('user'); // [{...}]

        // Create a session entry in the sessions table
        $SessionModel = new SessionModel;
        $sessionId = $request->session()->get('_token');
        $userId = $user->id;
        $session = $SessionModel->insert(['sessionId' => $sessionId, 'userId' => $userId]);

        // Set the user to logged in
        UserModel::where('email_address', $email)->update(['logged_in' => 0]);
        return response([
            'success' => 'maybe?'
        ]);
        // redirect to home

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