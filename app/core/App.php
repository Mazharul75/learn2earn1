<?php

class App {
<<<<<<< HEAD

=======
>>>>>>> ae2b77bca800df0f5d44383f0593aab75c048997
    protected $controller = 'AuthController';
    protected $method = 'login';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

<<<<<<< HEAD
        // Controller
        if (!empty($url[0])) {
=======
        // controller
        if (isset($url[0])) {
>>>>>>> ae2b77bca800df0f5d44383f0593aab75c048997
            $controllerName = ucfirst($url[0]) . 'Controller';
            if (file_exists("../app/controllers/" . $controllerName . ".php")) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        require_once "../app/controllers/" . $this->controller . ".php";
        $this->controller = new $this->controller;

<<<<<<< HEAD
        // Method
        if (!empty($url[1]) && method_exists($this->controller, $url[1])) {
=======
        // method
        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
>>>>>>> ae2b77bca800df0f5d44383f0593aab75c048997
            $this->method = $url[1];
            unset($url[1]);
        }

<<<<<<< HEAD
        // Params
=======
        // params
>>>>>>> ae2b77bca800df0f5d44383f0593aab75c048997
        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

<<<<<<< HEAD
    private function parseUrl() {
=======
    public function parseUrl() {
>>>>>>> ae2b77bca800df0f5d44383f0593aab75c048997
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
