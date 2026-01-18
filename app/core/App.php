<?php
class App {
    // Default controller and method
    protected $controller = 'AuthController';
    protected $method = 'login';
    protected $params = [];

    public function __construct() {
        try {
            $url = $this->parseUrl();
            if (isset($url[0])) {
                $name = ucfirst($url[0]) . 'Controller';
                if (file_exists("../app/controllers/" . $name . ".php")) {
                    $this->controller = $name;
                    unset($url[0]);
                } else {
                    throw new Exception("Controller '$name' not found.");
                }
            }
            require_once "../app/controllers/" . $this->controller . ".php";
            $this->controller = new $this->controller;
            if (isset($url[1])) {
                if (method_exists($this->controller, $url[1])) {
                    $this->method = $url[1];
                    unset($url[1]);
                } else {
                    throw new Exception("Method '{$url[1]}' not found in {$this->controller}.");
                }
            }
            $this->params = $url ? array_values($url) : [];
            call_user_func_array([$this->controller, $this->method], $this->params);

        } catch (Exception $e) {
            http_response_code(404);
            error_log("MVC Error: " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "<div style='font-family:sans-serif; padding:20px; text-align:center;'>";
                echo "<h1 style='color:#e74c3c;'>404 - Not Found</h1>";
                echo "<p>The requested page could not be found.</p>";
                echo "<small style='color:gray;'>Debug: " . htmlspecialchars($e->getMessage()) . "</small>";
                echo "<br><br><a href='" . BASE_URL . "dashboard/index' style='text-decoration:none; background:#3498db; color:white; padding:10px 20px; border-radius:5px;'>Go Home</a>";
                echo "</div>";
            } else {
                echo "<h1>404 - Page Not Found</h1>";
            }
        }
    }

    private function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}