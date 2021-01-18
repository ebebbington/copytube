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
    public function post(Request $request)
    {
        if (Auth::user()) {
            return response()->redirectTo("/home");
        }

        // get data
        $email = $request->input("email");
        $password = $request->input("password");
        $credentials = [
            "email_address" => $email,
            "password" => $password,
        ];

        // Get user
        $UserModel = new UserModel();
        $user = $UserModel->getByEmail($email);
        if ($user === false) {
            return response(
                [
                    "success" => false,
                    "message" => "This account does not exist with that email",
                ],
                403
            );
        }

        // Disable their account if no login attempts are left
        if ($user->login_attempts === 0) {
            $token = $UserModel->lockAccount($user->id, $email);
            $title = "Account Locked";
            $message =
                "Your account has been locked. Please reset your password using the following link: 127.0.0.1:9002/recover?token=" .
                $token;
            Mail::to($user->email_address)->send(
                new AccountLocked($title, $message)
            );
            return response(
                [
                    "success" => false,
                    "message" => "This account has been locked.",
                ],
                403
            );
        }
        // Auth
        if (Auth::attempt($credentials)) {
            // Set the user to logged in
            $UserModel->updateLoggedIn(0, $email);
            return response(
                [
                    "success" => true,
                ],
                200
            );
        } else {
            // Reduce login attempts
            if ($user->login_attempts > 0) {
                $UserModel->updateLoginAttempts(
                    $email,
                    $user->login_attempts - 1
                );
            }
            return response(
                [
                    "success" => false,
                    "message" => "Failed to authenticate",
                ],
                403
            );
        }
    }

    public function get()
    {
        if (Auth::user()) {
            return response()->redirectTo("/home");
        }
        return View::make("login")->with("title", "Login");
    }
}
