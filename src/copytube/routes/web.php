<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
// Route::get('/', function () {
//     return view('home')->with('title', 'Home');
// });
//return Cache::remember('home.index', 60 * 60 * 24, fn() => (new App\Http\Controllers\HomeController)->index($request));
*/

Route::redirect("/", "/home");

Route::prefix("register")->group(function () {
    Route::get("/", "RegisterController@index")->name("register");
    Route::post("/", "RegisterController@submit");
});

Route::prefix("login")->group(function () {
    Route::get("/", "LoginController@get")
        ->name("login")
        ->middleware("redirect.if.authed");
    Route::post("/", "LoginController@post");
});

Route::get("/home", "HomeController@index")
    ->middleware("auth")
    ->name("home");

Route::post("/video/comment", "VideoController@postComment")->middleware(
    "auth"
);
Route::get("/video/titles", "VideoController@autocomplete")->middleware("auth");
Route::get("/video", "VideoController@index")
    ->middleware("auth")
    ->name("video");
Route::get("/videos", "VideoController@getVideos")->middleware("auth");
Route::delete("/video/comment", "VideoController@deleteComment")->middleware(
    "auth"
);
Route::put("video/comment", "VideoController@updateComment")->middleware(
    "auth"
);

Route::get("/logout", "LogoutController@logout")->middleware("auth");

Route::get("/recover", "RecoverController@index");
Route::post("/recover", "RecoverController@post")->name("recover");

Route::delete("/user", "UserController@Delete")->middleware("auth");

Route::get("/{any}", function () {
    abort(404);
})->where("any", ".*");
