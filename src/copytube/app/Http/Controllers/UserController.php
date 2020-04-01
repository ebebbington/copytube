<?php

namespace App\Http\Controllers;

use App\CommentsModel;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class UserController extends Controller
{
    public function Delete (Request $request)
    {
        $user = Auth::user();

        // Remove file from fs
        if ($user['profile_picture'] !== 'sample.jpg') {
            try {
                Storage::delete($user['profile_picture']);
            } catch (FileException $err) {

            }
        }

        // Remove row from db
        $UserModel = new UserModel();
        $UserModel->DeleteQuery(['email_address' => $user['email_address']]);

        // Remove all comments
        $CommentsModel = new CommentsModel();
        $CommentsModel->DeleteQuery(['user_id' => $user->id]);

        // Log user out from Auth
        Auth::logout();

        return redirect()->route('register');
    }
}
