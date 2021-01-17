<?php

namespace App\Http\Controllers;

use App\UserModel;
use App\SessionModel;

use http\Client\Curl\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use View;
use Cookie;

class LogoutController extends Controller
{
    public function logout()
    {
        //$loggingPrefix = "[LogoutController - " . __FUNCTION__ . "] ";
        // update db
        $user = Auth::user();
        Log::debug($user);
        $User = new UserModel();
        $User->updateLoggedIn(1, $user->email_address);

        Auth::logout();
        return redirect("/login");
    }
}
