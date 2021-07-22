<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use View;

class ChatController extends Controller
{
    public function index()
    {
        $loggingPrefix = "[ChatController - " . __FUNCTION__ . "] ";
        Log::info($loggingPrefix . "Return view of `chat`");
        return View::make("chat")->with("title", "Chat");
    }
}
