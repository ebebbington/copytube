<?php
 
namespace App\Http\Controllers;

use App\UserModel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller {
    public function create (Request $request)
    {
        // validate
        $validatedData = $request->validate([
            'username' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        // hash password
        $hash = Hash::make('password', [
            'rounds' => 12
        ]);
        $_POST['password'] = $hash;

        // check if user exists
        $userExists = DB::table('users')->where('password', $_POST['password'])->first();
        if ($userExists) {
            return 'exists';
        }
        if (!$userExists) {
            // save the user
            $User = new UserModel();
            $User->create(
                [
                'username' => $_POST['username'],
                'email_address' => $_POST['email'],
                'password' => $_POST['password'],
                'logged_in' => 1,
                'login_attempts' => 3
                ]
            );
            var_dump($User);
            return 'saved';
        }
    }

    public function index ()
    {
        return view('register');
    }
}