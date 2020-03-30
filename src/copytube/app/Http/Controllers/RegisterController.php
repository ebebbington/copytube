<?php

namespace App\Http\Controllers;

use App\UserModel;

use http\Client\Curl\User;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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
        $loggingPrefix = "[RegisterController - ".__FUNCTION__.'] ';
        // check its an ajax call
        if ($request->ajax() === false) {
            Log::debug('Request is not an ajax call');
            return response([
              'success' => false,
            ], 403);
        }
        Log::info('Request to POST register is an ajax');

        // get data
        $username = $request->input('username');
        $email    = $request->input('email');
        $hash = UserModel::generateHash($request->input('password'));

        // Check if empty
        Log::info('Retrieved input and hashed password: ', [$username, $email, $hash]);
        if (empty($username) || empty($email) || empty($request->input('password'))) {
          return response([
            'success' => false,
            'message' => 'Some details have not bene provided'
          ]);
        }

        // Validate user details
        $User = new UserModel;
        $profilePictureName = $request->hasFile('profile-picture')
            ? strtolower($request->file('profile-picture')->getClientOriginalName())
            : 'sample.jpg';
        $passedValidation = $User->validate([
            'username' => $username,
            'email' => $email,
            'password' => $request->input('password'),
            'profile_picture' => $profilePictureName
        ]);
        if ($passedValidation === false) {
            Log::info('Couldnt validate input');
            return response([
              'success' => false,
              'message' => 'couldnt validate the input'
            ], 401);
        }

        // remove the raw password
        $_POST[ 'password' ] = null;
        $request->merge(['password' => null]);
        Log::info('Removed references to the raw password');

        // Check if user already exists
        $userExists = UserModel::exists($email);
        if ($userExists === true) {
            Log::debug('User already exists');
            return response([
              'success' => false,
              'message' => 'user already exists',
            ], 403);
        }
        Log::info('User doesnt exists');

        // Save the user
        $updated = $User->CreateQuery(['username' => $username, 'email_address' => $email, 'password' => $hash, 'logged_in' => 1, 'login_attempts' => 3]);
        if (empty($updated)) {
            Log::debug('Didnt save a user');
            return response([
              'success' => false,
              'message' => 'user didnt save into the database',
            ], 500);
        }
        // Get user id, save profile picture if required and update the profile_picture field
        $user = $User->SelectQuery(['where' => "email_address = '$email'", 'limit' => 1]);
        $profilePicturePath = $request->hasFile('profile-picture')
            ? $request->file('profile-picture')->store($user->id)
            : 'sample.jpg';
        $profilePicturePath = 'img/'.$profilePicturePath;
        $User->UpdateQuery(['email_address' => $email], ['profile_picture' => $profilePicturePath]);
        Log::info('Saved a user');

        return response([
          'success' => true,
        ], 200);
    }

    /**
     * Display the register view
     *
     * @return {object} View    View to display
     */
    public function index()
    {
        $loggingPrefix = "[RegisterController - ".__FUNCTION__.'] ';
      return View::make('register')->with('title', 'Register');
    }
}
