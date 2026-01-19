<?php
require_once '../app/models/Course.php';
require_once '../app/models/Enrollment.php';
require_once '../app/models/Job.php';
require_once '../app/models/JobApplication.php';
require_once '../app/models/Progress.php';
require_once '../app/models/Quiz.php';
require_once '../app/models/Notification.php';
require_once '../app/models/CourseRequest.php';

class LearnerController {
    private $courseModel;
    private $enrollModel;
    private $jobModel;
    private $jobAppModel;
    private $progressModel;
    private $quizModel;
    private $notifyModel;
    private $requestModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'learner') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $this->courseModel = new Course();
        $this->enrollModel = new Enrollment();
        $this->jobModel = new Job();
        $this->jobAppModel = new JobApplication();
        $this->progressModel = new Progress();
        $this->quizModel = new Quiz();
        $this->notifyModel = new Notification();
        $this->requestModel = new CourseRequest();
    }

    public function index() {
        $myCourses = $this->enrollModel->getLearnerCourses($_SESSION['user_id']);
        $myJobs = $this->jobAppModel->getLearnerApplications($_SESSION['user_id']);
        $notifications = $this->notifyModel->getUnread($_SESSION['user_id']);

        $this->loadView('learner/dashboard', [
            'myCourses' => $myCourses,
            'myJobs' => $myJobs,
            'notifications' => $notifications
        ]);
    }
    public function apply($job_id) {
        header('Location: ' . BASE_URL . 'learner/applyForm/' . $job_id);
        exit;
    }

    public function applyForm($job_id) {
        $existingApp = $this->jobAppModel->alreadyApplied($job_id, $_SESSION['user_id']);

        if ($existingApp && strtolower($existingApp['status']) !== 'invited') {
             
             // Check if view exists
             $viewFile = "../app/views/learner/alreadyApplied.php";
             if (file_exists($viewFile)) {
                 require_once $viewFile;
             } else {
                 echo "<div style='text-align:center; padding:50px;'>
                        <h2 style='color:green;'>You have already applied!</h2>
                        <a href='" . BASE_URL . "dashboard/index'>Go Dashboard</a>
                      </div>";
             }
             exit;
        }

        // Load the form
        $job = $this->jobModel->getJobById($job_id);
        
        $viewFile = "../app/views/learner/applyForm.php";
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            $this->loadView('learner/apply_job', ['job' => $job]);
        }
    }

    public function submitApplication() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['cv'])) {
            $job_id = $_POST['job_id'];
            $upload_dir = "../public/uploads/cvs/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $ext = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
            if ($ext != 'pdf') { die("Only PDF CVs allowed."); }
            $filename = "cv_" . $_SESSION['user_id'] . "_" . time() . ".pdf";
            if (move_uploaded_file($_FILES['cv']['tmp_name'], $upload_dir . $filename)) {
                $this->jobAppModel->apply($job_id, $_SESSION['user_id'], $filename);
                header('Location: ' . BASE_URL . 'dashboard/index');
                exit;
            }
        }
        die("Application failed.");
    }

    public function jobs() {
        $allJobs = $this->jobModel->getAllJobs();
        $availableJobs = [];
        foreach ($allJobs as $job) {
            $existingApp = $this->jobAppModel->alreadyApplied($job['id'], $_SESSION['user_id']);
            if ($existingApp && strtolower($existingApp['status']) !== 'invited') {
                continue; 
            }
            if (!empty($job['required_course_id'])) {
                $job['is_unlocked'] = $this->progressModel->isCourseCompleted($job['required_course_id'], $_SESSION['user_id']);
            } else {
                $job['is_unlocked'] = true;
            }
            $availableJobs[] = $job;
        }
        $this->loadView('learner/jobs', ['allJobs' => $availableJobs]);
    }

    public function courses() {
        $allCourses = $this->courseModel->getAllCourses();
        $myCourses = $this->enrollModel->getLearnerCourses($_SESSION['user_id']);
        $enrolledIds = array_column($myCourses, 'id');
        $availableCourses = [];
        foreach($allCourses as $course) {
            if (!in_array($course['id'], $enrolledIds)) {
                $availableCourses[] = $course;
            }
        }
        $this->loadView('learner/courses', ['allCourses' => $availableCourses]);
    }

    public function enroll($course_id) {
        $learner_id = $_SESSION['user_id'];
        if ($this->enrollModel->isEnrolled($learner_id, $course_id)) {
            echo "<script>alert('You are already enrolled!'); window.location.href='" . BASE_URL . "learner/courses';</script>";
            exit;
        }
        $course = $this->courseModel->getCourseById($course_id);
        if (!empty($course['prerequisite_id'])) {
            if (!$this->enrollModel->hasCompleted($learner_id, $course['prerequisite_id'])) {
                $needed = $this->courseModel->getCourseById($course['prerequisite_id']);
                $neededTitle = $needed['title'];
                echo "<script>alert('⛔ PREREQUISITE MISSING!'); window.location.href='" . BASE_URL . "learner/courses';</script>";
                exit;
            }
        }
        if ($this->requestModel->hasPendingRequest($learner_id, $course_id)) {
             echo "<script>alert('⏳ Already requested.'); window.location.href='" . BASE_URL . "learner/courses';</script>";
             exit;
        }
        $currentCount = $this->enrollModel->countEnrollments($course_id);
        $max = $course['max_capacity'];
        $reserved = $course['reserved_seats'];
        $public_limit = $max - $reserved;
        if ($currentCount >= $max) {
            echo "<script>alert('⛔️ Course FULL.'); window.location.href='" . BASE_URL . "learner/courses';</script>";
            exit;
        } elseif ($currentCount >= $public_limit) {
            $this->requestReservedSeat($learner_id, $course_id);
        } else {
            $this->enrollModel->enroll($learner_id, $course_id);
            header('Location: ' . BASE_URL . 'dashboard/index');
            exit;
        }
    }

    private function requestReservedSeat($learner_id, $course_id) {
        if ($this->requestModel->createRequest($learner_id, $course_id)) {
            echo "<script>alert('Request Sent.'); window.location.href='" . BASE_URL . "learner/courses';</script>";
        } else {
            die("Error sending request.");
        }
    }

    public function search() {
        $keyword = isset($_GET['query']) ? trim($_GET['query']) : '';
        if ($keyword === '') {
            $allCourses = $this->courseModel->getAllCourses();
        } else {
            $allCourses = $this->courseModel->searchCourses($keyword);
        }
        $myCourses = $this->enrollModel->getLearnerCourses($_SESSION['user_id']);
        $enrolledIds = array_column($myCourses, 'id');
        $availableCourses = [];
        if (!empty($allCourses)) {
            foreach($allCourses as $course) {
                if (!in_array($course['id'], $enrolledIds)) {
                    $availableCourses[] = $course;
                }
            }
        }
        header('Content-Type: application/json');
        echo json_encode($availableCourses);
        exit;
    }

    public function progress($course_id) {
        $rawTasks = $this->progressModel->getTasksByCourse($course_id, $_SESSION['user_id']);
        $materials = $this->courseModel->getMaterials($course_id);
        $checkedIDs = $this->progressModel->getCheckedMaterials($_SESSION['user_id'], $course_id);
        $is_empty = (empty($rawTasks) && empty($materials));
        $has_quiz = $this->quizModel->hasQuiz($course_id);
        $allFinished = (!$is_empty) ? $this->progressModel->checkPrerequisites($course_id, $_SESSION['user_id']) : false;
        $is_completed = $this->enrollModel->hasCompleted($_SESSION['user_id'], $course_id);
        $processedTasks = [];
        foreach ($rawTasks as $task) {
            if ($task['status'] == 'approved') {
                $task['status_label'] = '✅ Approved';
                $task['status_color'] = 'green';
                $task['is_uploadable'] = false;
            } elseif ($task['status'] == 'rejected') {
                $task['status_label'] = '❌ Rejected (Please Resubmit)';
                $task['status_color'] = 'red';
                $task['is_uploadable'] = true;
            } elseif ($task['status'] == 'pending') {
                $task['status_label'] = '⏳ Under Review';
                $task['status_color'] = 'orange';
                $task['is_uploadable'] = false;
            } else {
                $task['status_label'] = 'Not Submitted';
                $task['status_color'] = 'gray';
                $task['is_uploadable'] = true;
            }
            $processedTasks[] = $task;
        }
        $this->loadView('learner/progress', [
            'course' => $this->courseModel->getCourseById($course_id),
            'materials' => $materials,
            'tasks' => $processedTasks,
            'allFinished' => $allFinished,
            'course_id' => $course_id,
            'is_empty' => $is_empty,
            'has_quiz' => $has_quiz,
            'is_completed' => $is_completed,
            'checked_ids' => $checkedIDs
        ]);
    }

    public function submitTask() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['task_file'])) {
            $upload_dir = "../public/uploads/tasks/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $file_name = time() . "_" . $_FILES['task_file']['name'];
            if (move_uploaded_file($_FILES['task_file']['tmp_name'], $upload_dir . $file_name)) {
                $this->progressModel->submitTask($_SESSION['user_id'], $_POST['task_id'], $file_name);
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        }
        die("Upload failed.");
    }

    public function checkout($course_id, $material_id) {
        if ($this->progressModel->checkoutMaterial($_SESSION['user_id'], $material_id)) {
            header('Location: ' . BASE_URL . 'learner/progress/' . $course_id);
            exit;
        }
    }

    public function takeQuiz($course_id) {
        if (!$this->quizModel->hasQuiz($course_id)) {
            echo "<script>alert('No quiz.'); window.history.back();</script>";
            exit;
        }
        $questions = $this->quizModel->getQuizQuestions($course_id);
        $this->loadView('learner/take_quiz', ['questions' => $questions, 'course_id' => $course_id]);
    }

    public function submitQuiz($course_id) {
        $result = $this->quizModel->gradeQuiz($course_id, $_POST['answer']);
        if ($result['passed']) {
            $this->progressModel->markCourseComplete($_SESSION['user_id'], $course_id);
        }
        $this->loadView('learner/quiz_result', [
            'status' => $result['passed'] ? 'passed' : 'failed',
            'score' => $result['score'],
            'total' => $result['total'],
            'course_id' => $course_id
        ]);
    }

    public function viewCourse($course_id) {
        $course = $this->courseModel->getCourseById($course_id);
        $materials = $this->courseModel->getMaterials($course_id);
        $tasks = $this->progressModel->getTasksByCourse($course_id, $_SESSION['user_id']);
        $this->loadView('learner/course_view', [
            'course' => $course,
            'materials' => $materials,
            'tasks' => $tasks
        ]);
    }

    private function loadView($view, $data = []) {
        extract($data);
        $viewFile = "../app/views/{$view}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            http_response_code(404);
            if (file_exists('../app/views/errors/404.php')) {
                include '../app/views/errors/404.php';
            } else {
                echo "<h1>404 - View Not Found</h1>";
            }
            exit;
        }
    }
}
?>