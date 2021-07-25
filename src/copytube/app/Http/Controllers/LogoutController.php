<?php

namespace App\Http\Controllers;

use App\UserModel;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        //$loggingPrefix = "[LogoutController - " . __FUNCTION__ . "] ";
        // update db
        $user = Auth::user();
        Log::debug(print_r($user, true));
        $User = new UserModel();
        $User->updateLoggedIn(1, $user->email_address);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/login");
    }
}
