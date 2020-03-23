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
use App\Mail\Mail;
use Illuminate\Support\Str;

class RecoverController extends Controller
{
    public function index (Request $request)
    {
        $loggingPrefix = "[RecoverController - ".__FUNCTION__.'] ';
        $token = $request->query('token');
        $User = new UserModel;
        $query = [
            'where' => "recover_token = '$token'",
            'limit' => 1
        ];
        $found = $User->SelectQuery($query);
        if (!isset($token) || $found === false) return redirect()->route('login'); // because field is null and token might be null
        Cookie::queue('recoverToken', $token, 10);
        return View::make('recover')->with('title', 'Recover');
    }

    public function post (Request $request)
    {
        $loggingPrefix = "[RecoverController - ".__FUNCTION__.'] ';
        // get data
        $token = $request->session()->get('recoverToken');
        $email = $request->input('email');
        $password = $request->input('password');

        // get user by that email
        $User = new UserModel;
        $query = [
            'where' => "recover_token = '$token' and email_address = '$email'",
            'limit' => 1
        ];
        $found = $User->SelectQuery($query);
        if ($found === false) {
            return response([
                'success' => false,
                'Unable to authenticate'
            ], 403);
        }

        // validate
        $validated = $User->validate(['username' => $User->username, 'email' => $User->email_address, 'password' => $password]);

        // hash password
        $hash = Hash::make($request->input('password'), [
            'rounds' => 12,
        ]);

        // update the new hashed password, login_attempts and recover_token
        $User->UpdateQuery(['email_address' => $email], ['password' => $hash, 'login_attempts' => 3, 'recover_token' => null]);

        return response([
            'success' => true,
            'message' => 'Successfully updated your password'
        ]);
    }
}
