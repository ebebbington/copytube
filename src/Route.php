<?php

class Route {

    public $routes = [];
    public $request = '';

    public function __construct (Array $request = null) {
        $this->request = $request ? $request['REQUEST_URI'] : '/';
    }

    public function add (String $route = '', Object $callback = null) {
        // foreach ($routes as $route) {
        //     $this->accessableRoutes[$route] = $callback;
        // }
        $this->routes[$route] = $callback;
    }

    private function hasRoute (String $uri = '') {
        return array_key_exists($uri, $this->routes);
    }

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
