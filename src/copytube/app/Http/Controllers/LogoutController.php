<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        //$loggingPrefix = "[LogoutController - " . __FUNCTION__ . "] ";
        // update db
        $user = Auth::user();
        // Log::debug(print_r($user, true));
        $user = User::where("id", $user->id)->first();
        $user->logged_in = 1;
        $user->save();

        // TODO :: This logic should be moved into a util class or something so delete method inuser controller can use it too
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/login");
    }
}
