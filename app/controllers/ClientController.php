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
        $recModel = $this->model('Recommendation'); // Load Recommendation Model
        
        $applicants = $jobAppModel->getApplicantsByJob($job_id);
        $job = $jobModel->getJobById($job_id);
        $recommendations = $recModel->getByJob($job_id); // Fetch recommendations

        $this->view('client/applicants', [
            'applicants' => $applicants,
            'job' => $job,
            'recommendations' => $recommendations
        ]);
    }

    public function updateApplication($app_id, $status) {
        $jobAppModel = $this->model('JobApplication');
        
        // Validate status against your SQL ENUM
        $allowed = ['selected', 'rejected'];
        if (in_array($status, $allowed)) {
            $jobAppModel->updateStatus($app_id, $status);
        }
        
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function post() {
        $courseModel = $this->model('Course'); // Load the Course model
        
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
            // Fetch all courses to show in the dropdown
            $courses = $courseModel->getAllCourses();
            $this->view('client/post_job', ['courses' => $courses]);
        }
    }

    // Feature 2: Select a learner
    public function select($app_id) {
        // FIX: Use the Model instead of $this->db
        $jobAppModel = $this->model('JobApplication');
        if ($jobAppModel->selectLearner($app_id)) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }

    public function inviteLearner($learner_id, $job_id) {
        $notifyModel = $this->model('Notification');
        $jobModel = $this->model('Job'); 
        
        // 1. Get Job Title for the message
        $job = $jobModel->getJobById($job_id);
        $jobTitle = $job['title'];

        // 2. Create the Notification
        $message = "You have been invited by the Client to apply for the job: $jobTitle. Click to upload your CV.";
        $link = BASE_URL . "learner/applyForm/" . $job_id;
        
        if ($notifyModel->create($learner_id, $message, $link)) {
            // 3. Success Feedback: Alert and Redirect
            echo "<script>
                    alert('✅ Invitation sent successfully to the learner!'); 
                    window.location.href='" . $_SERVER['HTTP_REFERER'] . "';
                  </script>";
        } else {
            die("Error sending invitation.");
        }
    }

    // Feature 3: Invite recommended learners
    public function invite($learner_id, $job_id) {
        // FIX: Use the Model instead of $this->db
        $jobAppModel = $this->model('JobApplication');
        if ($jobAppModel->inviteLearner($learner_id, $job_id)) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    }
}