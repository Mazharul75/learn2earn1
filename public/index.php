<?php
// 1. GLOBAL SETTINGS
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. LOAD CONFIG & CORE
// Ensure these paths are correct relative to your public folder
require_once "../config/config.php";
require_once "../config/database.php";

try {
    // =============================================================
    // 3. PARSE URL (FIXED)
    // =============================================================
    
    // Check if .htaccess sent the URL parameter (Safest Method)
    if (isset($_GET['url'])) {
        $path = $_GET['url'];
    } else {
        // Fallback: Manual Parsing
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // FIX: Specifically handle your folder structure
        // We remove '/learn2earn/public' OR just '/learn2earn' to find the real route
        $path = str_replace('/learn2earn/public', '', $path);
        $path = str_replace('/learn2earn', '', $path);
    }

    $path = ltrim($path, '/'); // Remove leading slash
    $segments = explode('/', $path);

    // Defaults
    $controllerName = !empty($segments[0]) ? $segments[0] : 'dashboard';
    $action         = !empty($segments[1]) ? $segments[1] : 'index';
    $param          = !empty($segments[2]) ? $segments[2] : null;
    $param2         = !empty($segments[3]) ? $segments[3] : null;

    // =============================================================
    // 4. MANUAL ROUTER (The "Switchboard")
    // =============================================================

    // --- ROUTE: AUTH ---
    if ($controllerName === 'auth') {
        require_once '../app/controllers/AuthController.php';
        $controller = new AuthController();

        if ($action === 'login') $controller->login();
        elseif ($action === 'register') $controller->register();
        elseif ($action === 'profile') $controller->profile();
        elseif ($action === 'updateProfile') $controller->updateProfile();
        elseif ($action === 'logout') $controller->logout();
        elseif ($action === 'apiCheckEmail') $controller->apiCheckEmail();
        else $controller->login(); // Default
    }

    // --- ROUTE: LEARNER ---
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

    // --- ROUTE: INSTRUCTOR ---
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

    // --- ROUTE: CLIENT ---
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

    // --- ROUTE: ADMIN ---
    elseif ($controllerName === 'admin') {
        require_once '../app/controllers/AdminController.php';
        $controller = new AdminController();

        if ($action === 'dashboard') $controller->dashboard();
        elseif ($action === 'invite') $controller->invite();
        elseif ($action === 'deleteUser') $controller->deleteUser($param);
        else $controller->dashboard();
    }

    // --- ROUTE: DASHBOARD (Home) ---
    elseif ($controllerName === 'dashboard') {
        require_once '../app/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
    }

    // --- 404 NOT FOUND ---
    else {
        http_response_code(404);
        echo "<div style='text-align:center; margin-top:50px;'>";
        echo "<h1 style='color:red;'>404 - Not Found</h1>";
        echo "<p>The controller '<strong>" . htmlspecialchars($controllerName) . "</strong>' does not exist.</p>";
        echo "<p>Path detected: " . htmlspecialchars($path) . "</p>";
        echo "<a href='" . BASE_URL . "dashboard/index'>Go Home</a>";
        echo "</div>";
    }

} catch (Exception $e) {
    // 5. ERROR HANDLING
    http_response_code(500);
    error_log("MVC Error: " . $e->getMessage());

    if (ini_get('display_errors')) {
        echo "<h1>500 - Internal Server Error</h1>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        echo "<h1>Something went wrong!</h1>";
    }
}
?>