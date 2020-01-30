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
Route::get('/', 'HomeController@index');

Route::get('/register', 'RegisterController@index');
Route::post('/register', 'RegisterController@submit');

Route::get('/login', 'LoginController@get');
Route::post('/login', 'LoginController@post');

Route::get('/home', 'HomeController@index');

Route::post('/video/comment', 'VideoController@postComment');
