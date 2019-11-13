<?php
 
namespace App\Http\Controllers;

use App\UserModel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller {

    /**
     * Submit the register form
     * 
     * @param {object} $request
     * 
     * @return {object} response    Data and status
     */
    public function submit (Request $request)
    {
        // check its an ajax call
        if ($request->ajax() === false)
        {
            Log::debug('Not an ajax call to registering a user');
            return response([
                'success' => false
            ], 403);
        }

        // validate
        $validatedData = $request->validate([
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required|regex:/[0-9a-zA-Z]{8,}/'
        ]);
        Log::debug('registering an account passed server validation');

        // get data
        $username = $request->input('username');
        $email = $request->input('email');
        // hash password
        $hash = Hash::make($request->input('password'), [
            'rounds' => 12
        ]);

        // remove the raw password
        $_POST['password'] = null;
        $request->merge(['password' => null]);
        Log::debug('removed refs to raw password on register account');
        
        // save user (also checks if they exist)
        $User = new UserModel();
        $result = $User->checkAndSave($username, $email, $hash);
        if ($result === true) {
            Log::debug('Saved a new user account');
        }
        if ($result === false) {
            Log::debug('Couldnt save a new user account');
        }

        return response([
            'success' => $result
        ], 200);
    }

    /**
     * Display the register view
     * 
     * @return {object} View    View to display
     */
    public function index ()
    {
        return view('register');
    }
}