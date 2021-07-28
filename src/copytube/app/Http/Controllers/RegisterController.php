<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use View;

class RegisterController extends Controller
{
    /**
     * Submitting the Register form
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return ResponseFactory|Response
     */
    public function submit(Request $request)
    {
        // get data
        $username = $request->input("username");
        $email = $request->input("email");

        // Validate user details
        $profilePictureName = $request->hasFile("profile-picture")
            ? strtolower(
                $request->file("profile-picture")->getClientOriginalName()
            )
            : "sample.jpg";
        $passedValidation = User::validate(
            [
                "username" => $username,
                "email" => $email,
                "password" => $request->input("password"),
                "profile_picture" => $profilePictureName,
            ],
            User::$rules
        );
        if ($passedValidation !== true) {
            Log::info("Couldnt validate input: " . $passedValidation);
            return response(
                [
                    "success" => false,
                    "message" => $passedValidation,
                ],
                401
            );
        }
        if (User::where("email_address", $email)->first()) {
            return response(
                [
                    "success" => false,
                    "message" => "user already exists",
                ],
                403
            );
        }
        Log::info("User doesnt exists");

        // remove the raw password
        $hash = User::generateHash($request->input("password"));
        $_POST["password"] = null;
        $request->merge(["password" => null]);
        Log::info("Removed references to the raw password");

        // Save the user
        $user = new User();
        $user->username = $username;
        $user->email_address = $email;
        $user->password = $hash;
        $user->logged_in = 1;
        $user->login_attempts = 3;
        $user->save();
        //        if (empty($updated)) {
        //            return response([
        //              'success' => false,
        //              'message' => 'user didnt save into the database',
        //            ], 500);
        //        }
        // Get user id, save profile picture if required and update the profile_picture field
        $profilePicturePath = $request->hasFile("profile-picture")
            ? $request->file("profile-picture")->store($user->id)
            : "sample.jpg";
        $profilePicturePath = "img/" . $profilePicturePath;
        $user->profile_picture = $profilePicturePath;
        $user->save();
        Log::info("Saved a user");

        return response(
            [
                "success" => true,
            ],
            200
        );
    }

    /**
     * Display the register view
     *
     * @return {object} View    View to display
     */
    public function index()
    {
        return View::make("register")->with("title", "Register");
    }
}
