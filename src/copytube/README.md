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

# Help

## Cheatsheet

* Auth

Uses the users table, and will check if the user exists and creds match. But
wont check login attempts or lock an account - this needs to be done in the login controller
```php
$credentials = [
  'email_address' => 'Edward.idontexist@hotmail.com',
  'password' => 'Some password'
];
// Check if user is logged in
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
