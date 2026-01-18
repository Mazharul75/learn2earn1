<?php
class ClientController extends Controller {
    private $jobModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        $this->jobModel = $this->model('Job');
    }

    public function index() {
        $jobs = $this->jobModel->getJobsByClient($_SESSION['user_id']);
        $this->view('client/dashboard', ['jobs' => $jobs]);
    }

    public function applicants($job_id) {
        $jobAppModel = $this->model('JobApplication');
        $jobModel = $this->model('Job');
        $recModel = $this->model('Recommendation'); 
        
        $applicants = $jobAppModel->getApplicantsByJob($job_id);
        $job = $jobModel->getJobById($job_id);
        $recommendations = $recModel->getByJob($job_id); 

        $this->view('client/applicants', [
            'applicants' => $applicants,
            'job' => $job,
            'recommendations' => $recommendations
        ]);
    }

    public function updateApplication($app_id, $status) {
        $jobAppModel = $this->model('JobApplication');
        $allowed = ['selected', 'rejected'];
        
        if (in_array($status, $allowed)) {
            $jobAppModel->updateStatus($app_id, $status);
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function post() {
        $courseModel = $this->model('Course');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'required_course_id' => !empty($_POST['required_course_id']) ? $_POST['required_course_id'] : null
            ];
    
            if ($this->jobModel->addJob($data)) {
                header('Location: ' . BASE_URL . 'client/index');
                exit;
            }
        } else {
            $courses = $courseModel->getAllCourses();
            $this->view('client/post_job', ['courses' => $courses]);
        }
    }

    // Feature: Hire a learner directly (Alternative to updateApplication)
    public function select($app_id) {
        $jobAppModel = $this->model('JobApplication');
        if ($jobAppModel->selectLearner($app_id)) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }

    // Feature: Invite a learner from recommendations
    public function inviteLearner($learner_id, $job_id) {
        $notifyModel = $this->model('Notification');
        $jobModel = $this->model('Job'); 
        $jobAppModel = $this->model('JobApplication'); // Load this to update status
        
        // 1. Check if already invited/applied to prevent duplicates
        if ($jobAppModel->alreadyApplied($job_id, $learner_id)) {
             echo "<script>alert('⚠️ This learner has already applied or been invited.'); window.location.href='" . $_SERVER['HTTP_REFERER'] . "';</script>";
             return;
        }

        // 2. Get Job Details
        $job = $jobModel->getJobById($job_id);
        
        // 3. Create Notification
        $message = "You have been invited to apply for: " . $job['title'];
        $link = BASE_URL . "learner/applyForm/" . $job_id;
        
        // 4. Record the invitation in DB (as a special status or just notify)
        // For now, we just notify. If you want to track it in DB, use jobAppModel->inviteLearner
        if ($notifyModel->create($learner_id, $message, $link)) {
            echo "<script>
                    alert('✅ Invitation sent successfully!'); 
                    window.location.href='" . $_SERVER['HTTP_REFERER'] . "';
                  </script>";
        } else {
            die("Error sending invitation.");
        }
    }

    public function searchApplicantsApi() {
        if (isset($_GET['job_id']) && isset($_GET['query'])) {
            $job_id = $_GET['job_id'];
            $query = trim($_GET['query']);
            
            $jobAppModel = $this->model('JobApplication');
            $applicants = $jobAppModel->searchApplicants($job_id, $query);
            
            header('Content-Type: application/json');
            echo json_encode($applicants);
            exit;
        }
    }
}