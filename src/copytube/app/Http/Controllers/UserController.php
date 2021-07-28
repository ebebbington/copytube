<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Jobs\ProcessUserDeleted;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function Delete(Request $request)
    {
        $user = Auth::user();

        // Remove file from fs
        Storage::disk("local_public")->deleteDirectory("img/" . $user->id); //Storage::disk('local_public')->delete('img/'.$user['id']);

        // Remove row from db
        User::where("email_address", $user->email_address)->delete();

        // Remove all comments
        Comment::where("user_id", $user->id)->delete();

        // Log user out from Auth
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Send event to remove all comments
        dispatch(new ProcessUserDeleted($user->id));
    }
}
