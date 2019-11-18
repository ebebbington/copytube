<?php
 
namespace App\Http\Controllers;

use App\UserModel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller {

    public function __construct()
    {
        Log::debug('REGISTER CONTROLLER');
    }

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
            Log::debug('Request is not an ajax call');
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
        Log::debug('Passed server validation');

        // get data
        $username = $request->input('username');
        $email = $request->input('email');
        // hash password
        $hash = Hash::make($request->input('password'), [
            'rounds' => 12
        ]);
        Log::debug(['message' => 'Retrieved input and hashed password', 'data' => array($username, $email, $hash)]);
        
        // remove the raw password
        $_POST['password'] = null;
        $request->merge(['password' => null]);
        Log::debug('Removed references to the raw password');
        
        // save user (also checks if they exist)
        $User = new UserModel();
        $result = $User->checkAndSave($username, $email, $hash);
        if ($result['success'] === true) {
            Log::debug([
                'message' => 'Saved a new user account',
                'data' => array($username, $email, $hash)
            ]);
            return response([
                'success' => $result['success']
            ], 200);
        } else {
            Log::debug('Couldnt save a new user account');
            return response([
                'success' => false
            ], 500);
        }
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