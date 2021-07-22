<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use View;
use App\VideosModel;

class HomeController extends Controller
{
    public function index()
    {
        $loggingPrefix = "[HomeController - " . __FUNCTION__ . "] ";
        Log::info($loggingPrefix);
        $VideosModel = new VideosModel();
        $user = Auth::user();
        $videos = $VideosModel->getVideosForHomePage();
        return View::make("home")
            ->with("title", "Home")
            ->with("username", $user->username)
            ->with("email", $user->email_address)
            ->with("profilePicture", $user->profile_picture)
            ->with("videos", $videos);
    }
}
