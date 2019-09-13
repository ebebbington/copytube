<?php
/**
 * Creation
 * User: Edward
 * Date: 12/09/2019
 * Time: 18:38
 * 
 * This script is the routing file for CopyTube
*/

/**
 * Initilise the session straight away
 */
session_start();

/**
 * Set up server variables for myself to remember
 */
$uri = $_SERVER['REQUEST_URI']; // eg /home/classes
$root = $_SERVER['DOCUMENT_ROOT']; // /var/www/copytube
$url = $root . $uri;
$method = $_SERVER['REQUEST_METHOD']; // eg GET
$params = $_GET;
//echo print_r($GLOBALS);

/**
 * Display developer info on the page
 */
function displayInfo ($info) {
    echo '<pre>';
    echo print_r($info);
    echo '</pre>';
}

/**
 * Autoload Classes
 * 
 * This allows us to create instances of classes
 * anywhere, using: $myClass = new ClassName();
 */
function autoLoadClasses () {
    spl_autoload_register(function ($className) {
        // Check against if its a model or controller
        if (strpos($className, 'Controller')) {
            // strip the model string to be used in the include statement
            $className = str_replace('Controller', '', $className);
            include_once '/var/www/copytube/controllers/' . $className . '.php';
        }
        if (strpos($className, 'Model')) {
            // strip the model string to be used in the include statement
            $className = str_replace('Model', '', $className);
            include_once '/var/www/copytube/models/' . $className . '.php';
        }
    });
}(autoLoadClasses());

/**
 * Set Our Custom Error Handler
 */
function setCustomErrorHandler () {
    set_error_handler(function ($errCode, $text, $file, $line, $content) {
        $configPath = $_SERVER['DOCUMENT_ROOT']. '/copytube.ini';
        $config = parse_ini_file($configPath, true);
        $errorLogPath = $config['Logging']['error_log_file'];
        $fileSize = (filesize($errorLogPath) / 1000) / 1000; // in megabytes
        $fileSize > 10 ? $writeType = 'w' : $writeType = 'a';
        $errorArray   = ["\n\nError Code: $errCode", "\nDescription: $text", "\nFile: $file", "\nLine: $line"];
        $errorLogFile = fopen($errorLogPath, $writeType);
        for ($i = 0; $i < sizeof($errorArray); $i++) {
        fwrite($errorLogFile, $errorArray[ $i ]);
        }
        fclose($errorLogFile);
        return TRUE;
    }, E_ALL | E_STRICT);
}(setCustomErrorHandler());

/**
 * Routing logic
 */
function createRoutes () {
    require 'Route.php';
    // Generate the troute
    $Route = new Route($_SERVER); // used to pass in the uri
    // Create all routes
    $Route->add('/', function () { // add a route uri and callback
        $IndexController = new IndexController();
        
        //require __DIR__ . '/views/index.html';
    });
    $Route->add('/login', function () { // add a route uri and callback
        require __DIR__ . '/views/login.html';
    });
    $Route->add('/recover', function () { // add a route uri and callback
        require __DIR__ . '/views/recover.html';
    });
    $Route->add('/register', function () { // add a route uri and callback
        require __DIR__ . '/views/register.html';
    });
    // Submit the route after initiating all routes
    $Route->submit(); // calls the callback if one
}(createRoutes());
// switch ($request) {
//     case '/':
//         /**
//         * Check if cookies and session is set for
//         * if user isnt logged in
//         */
//         $userSession = $_SESSION['user'];
//         $cookies = $_COOKIES;
//         $ValidateModel = new ValidateModel();
//         $result = $ValidateModel->isSet([$userSession, $cookies]);
//         if ($result['success'] === false) {
//             require __DIR__ . '/views/login.html';
//         }
//         /**
//          * Check the cookies are correct for if user isnt
//          * logged in
//          */
//         $UserModel = new User($_COOKIES['sessionId2']);
//         if (!$User->exists) {
//             // clear session and cookies
//             // redirect to login
//             require __DIR__ . '/views/login.html';
//         }
//         /**
//          * By now, the user has logged in. Assign user into
//          * the session object
//          */
//         break;
//     case '/login':
//         require __DIR__ . '/views/login.html';
//         break;
//     case '/register':
//         require __DIR__ . '/views/register.html';
//         break;
//     case strpos($uri, '/register?rid='):
//         var_dump(basename($_SERVER['REQUEST_URI']));
//         var_dump($_GET);
//         exit();
//         require __DIR__ . '/views/recover.html';
//         break;
//     default:
//         // todo :: log error, display error page
//         require __DIR__ . '/views/404.html';

// }
