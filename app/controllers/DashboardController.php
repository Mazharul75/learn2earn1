<?php
require_once '../app/models/Enrollment.php';
require_once '../app/models/JobApplication.php';
require_once '../app/models/Notification.php';
require_once '../app/models/Course.php';
require_once '../app/models/Job.php';

class DashboardController {
    private $enrollModel;
    private $jobAppModel;
    private $notifyModel;
    private $courseModel;
    private $jobModel;

    public function __construct() {
        $this->enrollModel = new Enrollment();
        $this->jobAppModel = new JobApplication();
        $this->notifyModel = new Notification();
        $this->courseModel = new Course();
        $this->jobModel = new Job();
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        
        $role = $_SESSION['user_role'];

        if ($role == 'learner') {
            $this->loadView('learner/dashboard', [
                'myCourses' => $this->enrollModel->getLearnerCourses($_SESSION['user_id']),
                'myJobs' => $this->jobAppModel->getLearnerApplications($_SESSION['user_id']),
                'notifications' => $this->notifyModel->getUnread($_SESSION['user_id'])
            ]);
        } elseif ($role == 'instructor') {
            $this->loadView('instructor/dashboard', [
                'courses' => $this->courseModel->getCoursesByInstructor($_SESSION['user_id'])
            ]);
        } elseif ($role == 'client') {
            $this->loadView('client/dashboard', [
                'jobs' => $this->jobModel->getJobsByClient($_SESSION['user_id'])
            ]);
        }
    }

    private function loadView($view, $data = []) {
        extract($data);
        $viewFile = "../app/views/{$view}.php";
        if (file_exists($viewFile)) include $viewFile;
        else die("View not found: {$view}");
    }
}
?>