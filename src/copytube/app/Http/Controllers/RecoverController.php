<?php

namespace App\Http\Controllers;

use App\UserModel;
use App\SessionModel;

use http\Client\Curl\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use View;
use App\Mail\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class RecoverController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->query("token");
        $User = new UserModel();
        $user = $User->getByToken($token);
        if (!isset($token) || $user === false) {
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
        $User = new UserModel();
        $user = $User->getByEmail($email);
        if ($user === false) {
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
            return json_encode([
                "success" => false,
                "message" => "Token does not match",
            ]);
        }
        $validated = $User->validate([
            "username" => $user->username,
            "email" => $user->email_address,
            "password" => $password,
            "profile_picture" => $user->profile_picture,
        ]);
        if ($validated !== true) {
            return response([
                "success" => false,
                "message" => $validated,
            ]);
        }

        // update the new hashed password, login_attempts and recover_token
        $User->updateAfterRecover($email, $password);

        return response([
            "success" => true,
            "message" => "Successfully updated your password",
        ]);
    }
}
