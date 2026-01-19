<?php
require_once '../app/models/Job.php';
require_once '../app/models/JobApplication.php';
require_once '../app/models/Recommendation.php';
require_once '../app/models/Course.php';
require_once '../app/models/Notification.php';

class ClientController {
    private $jobModel;
    private $jobAppModel;
    private $recModel;
    private $courseModel;
    private $notifyModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'client') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        $this->jobModel = new Job();
        $this->jobAppModel = new JobApplication();
        $this->recModel = new Recommendation();
        $this->courseModel = new Course();
        $this->notifyModel = new Notification();
    }

    public function index() {
        $jobs = $this->jobModel->getJobsByClient($_SESSION['user_id']);
        $this->loadView('client/dashboard', ['jobs' => $jobs]);
    }

    public function applicants($job_id) {
        $this->loadView('client/applicants', [
            'applicants' => $this->jobAppModel->getApplicantsByJob($job_id),
            'job' => $this->jobModel->getJobById($job_id),
            'recommendations' => $this->recModel->getByJob($job_id)
        ]);
    }

    public function updateApplication($app_id, $status) {
        if (in_array($status, ['selected', 'rejected'])) {
            $this->jobAppModel->updateStatus($app_id, $status);
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function post() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'required_course_id' => !empty($_POST['required_course_id']) ? $_POST['required_course_id'] : null
            ];
            
            if ($this->jobModel->addJob($data)) {
                header('Location: ' . BASE_URL . 'client/index');
                exit;
            }
        } else {
            $this->loadView('client/post_job', ['courses' => $this->courseModel->getAllCourses()]);
        }
    }

    public function inviteLearner($learner_id, $job_id) {
        // 1. Check if already applied/invited 
        if ($this->jobAppModel->alreadyApplied($job_id, $learner_id)) {
             echo "<script>alert('Already invited or applied.'); window.location.href='" . $_SERVER['HTTP_REFERER'] . "';</script>";
             return;
        }

        // 2. SAVE the invitation in the database
        $this->jobAppModel->recordInvitation($job_id, $learner_id);

        // 3. Send the Notification
        $job = $this->jobModel->getJobById($job_id);
        $this->notifyModel->create($learner_id, "Invited to apply for: " . $job['title'], BASE_URL . "learner/applyForm/" . $job_id);

        // 4. Success
        echo "<script>alert('Invitation sent!'); window.location.href='" . $_SERVER['HTTP_REFERER'] . "';</script>";
    }

    public function select($app_id) {
        if ($this->jobAppModel->selectLearner($app_id)) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }

    public function searchApplicantsApi() {
        if (isset($_GET['job_id']) && isset($_GET['query'])) {
            $applicants = $this->jobAppModel->searchApplicants($_GET['job_id'], trim($_GET['query'] ?? ''));
            header('Content-Type: application/json');
            echo json_encode($applicants);
            exit;
        }
    }

    private function loadView($view, $data = []) {
        extract($data);
        $viewFile = "../app/views/{$view}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            http_response_code(404);
            include '../app/views/errors/404.php';
            exit;
        }
    }
}
?>