<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Jobs\ProcessUserDeleted;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function Delete()
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

        // Send event to remove all comments
        dispatch(new ProcessUserDeleted($user->id));
    }
}
