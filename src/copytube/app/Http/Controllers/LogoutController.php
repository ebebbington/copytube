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
        $user = User::where("id", $user->id)->first();
        $user->logged_in = 1;
        $user->save();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect("/login");
    }
}
