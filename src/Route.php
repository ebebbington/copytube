<?php

/**
 *  The Route Model
 * 
 * Holds the routes the app can make, and he callback they execute.
 * On submit of a route, it will check the request and handle
 * the callbacks accordingly
 * 
 * @author Edward Bebbington
 * @copyright
 * @license
 * @method __construct()
 * @method add()
 * @method hasRoute()
 * @method submit()
 */
class Route {

    /** @var Array $routes List of allowed routes (path and callback) */
    public $routes = [];
    /** @var String $request The URI of the request */
    public $request = '';

    /**
     * Constructor
     * 
     * Sets the URI of the request
     * 
     * @param Array $request The URI of the request
     */
    public function __construct (Array $request = null) {
        $this->request = $request ? $request['REQUEST_URI'] : '/';
    }

    /**
     * Add a Route
     * 
     * Adds the route URI and callback to this class
     * 
     * @param String $route The URI of the route to add
     * @param Object $callback The function to call when the URI is met
     */
    public function add (String $route = '', Object $callback = null) {
        // foreach ($routes as $route) {
        //     $this->accessableRoutes[$route] = $callback;
        // }
        $this->routes[$route] = $callback;
    }

    /**
     * Does Class Have Route
     * 
     * Checks if the class has the accessable route
     * 
     * @param String $uri The URI to check against the current routes property
     * 
     * @return Bool If the route exists
     */
    private function hasRoute (String $uri = '') {
        return array_key_exists($uri, $this->routes);
    }

    /**
     * Submit a Route
     * 
     * Once all routes have been added, submit the route
     * which will check he route the user is accessing
     * to see if its allwoed, and call the callback if the
     * requested route exists, else display the 404 page
     */
    public function submit () {
        if ($this->hasRoute($this->request)) {
            $this->routes[$this->request]->call($this);
        } else {
            require __DIR__ . '/views/404.html';
        }
        // $uriGetParam = isset($_GET) ? array_shift(array_values($_GET)) : '/';
        // foreach ($this->accessableRoutes as $key => $value) {
        //     if (preg_match("#^$value$#", $uriGetParam)) {
        //         echo 'match';
        //         echo $uriGetParam;
        //         echo $value;
        //     }
        // } 
    }

}
