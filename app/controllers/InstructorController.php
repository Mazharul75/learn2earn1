<?php
require_once '../app/models/Course.php';
require_once '../app/models/Quiz.php';
require_once '../app/models/Progress.php';
require_once '../app/models/Job.php';
require_once '../app/models/Recommendation.php';
require_once '../app/models/Notification.php';
require_once '../app/models/CourseRequest.php';
require_once '../app/models/Enrollment.php';

class InstructorController {
    private $courseModel;
    private $quizModel;
    private $progressModel;
    private $jobModel;
    private $recModel;
    private $notifyModel;
    private $requestModel;
    private $enrollModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'instructor') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        $this->courseModel = new Course();
        $this->quizModel = new Quiz();
        $this->progressModel = new Progress();
        $this->jobModel = new Job();
        $this->recModel = new Recommendation();
        $this->notifyModel = new Notification();
        $this->requestModel = new CourseRequest();
        $this->enrollModel = new Enrollment();
    }

    public function index() {
        $courses = $this->courseModel->getCoursesByInstructor($_SESSION['user_id']);
        $this->loadView('instructor/dashboard', ['courses' => $courses]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'difficulty' => $_POST['difficulty'] ?? 'Beginner',
                'max_capacity' => (int)($_POST['max_capacity'] ?? 0),
                'reserved_seats' => (int)($_POST['reserved_seats'] ?? 0),
                'prerequisite_id' => !empty($_POST['prerequisite_id']) ? $_POST['prerequisite_id'] : null
            ];

            if ($data['difficulty'] != 'Beginner' && empty($data['prerequisite_id'])) {
                $courses = $this->courseModel->getCourseList();
                $this->loadView('instructor/create_course', [
                    'courses' => $courses, 
                    'error' => 'Intermediate and Advanced courses MUST have a Prerequisite course selected.'
                ]);
                return;
            }

            if ($this->courseModel->addCourse($data)) {
                header('Location: ' . BASE_URL . 'instructor/index');
                exit;
            }
        } else {
            $courses = $this->courseModel->getCourseList();
            $this->loadView('instructor/create_course', ['courses' => $courses]);
        }
    }

    public function manage($course_id) {
        $rawSubmissions = $this->progressModel->getPendingSubmissions($course_id);
        $submissions = [];
        foreach ($rawSubmissions as $sub) {
            $sub['file_url'] = BASE_URL . "public/uploads/tasks/" . $sub['submission_file'];
            $submissions[] = $sub;
        }

        $this->loadView('instructor/manage_course', [
            'course' => $this->courseModel->getCourseById($course_id),
            'materials' => $this->courseModel->getMaterials($course_id),
            'questions' => $this->quizModel->getQuizQuestions($course_id),
            'submissions' => $submissions
        ]);
    }

    public function students($course_id) {
        $course = $this->courseModel->getCourseById($course_id);
        $students = $this->courseModel->getStudentsByCourse($course_id);
        $this->loadView('instructor/students', ['course' => $course, 'students' => $students]);
    }

    public function uploadMaterial() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['material'])) {
            $course_id = $_POST['course_id'] ?? 0;
            $upload_dir = "../public/uploads/materials/"; 
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $file_name = uniqid() . '_' . $_FILES['material']['name'];

            if (move_uploaded_file($_FILES['material']['tmp_name'], $upload_dir . $file_name)) {
                $this->courseModel->addMaterial(['course_id' => $course_id, 'file_name' => $file_name]);
                header('Location: ' . BASE_URL . 'instructor/manage/' . $course_id);
                exit;
            }
        }
    }

    public function createTask() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->courseModel->addTask($_POST['course_id'], $_POST['title'] ?? '', $_POST['description'] ?? '');
            header('Location: ' . BASE_URL . 'instructor/manage/' . $_POST['course_id']);
            exit;
        }
    }

    public function createQuiz() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->quizModel->addQuestion($_POST)) {
                header('Location: ' . BASE_URL . 'instructor/manage/' . $_POST['course_id']);
                exit;
            }
        }
    }

    public function reviewTask() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->progressModel->updateTaskStatus($_POST['completion_id'], $_POST['status'], $_POST['feedback'] ?? '');
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function viewJobs() {
        $this->loadView('instructor/jobs_list', ['jobs' => $this->jobModel->getAllJobs()]);
    }

    public function recommend($job_id) {
        $job = $this->jobModel->getJobById($job_id);
        $eligibleStudents = [];
        if (!empty($job['required_course_id'])) {
            $eligibleStudents = $this->courseModel->getCompletedStudents($job['required_course_id']);
        }
        $this->loadView('instructor/recommend_student', ['job' => $job, 'students' => $eligibleStudents]);
    }

    public function submitRecommendation() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $job_id = $_POST['job_id'];
            $learner_id = $_POST['learner_id'];
            
            $this->recModel->add($job_id, $learner_id, $_SESSION['user_id']);
            
            $job = $this->jobModel->getJobById($job_id);
            $this->notifyModel->create($learner_id, "You have been recommended for: " . $job['title'], BASE_URL . "learner/applyForm/" . $job_id);
            
            echo "<script>alert('Recommendation sent!'); window.location.href='" . BASE_URL . "instructor/viewJobs';</script>";
        }
    }

    public function requests() {
        $requests = $this->requestModel->getRequestsByInstructor($_SESSION['user_id']);
        $this->loadView('instructor/requests', ['requests' => $requests]);
    }

    public function handleRequest($request_id, $action) {
        if ($action == 'approve') {
            $req = $this->requestModel->approveRequest($request_id);
            if ($req) {
                $this->enrollModel->enroll($req['learner_id'], $req['course_id']);
                $this->notifyModel->create($req['learner_id'], "Request Approved!", BASE_URL . "learner/progress/" . $req['course_id']);
                echo "<script>alert('Approved!'); window.location.href='" . BASE_URL . "instructor/requests';</script>";
            }
        } elseif ($action == 'reject') {
            $this->requestModel->rejectRequest($request_id);
            echo "<script>alert('Rejected.'); window.location.href='" . BASE_URL . "instructor/requests';</script>";
        }
    }

    public function searchStudentsApi() {
        if (isset($_GET['course_id']) && isset($_GET['query'])) {
            $students = $this->courseModel->searchEnrolledStudents($_GET['course_id'], trim($_GET['query'] ?? ''));
            header('Content-Type: application/json');
            echo json_encode($students);
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