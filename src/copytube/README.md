# CopyTube (Main App)

This section provides the main application for this project, such as:

* PHP-FPM handling

* Views

* All client side functionality

* Routing

* Video chat

# Directory Structure / Description

* `app/`

    * Holds the base code for the application

* `bootstrap/`

    * Holds bootstrapping scripts for the application

* `config/`

    * Holds all project configuration

* `data/`

    * Holds files that arent used but provide some use (like documentation)

* `database/`

    * Holds the database files

* `node_modules/`

    * Holds NPM dependencies

* `playground/`

    * A plyground directory to test PHP-related features

* `public/`

    * Helps in starting the project and holds other scripts (JS & CSS), along with any images

* `resources/`

    * Holds all client side CSS/SASS, language and JS files

* `routes/`

    * Holds all definitions files for routes (web, console etc.)

* `storage/`

    * Holds session files, logs and other miscellaneous files

* `tests/`

    * Holds all tests for the project

* `vendor/`

    * Holds all composer dependencies

* `.editorconfig`

    * Holds configurations for coding styles to suit different IDE's and environments

* `.env`

    * Holds all environmental data

* `.env.example`

    * Example environment file

* `.gitattributes`

    * ?

* `phpunit.result.cache`

    * ?

* `.styleci.yml`

    * ?

* `artisan`

    * ?

* `composer.json`

    * Defines dependencies for composer

* `composer.lock`

    * Defined dependencies for the composer dependencies

* `package-lock.json`

    * Defines dependencies for package.json dependencies

* `package.json`

    * Defines dependencies for NPM

* `phpunit.xml`

    * Configuration file for PHPUnit

* `server.php`

    * ?

* `tsconfig.json`

    * Configuration file for TypeScript

* `webpack.mix.js`

    * ?

* `yarn.lock`

    * ?

* `.gitignore`

    * List of files and directories for Git to ignore

# Tools Used

This is the list of all tools used here, which also act as the tools learnt, or tools implemented to learn:

* HTML

    * General mark-up
    * Blade

* CSS

    * General CSS

* JS

    * jQuery
    * General JS

* PHP

    * OO
    * MVC
    * PHP-FPM
    * Data Modelling
    * Laravel
        * Components
    * Routing
    * PHPUnit
    * SQL Queries

* Other

    * Sessions
    * Handling auth (User logging in)
    
* SocketIO Client

# Building

We use the `package.json` file to build the JS and CSS.

It boils down to using webpack to compile and minify the `app.scss|js` files, where each file imports all the other stylesheets or javascripts. The process is:

* By current architecture, we import all our javascripts and stylesheets into the `app.js` and `app.scss` files, respectively.
* Then our run command will compile and those files

To do so, run: `npm run dev`. To understand a bit more, see the `webpack.mix.js` file. This also uses the `tsconfig.json` for when we write TS javascript files

# PHPUnit Tests

## Writing the Tests

## Running the Tests

# XDebug

Xdebug is setup and configured for CopyTube, with the use a Chrome extension and VSCode. It has only been done so by configuring the Xdebug configuration file from a remote-host IP to `host.docker.internal`, as well as setting up VSCode.

## Setup

* 1. Install the chrome extension [here](https://chrome.google.com/webstore/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc)
* 2. The configuration file for VSCode has already been setup (`./.vscode/*`)

# Information

## 4xx|5xx Status

Simply use the following code to do so: `abort($statusCode)`. I have set up a 404 page that laravel will
automatically detect so when doing `abort(404);` this is all handled. The 404 page has a hard coded title of 404,
and the page uses the default message of `Page Not Found`. If you wish to
specify a custom message, just use `abort(404, 'Woah cant find it mate!');`.

## Auth / Users

Authentication using Laravel is implemented. How it works is, certain routes are using `->middleware('auth')`, this checks if `Auth::check()` is true. If it
isn't then it will redirect to the Login route - to achieve this I had to add `['as' => 'login']` to the route call, which gives it's
the name e.g. redirecting to route Login looks for route named login.

How we auth users is simple. The User.php file (with the help of me using a users db table) is automatically setup to check this
table, so when we call `Auth::attempt(['email_address' => ..., 'password' => ...])`, it will do the validation using a related user
in the users table. This call will also log the user in

## Events / Listeners / Realtime Update Using Redis and Realtime socket

This is implemented by simply (and this applies to anything else regarding scalability) updating `EventServiceProvider`.
I added the event class and the listener class to the `$listen` property, then using `php artisan event:make` created those classes
for me. In the event class you then assign a parameter to a property, and once done will be sent off to
the listener. Then the listener will use the `handle` method with the data assigned above inside of the parameters - here you can write whatever you want,
but for me I wanted to push it to Redis, for example:

```php
public function handle(CommentAdded $event)
{
    // In the event, we assigned $this->comment, so that is accessible in the event param
    // You could do $this->name = 'ed', and still do $event->name
    Redis::publish('realtime.comments.new', json_encode($event->comment));
}
```

This is all called by simply doing:
```php
event(new \App\Events\CommentAdded($comment));
```

See Laravels Events for more information

## Cheatsheet

* Auth

Uses the users table, and will check if the user exists and creds match. But
wont check login attempts or lock an account - this needs to be done in the login controller
```php
$credentials = [
  'email_address' => 'Edward.idontexist@hotmail.com',
  'password' => 'Some password'
];
// Check if user is logged in - manual way, the automatic way is using ->middleware('auth')
Log::debug('Is user logged in: ' . Auth::check());
// Log user in
Log::debug('Logging user in:');
Log::debug(Auth::attempt($credentials));
// Get authed/logged in user
Log::debug('The authed user');
Log::debug(Auth::user());
// Log user out
Log::debug('Gonna log user out: ' . Auth::logout());

// Then in the routes, only allow access if they are authed:
Route::get('/home', ['middleware' => 'auth', 'as' => 'home', 'uses' => 'HomeController@index']);
// or
Route::get('...')->middleware('auth');

// Get the user (if authed)
$user = Auth::user();
```

* Cache

```php
// TEST cache using redis container. Works but how?
if ($value = Redis::get('rediscontainerkey')) {
    Log::debug('Redis key already exists: ' . $value);
} else {
    Log::debug('Going to create redis container key as it doesnt exist');
    Redis::set('rediscontainerkey', 'rediscontainervalue');
}
// TEST cache using default file driver. If driver is set to file, the data is stored in /storage/framework/cache/data/...
//      This is the default cache implementation for laravel, using either file or redis as the driver
Cache::put('thekey', 'the redis cache value', 5000);
Log::debug('Cache value for thekey: ' . Cache::get('thekey'));
```

To convert this into pointing to Redis i just needed to define the DRIVER as redis as CACHE_PREFIX as "".
Cache is preferred over Redis

* Redis

To get Redis working, you obviously need to define the environmental variables required for `config/database.php`.

Once this is done, you need to change:

```php
'client' => env('REDIS_CLIENT', 'phpredis'),

'redis' => [
    'driver' => 'redis',
    'connection' => 'cache',
],
```

to:

```php
'client' => env('REDIS_CLIENT', 'predis'),

'redis' => [
    'driver' => 'redis',
    'connection' => 'default',
],
```

Then remove the following block from `config/database.php` for the redis context:

```php
'options' => [
    'cluster' => env('REDIS_CLUSTER', 'redis'),
    'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
],
```

Boom all done, now we can write the following and query the redis container:

```php
Redis::set('thekey', 'thevalue');
// or
$redis = Redis::connection();
$redis->set('thekey', 'thevalue');
if ($value = $redis->get('thekey')) { // also same to `Redis::has('thekey')`
    // is set
} else {
    // not set
}
```

```shell script
$ docker exec -it copytube_redis bash

# redis-cli

> get thekey # thevalue
```

# Help
