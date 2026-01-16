<?php
class DashboardController extends Controller {
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    
        $role = $_SESSION['user_role'];
        
        // 1. Load Notification Model (Needed for all roles)
        $notifyModel = $this->model('Notification'); 

        if ($role == 'learner') {
            $enrollModel = $this->model('Enrollment');
            $jobAppModel = $this->model('JobApplication');
            
            // 2. Fetch Notifications for Learner
            $notifications = $notifyModel->getUnread($_SESSION['user_id']);

            $this->view('learner/dashboard', [
                'myCourses' => $enrollModel->getLearnerCourses($_SESSION['user_id']),
                'myJobs' => $jobAppModel->getLearnerApplications($_SESSION['user_id']),
                'notifications' => $notifications // <--- CRITICAL FIX
            ]);

        } elseif ($role == 'instructor') {
            $courseModel = $this->model('Course');
            $courses = $courseModel->getCoursesByInstructor($_SESSION['user_id']);
            $this->view('instructor/dashboard', ['courses' => $courses]);

        } elseif ($role == 'client') {
            $jobModel = $this->model('Job');
            $jobs = $jobModel->getJobsByClient($_SESSION['user_id']);
            $this->view('client/dashboard', ['jobs' => $jobs]);
        }
    }
}