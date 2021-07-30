<?php

namespace App\Http\Controllers;

use App\Mail\AccountLocked;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

class LoginController extends Controller
{
    public function post(Request $request)
    {
        // get data
        $email = $request->input("email");
        $password = $request->input("password");
        $credentials = [
            "email_address" => $email,
            "password" => $password,
        ];

        // Get user
        $user = User::where("email_address", $email)->first();
        if (!$user) {
            return response(
                [
                    "success" => false,
                    "message" => "This account does not exist with that email",
                ],
                403
            );
        }

        // Disable their account if no login attempts are left
        // NOTE :: For future reference, we should pop this into a queue so it isn't blocking, but for just learning Laravel, there's not much point
        if ($user->login_attempts === 0) {
            $token = Str::random(32);
            $user->recover_token = $token;
            $user->save();
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
        if (!Auth::attempt($credentials)) {
            // Reduce login attempts
            if ($user->login_attempts > 0) {
                $user->login_attempts = $user->login_attempts - 1;
            }
            return response(
                [
                    "success" => false,
                    "message" => "Failed to authenticate",
                ],
                403
            );
        }

        // Set the user to logged in
        $user->logged_in = 0;
        $user->save();
        return response(
            [
                "success" => true,
            ],
            200
        );
    }

    public function get()
    {
        return View::make("login")->with("title", "Login");
    }
}
