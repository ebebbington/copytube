<?php

namespace App\Http\Controllers;

use App\CommentsModel;
use App\Jobs\ProcessUserDeleted;
use App\UserModel;
use Auth;
use Storage;

class UserController extends Controller
{
    public function Delete()
    {
        $user = Auth::user();

        // Remove file from fs
        Storage::disk("local_public")->deleteDirectory("img/" . $user->id); //Storage::disk('local_public')->delete('img/'.$user['id']);

        // Remove row from db
        $UserModel = new UserModel();
        $UserModel->DeleteQuery(["email_address" => $user->email_address]);

        // Remove all comments
        $CommentsModel = new CommentsModel();
        $CommentsModel->DeleteQuery(["user_id" => $user->id]);

        // Log user out from Auth
        Auth::logout();

        // Send event to remove all comments
        dispatch(new ProcessUserDeleted($user->id));
    }
}
