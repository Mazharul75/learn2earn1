<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
define('BASE_URL', 'http://localhost/learn2earn/'); 
require_once "../config/database.php"; 

try {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $segments = explode('/', trim($path, '/'));
    if (isset($segments[0]) && strtolower($segments[0]) === 'learn2earn') {
        array_shift($segments); 
    }
    if (isset($segments[0]) && strtolower($segments[0]) === 'public') {
        array_shift($segments); 
    }
    $controllerName = !empty($segments[0]) ? $segments[0] : 'dashboard';
    $action         = !empty($segments[1]) ? $segments[1] : 'index';
    $param          = !empty($segments[2]) ? $segments[2] : null;
    $param2         = !empty($segments[3]) ? $segments[3] : null;

    if ($controllerName === 'auth') {
        require_once '../app/controllers/AuthController.php';
        $controller = new AuthController();

        if ($action === 'login') $controller->login();
        elseif ($action === 'register') $controller->register();
        elseif ($action === 'profile') $controller->profile();
        elseif ($action === 'updateProfile') $controller->updateProfile();
        elseif ($action === 'deleteAccount') $controller->deleteAccount();
        elseif ($action === 'logout') $controller->logout();
        elseif ($action === 'apiCheckEmail') $controller->apiCheckEmail();
        else $controller->login();
    }

    elseif ($controllerName === 'learner') {
        require_once '../app/controllers/LearnerController.php';
        $controller = new LearnerController();

        if ($action === 'courses') $controller->courses();
        elseif ($action === 'enroll') $controller->enroll($param);
        elseif ($action === 'progress') $controller->progress($param);
        elseif ($action === 'jobs') $controller->jobs();
        elseif ($action === 'apply') $controller->apply($param);
        elseif ($action === 'applyForm') $controller->applyForm($param);
        elseif ($action === 'submitApplication') $controller->submitApplication();
        elseif ($action === 'submitTask') $controller->submitTask();
        elseif ($action === 'takeQuiz') $controller->takeQuiz($param);
        elseif ($action === 'submitQuiz') $controller->submitQuiz($param);
        elseif ($action === 'checkout') $controller->checkout($param, $param2);
        elseif ($action === 'search') $controller->search();
        else $controller->courses();
    }

    elseif ($controllerName === 'instructor') {
        require_once '../app/controllers/InstructorController.php';
        $controller = new InstructorController();

        if ($action === 'index') $controller->index();
        elseif ($action === 'create') $controller->create();
        elseif ($action === 'manage') $controller->manage($param);
        elseif ($action === 'students') $controller->students($param);
        elseif ($action === 'uploadMaterial') $controller->uploadMaterial();
        elseif ($action === 'createTask') $controller->createTask();
        elseif ($action === 'createQuiz') $controller->createQuiz();
        elseif ($action === 'reviewTask') $controller->reviewTask();
        elseif ($action === 'viewJobs') $controller->viewJobs();
        elseif ($action === 'recommend') $controller->recommend($param);
        elseif ($action === 'submitRecommendation') $controller->submitRecommendation();
        elseif ($action === 'requests') $controller->requests();
        elseif ($action === 'handleRequest') $controller->handleRequest($param, $param2);
        elseif ($action === 'searchStudentsApi') $controller->searchStudentsApi();
        else $controller->index();
    }

    elseif ($controllerName === 'client') {
        require_once '../app/controllers/ClientController.php';
        $controller = new ClientController();

        if ($action === 'index') $controller->index();
        elseif ($action === 'post') $controller->post();
        elseif ($action === 'applicants') $controller->applicants($param);
        elseif ($action === 'updateApplication') $controller->updateApplication($param, $param2);
        elseif ($action === 'inviteLearner') $controller->inviteLearner($param, $param2);
        elseif ($action === 'searchApplicantsApi') $controller->searchApplicantsApi();
        elseif ($action === 'select') $controller->select($param);
        else $controller->index();
    }

    elseif ($controllerName === 'admin') {
        require_once '../app/controllers/AdminController.php';
        $controller = new AdminController();

        if ($action === 'dashboard') $controller->dashboard();
        elseif ($action === 'invite') $controller->invite();
        elseif ($action === 'deleteUser') $controller->deleteUser($param);
        else $controller->dashboard();
    }

    elseif ($controllerName === 'dashboard') {
        require_once '../app/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
    }

    else {
        http_response_code(404);
        $view404 = '../app/views/errors/404.php';
        if (file_exists($view404)) {
            include $view404;
        } else {
            echo "<div style='text-align:center; margin-top:50px;'>";
            echo "<h1 style='color:red;'>404 - Not Found</h1>";
            echo "<p>The controller '<strong>" . htmlspecialchars($controllerName) . "</strong>' does not exist.</p>";
            echo "<a href='" . BASE_URL . "dashboard/index'>Go Home</a>";
            echo "</div>";
        }
    }

} catch (Exception $e) {
    http_response_code(500);
    error_log("MVC Error: " . $e->getMessage());

    $view500 = '../app/views/errors/500.php';
    if (file_exists($view500)) {
        $error_message = $e->getMessage();
        include $view500;
    } else {
        echo "<h1>500 - Internal Server Error</h1>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>