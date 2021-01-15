<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessUserDeleted;
use App\UserModel;
use App\SessionModel;
use http\Client\Curl\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use View;
use App\VideosModel;
use App\CommentsModel;
use Cookie;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $loggingPrefix = "[HomeController - " . __FUNCTION__ . "] ";
        Log::info($loggingPrefix);
        // TODO Get and display all videos
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
