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

// Route::get('/', function () {
//     return view('home')->with('title', 'Home');
// });
//return Cache::remember('home.index', 60 * 60 * 24, fn() => (new App\Http\Controllers\HomeController)->index($request));

// TODO :: Adde middleware auth to required routes

Route::get('/', 'HomeController@index');

Route::get('/register', 'RegisterController@index');
Route::post('/register', 'RegisterController@submit');

Route::get('/login', ['as' => 'login', 'uses' => 'LoginController@get']);
Route::post('/login', 'LoginController@post');

Route::get('/home', 'HomeController@index');

Route::post('/video/comment', 'VideoController@postComment');
Route::get('/video', 'VideoController@getAllVideoTitles');

Route::get('/logout', 'LogoutController@logout');

Route::get('/recover', 'RecoverController@index');
Route::post('/recover', 'RecoverController@post');

Route::get('/chat', 'ChatController@index');
