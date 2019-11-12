<?php
 
namespace App\Http\Controllers;

use App\UserModel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

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
        // validate
        $validatedData = $request->validate([
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required|regex:/[0-9a-zA-Z]{8,}/'
        ]);

        // hash password
        $hash = Hash::make('password', [
            'rounds' => 12
        ]);
        $_POST['password'] = $hash;
        
        // save user (also checks if they exist)
        $User = new UserModel();
        $result = $User->checkAndSave($_POST, 1, 3);
        
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