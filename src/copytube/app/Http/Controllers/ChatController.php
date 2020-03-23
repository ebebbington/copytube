<?php

namespace App\Http\Controllers;

use App\UserModel;
use App\SessionModel;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use View;
use Auth;
use Cookie;

class ChatController extends Controller
{

    public function index (Request $request)
    {
        $loggingPrefix = "[ChatController - ".__FUNCTION__.'] ';
        Log::info($loggingPrefix . 'Return view of `chat`');
        return View::make('chat')->with('title', 'Chat');
    }
}
