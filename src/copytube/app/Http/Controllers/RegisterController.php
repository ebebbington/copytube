<?php

namespace App\Http\Controllers;

use App\UserModel;

use http\Client\Curl\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function submit(Request $request)
    {
        // check its an ajax call
        if ($request->ajax() === false) {
            Log::debug('Request is not an ajax call');
            return response([
              'success' => false,
            ], 403);
        }
        Log::debug('Request to POST register is an ajax');

        // get data
        $username = $request->input('username');
        $email    = $request->input('email');
        $hash = Hash::make($request->input('password'), [
          'rounds' => 12,
        ]);
        Log::debug('Retrieved input and hashed password: ', [$username, $email, $hash]);

        // check data was passed in
        if (empty($username) || empty($email) || empty($request->input('password'))) {
          return response([
            'success' => false,
            'message' => 'Some details have not bene provided'
          ]);
        }

        // Validate user details
        $UserModel = new UserModel;
        $passedValidation = $UserModel->validate($request->all());
        if ($passedValidation === false) {
            Log::debug('Couldnt validate input');
            return response([
              'success' => false,
              'message' => 'couldnt validate the input'
            ], 401);
        }

        // remove the raw password
        $_POST[ 'password' ] = null;
        $request->merge(['password' => null]);
        Log::debug('Removed references to the raw password');

        // Check if user already exists
        $userExists = UserModel::exists($email);
        if ($userExists === true) {
            Log::debug('User already exists');
            return response([
              'success' => false,
              'message' => 'user already exists',
            ], 200);
        }
        Log::debug('User doesnt exists');

        // Save the user
        $User = $UserModel->CreateQuery(['username' => $username, 'email_address' => $email, 'password' => $hash, 'logged_in' => 1, 'login_attempts' => 3]);
        if (isset($User->exists) && $User->exists === false) {
            Log::debug('Didnt save a user');
            return response([
              'success' => false,
              'message' => 'user didnt save into the database',
            ], 500);
        }
        if (isset($User->exists) && $User->exists === true) {
            Log::debug('Saved a user');
            return response([
              'success' => true,
            ], 200);
        }

        // For precaution
        Log::debug("I shouldnt be here");
        return response([
          'success' => false,
            'message' => 'Something went wrong'
        ], 500);
    }

    /**
     * Display the register view
     *
     * @return {object} View    View to display
     */
    public function index()
    {
      return View::make('register')->with('title', 'Register');
    }
}
