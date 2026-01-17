<?php
class LearnerController extends Controller {
    private $courseModel;
    private $enrollModel;
    private $jobModel;
    private $jobAppModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'learner') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        $this->courseModel = $this->model('Course');
        $this->enrollModel = $this->model('Enrollment');
        $this->jobModel = $this->model('Job');
        $this->jobAppModel = $this->model('JobApplication');
    }

    // Show all courses available for enrollment
    public function courses() {
        $allCourses = $this->courseModel->getAllCourses();
        
        // 1. Get IDs of courses the user has ALREADY enrolled in
        $myCourses = $this->enrollModel->getLearnerCourses($_SESSION['user_id']);
        $enrolledIds = array_column($myCourses, 'id'); // Extract IDs: [1, 3, 5]

        // 2. Filter available courses
        $availableCourses = [];
        foreach($allCourses as $course) {
            // If ID is NOT in the enrolled list, add it to available
            if (!in_array($course['id'], $enrolledIds)) {
                $availableCourses[] = $course;
            }
        }

        // 3. Send ONLY available courses to the view
        $this->view('learner/courses', ['allCourses' => $availableCourses]);
    }

    

    public function enroll($course_id) {
        $learner_id = $_SESSION['user_id'];
        
        // 1. Check if already enrolled
        if ($this->enrollModel->isEnrolled($learner_id, $course_id)) {
            echo "<script>alert('You are already enrolled!'); window.location.href='" . BASE_URL . "learner/courses';</script>";
            exit;
        }

        // 2. Check if already REQUESTED (Pending)
        $requestModel = $this->model('CourseRequest'); // Create this model in Step 3
        if ($requestModel->hasPendingRequest($learner_id, $course_id)) {
             echo "<script>alert('⏳ You have already requested a seat. Please wait for instructor approval.'); window.location.href='" . BASE_URL . "learner/courses';</script>";
             exit;
        }

        // 3. FETCH COURSE DETAILS
        $course = $this->courseModel->getCourseById($course_id);
        $currentCount = $this->enrollModel->countEnrollments($course_id);

        // 4. CALCULATE LIMITS
        $max = $course['max_capacity'];
        $reserved = $course['reserved_seats'];
        $public_limit = $max - $reserved;

        // 5. APPLY LOGIC
        if ($currentCount >= $max) {
            // Case: Totally Full
            echo "<script>alert('⛔️ Course is completely FULL.'); window.location.href='" . BASE_URL . "learner/courses';</script>";
            exit;

        } elseif ($currentCount >= $public_limit) {
            // Case: Public Full -> Request Reserved Seat
            // Redirect to a confirmation page or auto-request
            $this->requestReservedSeat($learner_id, $course_id);
            
        } else {
            // Case: Available -> Enroll Normally
            $this->enrollModel->enroll($learner_id, $course_id);
            header('Location: ' . BASE_URL . 'dashboard/index');
            exit;
        }
    }

    // Helper to handle the request logic
    private function requestReservedSeat($learner_id, $course_id) {
        $requestModel = $this->model('CourseRequest');
        
        if ($requestModel->createRequest($learner_id, $course_id)) {
            echo "<script>
                alert('⚠️ Public seats are full! A request for a RESERVED SEAT has been sent to the instructor.'); 
                window.location.href='" . BASE_URL . "learner/courses';
            </script>";
        } else {
            die("Error sending request.");
        }
    }








    public function jobs() {
        $allJobs = $this->jobModel->getAllJobs();
        $progressModel = $this->model('Progress');
        $jobAppModel = $this->model('JobApplication'); // Load this model
        $learner_id = $_SESSION['user_id'];

        // 1. Get IDs of jobs user has already applied to
        $myApps = $jobAppModel->getLearnerApplications($learner_id);
        // Note: getLearnerApplications returns titles/status, let's fix the SQL or filter differently.
        // Better Approach: Check individually or fetch IDs. 
        // For simplicity/performance, let's just use alreadyApplied() inside the loop.

        $availableJobs = [];

        foreach ($allJobs as $job) {
            // 2. Filter: If already applied, SKIP this job
            if ($jobAppModel->alreadyApplied($job['id'], $learner_id)) {
                continue; 
            }

            // 3. Check Lock Status (Existing Logic)
            if (!empty($job['required_course_id'])) {
                $job['is_unlocked'] = $progressModel->isCourseCompleted($job['required_course_id'], $learner_id);
            } else {
                $job['is_unlocked'] = true;
            }
            
            $availableJobs[] = $job;
        }
    
        $this->view('learner/jobs', ['allJobs' => $availableJobs]);
    }
    
    public function apply($job_id) {
        $learner_id = $_SESSION['user_id'];
        
        if (!$this->jobAppModel->alreadyApplied($job_id, $learner_id)) {
            $this->jobAppModel->apply($job_id, $learner_id);
        }
        
        header('Location: ' . BASE_URL . 'dashboard/index');
        exit;
    }

    public function search() {
        if (isset($_GET['query'])) {
            $keyword = trim($_GET['query']);
            $courses = $this->courseModel->searchCourses($keyword);
            
            // Return data as JSON for AJAX 
            header('Content-Type: application/json');
            echo json_encode($courses);
            exit;
        }
    }

    public function progress($course_id) {
        $progressModel = $this->model('Progress');
        $courseModel = $this->model('Course');
        
        // 1. Fetch Raw Data
        $rawTasks = $progressModel->getTasksByCourse($course_id, $_SESSION['user_id']);
        
        // 2. STRICT MVC: Prepare "View Data" (Logic happens here, not in HTML)
        $processedTasks = [];
        foreach ($rawTasks as $task) {
            // Determine Status Label and Color
            if ($task['status'] == 'approved') {
                $task['status_label'] = '✅ Approved';
                $task['status_color'] = 'green';
                $task['is_uploadable'] = false; // Hide button
            } elseif ($task['status'] == 'rejected') {
                $task['status_label'] = '❌ Rejected (Please Resubmit)';
                $task['status_color'] = 'red';
                $task['is_uploadable'] = true; // Show button
            } elseif ($task['status'] == 'pending') {
                $task['status_label'] = '⏳ Under Review';
                $task['status_color'] = 'orange';
                $task['is_uploadable'] = false; // Hide button
            } else {
                $task['status_label'] = 'Not Submitted';
                $task['status_color'] = 'gray';
                $task['is_uploadable'] = true; // Show button
            }
            
            $processedTasks[] = $task;
        }

        // 3. Prepare other data
        $data = [
            'course' => $courseModel->getCourseById($course_id),
            'materials' => $courseModel->getMaterials($course_id),
            'tasks' => $processedTasks, // Send the processed tasks
            'allFinished' => $progressModel->checkPrerequisites($course_id, $_SESSION['user_id']),
            'course_id' => $course_id
        ];

        $this->view('learner/progress', $data);
    }

    public function submitTask() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['task_file'])) {
            $progressModel = $this->model('Progress');
            $learner_id = $_SESSION['user_id'];
            $task_id = $_POST['task_id'];
            
            // Upload Logic
            $upload_dir = "../public/uploads/tasks/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            
            $file_name = time() . "_" . $_FILES['task_file']['name'];
            
            if (move_uploaded_file($_FILES['task_file']['tmp_name'], $upload_dir . $file_name)) {
                $progressModel->submitTask($learner_id, $task_id, $file_name);
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        }
        die("Upload failed.");
    }
    
    public function completeTask($task_id) {
        $progressModel = $this->model('Progress');
        if ($progressModel->markTaskDone($task_id, $_SESSION['user_id'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    // Feature 3: Apply for jobs 
    public function applyForJob($job_id) {
        $jobAppModel = $this->model('JobApplication');
        if($jobAppModel->apply($job_id, $_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'dashboard/index');
        }
    }

    public function applyForm($job_id) {
        // 1. Check if already applied
        if ($this->jobAppModel->alreadyApplied($job_id, $_SESSION['user_id'])) {
            // Show a friendly error view
            echo "<div style='text-align:center; padding:50px; font-family: sans-serif;'>
                    <h2 style='color:green;'>✅ You have already applied for this job!</h2>
                    <p>You cannot apply twice. Check your dashboard for status updates.</p>
                    <a href='" . BASE_URL . "dashboard/index' style='background:#2ecc71; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Go to Dashboard</a>
                  </div>";
            exit;
        }

        // 2. If not applied, show the form
        $job = $this->jobModel->getJobById($job_id);
        $this->view('learner/apply_job', ['job' => $job]);
    }

    // 2. Handle the POST request with CV Upload
    public function submitApplication() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['cv'])) {
            $job_id = $_POST['job_id'];
            $learner_id = $_SESSION['user_id'];

            // File Upload Logic
            $upload_dir = "../public/uploads/cvs/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $ext = strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION));
            if ($ext != 'pdf') { die("Only PDF CVs allowed."); }

            $filename = "cv_" . $learner_id . "_" . time() . ".pdf";
            
            if (move_uploaded_file($_FILES['cv']['tmp_name'], $upload_dir . $filename)) {
                // Save to DB
                if ($this->jobAppModel->apply($job_id, $learner_id, $filename)) {
                    // Send notification to self (optional) or just redirect
                    header('Location: ' . BASE_URL . 'dashboard/index');
                    exit;
                }
            }
        }
        die("Application failed.");
    }

    public function index() {
        // 1. Load Models
        $enrollModel = $this->model('Enrollment');
        $jobAppModel = $this->model('JobApplication');
        $notifyModel = $this->model('Notification'); // <--- CRITICAL: Load this model

        // 2. Fetch Data
        $myCourses = $enrollModel->getLearnerCourses($_SESSION['user_id']);
        $myJobs = $jobAppModel->getLearnerApplications($_SESSION['user_id']);
        
        // 3. CRITICAL: Fetch the notifications for the logged-in user
        $myNotifications = $notifyModel->getUnread($_SESSION['user_id']);

        // 4. Pass EVERYTHING to the view
        $data = [
            'myCourses' => $myCourses,
            'myJobs' => $myJobs,
            'notifications' => $myNotifications // <--- CRITICAL: Pass this variable
        ];
        
        $this->view('learner/dashboard', $data);
    }
    
    // New method to view specific course details (Materials + Tasks)
    public function viewCourse($course_id) {
        $course = $this->courseModel->getCourseById($course_id);
        $materials = $this->courseModel->getMaterials($course_id);
        $progressModel = $this->model('Progress');
        $tasks = $progressModel->getTasksByCourse($course_id, $_SESSION['user_id']);
    
        $this->view('learner/course_view', [
            'course' => $course,
            'materials' => $materials,
            'tasks' => $tasks
        ]);
    }

    public function checkout($material_id) {
        $progressModel = $this->model('Progress');
        if ($progressModel->checkoutMaterial($_SESSION['user_id'], $material_id)) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function takeQuiz($course_id) {
        $quizModel = $this->model('Quiz');
        $questions = $quizModel->getQuizQuestions($course_id);
        $this->view('learner/take_quiz', ['questions' => $questions, 'course_id' => $course_id]);
    }
    
    public function submitQuiz($course_id) {
        $quizModel = $this->model('Quiz');
        $progressModel = $this->model('Progress');
        
        // 1. Get detailed results
        $result = $quizModel->gradeQuiz($course_id, $_POST['answer']);

        if ($result['passed']) {
            // 2. Mark course as complete (Progress = 100)
            $progressModel->markCourseComplete($_SESSION['user_id'], $course_id);
            
            // 3. Show Success View
            $this->view('learner/quiz_result', [
                'status' => 'passed',
                'score' => $result['score'],
                'total' => $result['total'],
                'course_id' => $course_id
            ]);
        } else {
            // 4. Show Failure View
            $this->view('learner/quiz_result', [
                'status' => 'failed',
                'score' => $result['score'],
                'total' => $result['total'],
                'course_id' => $course_id
            ]);
        }
    }
}