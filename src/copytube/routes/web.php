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

Route::get("/", function () {
    return redirect("home");
});

Route::get("/register", [
    "as" => "register",
    "uses" => "RegisterController@index",
]);
Route::post("/register", "RegisterController@submit");

Route::get("/login", ["as" => "login", "uses" => "LoginController@get"]);
Route::post("/login", "LoginController@post");

Route::get("/home", [
    "as" => "home",
    "uses" => "HomeController@index",
])->middleware("auth");

Route::post("/video/comment", "VideoController@postComment")->middleware(
    "auth"
);
Route::get("/video/titles", "VideoController@autocomplete")->middleware("auth");
Route::get("/video", [
    "as" => "video",
    "uses" => "VideoController@index",
])->middleware("auth");
Route::get("/videos", "VideoController@getVideos")->middleware("auth");
Route::delete("/video/comment", "VideoController@deleteComment")->middleware(
    "auth"
);
Route::put("video/comment", "VideoController@updateComment")->middleware(
    "auth"
);

Route::get("/logout", "LogoutController@logout")->middleware("auth");

Route::get("/recover", "RecoverController@index");
Route::post("/recover", [
    "as" => "recover",
    "uses" => "RecoverController@post",
]);

Route::get("/chat", "ChatController@index");

Route::delete("/user", "UserController@Delete")->middleware("auth");

Route::get("/{any}", function () {
    abort(404);
})->where("any", ".*");
