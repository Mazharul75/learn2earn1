<?php
class Controller {
    public function model($model) {
        require_once "../app/models/" . $model . ".php";
        return new $model();
    }

    public function view($view, $data = []) {
        if (file_exists("../app/views/" . $view . ".php")) {
            // This line is the fix: it turns ['courses' => $courses] into $courses
            extract($data); 
            require_once "../app/views/" . $view . ".php";
        } else {
            die("View does not exist.");
        }
    }
}