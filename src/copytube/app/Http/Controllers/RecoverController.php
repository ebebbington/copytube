<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use View;
use Illuminate\Support\Facades\Cookie;

class RecoverController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->query("token");
        $user = User::where("recover_token", $token)->first();
        if (!isset($token) || !$user) {
            return redirect()->route("login");
        } // because field is null and token might be null
        Log::debug("Going to queue cookie token: " . $token);
        Cookie::queue("recoverToken", $token, 3600);
        return View::make("recover")->with("title", "Recover");
    }

    public function post(Request $request)
    {
        // get data
        $token = $request->cookie("recoverToken");
        $email = $request->input("email");
        $password = $request->input("password");

        // get user by that email
        $user = User::where("email_address", $email)->first();
        if (!$user) {
            return response(
                [
                    "success" => false,
                    "message" => "Unable to authenticate",
                ],
                403
            );
        }

        // validate
        if ($user->recover_token !== $token) {
            return response(
                [
                    "success" => false,
                    "message" => "Token does not match",
                ],
                403
            );
        }
        $validated = User::validate(
            [
                "username" => $user->username,
                "email" => $user->email_address,
                "password" => $password,
                "profile_picture" => $user->profile_picture,
            ],
            User::$rules
        );
        if ($validated !== true) {
            return response(
                [
                    "success" => false,
                    "message" => $validated,
                ],
                403
            );
        }

        // update the new hashed password, login_attempts and recover_token
        $user->password = User::generateHash($password);
        $user->login_attempts = 3;
        $user->recover_token = null;
        $user->save();

        return response([
            "success" => true,
            "message" => "Successfully updated your password",
        ]);
    }
}
