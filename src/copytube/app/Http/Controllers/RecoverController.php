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
        $user = $User->getByToken($token);
        if (!isset($token) || $user === false) return redirect()->route('login'); // because field is null and token might be null
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
        $user = $User->getByEmail($email);
        if ($user === false) {
            return response([
                'success' => false,
                'Unable to authenticate'
            ], 403);
        }

        // validate
        $validated = $User->validate(['username' => $User->username, 'email' => $User->email_address, 'password' => $password]);

        // update the new hashed password, login_attempts and recover_token
        $User->updateAfterRecover($email, $request->input('password'));

        return response([
            'success' => true,
            'message' => 'Successfully updated your password'
        ]);
    }
}
