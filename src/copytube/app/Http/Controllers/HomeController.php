<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Video;

class HomeController extends Controller
{
    public function index()
    {
        $loggingPrefix = "[HomeController - " . __FUNCTION__ . "] ";
        Log::info($loggingPrefix);
        $user = Auth::user();
        $videos = Video::find(3)->get();
        return View::make("home")
            ->with("title", "Home")
            ->with("username", $user->username)
            ->with("email", $user->email_address)
            ->with("profilePicture", $user->profile_picture)
            ->with("videos", $videos);
    }
}
