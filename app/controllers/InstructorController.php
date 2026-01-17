<?php
class InstructorController extends Controller {
    private $courseModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'instructor') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        $this->courseModel = $this->model('Course');
    }

    public function index() {
        $courses = $this->courseModel->getCoursesByInstructor($_SESSION['user_id']);
        // We pass the data array to the view
        $this->view('instructor/dashboard', ['courses' => $courses]);
    }

    public function students($course_id) {
        // Security check: Ensure instructor owns this course
        $course = $this->courseModel->getCourseById($course_id);
        if($course['instructor_id'] != $_SESSION['user_id']){
            header('Location: ' . BASE_URL . 'instructor/index');
            exit;
        }

        $students = $this->courseModel->getStudentsByCourse($course_id);
        $this->view('instructor/students', ['course' => $course, 'students' => $students]);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'difficulty' => $_POST['difficulty'],
                'max_capacity' => (int)$_POST['max_capacity'],
                'reserved_seats' => (int)$_POST['reserved_seats'],
                'prerequisite_id' => $_POST['prerequisite_id']
            ];

            // LOGIC: If Intermediate/Advanced, Prerequisite is MANDATORY
            if ($data['difficulty'] != 'Beginner' && empty($data['prerequisite_id'])) {
                $courses = $this->courseModel->getCourseList(); // Re-fetch for view
                $this->view('instructor/create_course', [
                    'courses' => $courses, 
                    'error' => '⚠️ Intermediate and Advanced courses MUST have a Prerequisite course selected.'
                ]);
                return;
            }

            if ($this->courseModel->addCourse($data)) {
                header('Location: ' . BASE_URL . 'instructor/index');
                exit;
            } else {
                die("Something went wrong.");
            }
        } else {
            // GET Request: Load the form with existing courses
            $courses = $this->courseModel->getCourseList();
            $this->view('instructor/create_course', ['courses' => $courses]);
        }
    }


    public function createQuiz() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $quizModel = $this->model('Quiz');
            if ($quizModel->addQuestion($_POST)) {
                header('Location: ' . BASE_URL . 'instructor/manage/' . $_POST['course_id']);
                exit;
            }
        }
    }

    public function createTask() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->courseModel->addTask($_POST['course_id'], $_POST['title'], $_POST['description']);
            header('Location: ' . BASE_URL . 'instructor/manage/' . $_POST['course_id']);
            exit;
        }
    }

    public function reviewTask() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $progressModel = $this->model('Progress');
            $progressModel->updateTaskStatus($_POST['completion_id'], $_POST['status'], $_POST['feedback']);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function manage($course_id) {
        $course = $this->courseModel->getCourseById($course_id);
        $materials = $this->courseModel->getMaterials($course_id);
        $quizModel = $this->model('Quiz');
        $questions = $quizModel->getQuizQuestions($course_id);
        
        $progressModel = $this->model('Progress');
        $rawSubmissions = $progressModel->getPendingSubmissions($course_id);

        // STRICT MVC: Process data for the view
        $submissions = [];
        foreach ($rawSubmissions as $sub) {
            $sub['file_url'] = BASE_URL . "public/uploads/tasks/" . $sub['submission_file'];
            $submissions[] = $sub;
        }

        $this->view('instructor/manage_course', [
            'course' => $course,
            'materials' => $materials,
            'questions' => $questions,
            'submissions' => $submissions
        ]);
    }
    
    public function uploadMaterial() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['material'])) {
            $course_id = $_POST['course_id'];
            
            // FIX 1: Validate File Type
            $allowed = ['pdf', 'jpg', 'png', 'doc', 'docx', 'zip'];
            $ext = strtolower(pathinfo($_FILES['material']['name'], PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                die("Error: Only PDF, JPG, PNG, DOC, and ZIP files allowed.");
            }

            // FIX 2: Safer Permissions (0755 instead of 0777)
            $upload_dir = "../public/uploads/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            // FIX 3: Unique filename to prevent overwriting
            $file_name = uniqid() . '_' . $_FILES['material']['name'];

            if (move_uploaded_file($_FILES['material']['tmp_name'], $upload_dir . $file_name)) {
                $data = ['course_id' => $course_id, 'file_name' => $file_name];
                if ($this->courseModel->addMaterial($data)) {
                    header('Location: ' . BASE_URL . 'instructor/manage/' . $course_id);
                    exit;
                }
            }
        }
        die("Upload failed.");
    }

    // View available jobs to recommend students for
    public function viewJobs() {
        $jobModel = $this->model('Job');
        $jobs = $jobModel->getAllJobs();
        $this->view('instructor/jobs_list', ['jobs' => $jobs]);
    }

    // Show form to pick a student (Fixed Logic)
    public function recommend($job_id) {
        $jobModel = $this->model('Job');
        $courseModel = $this->model('Course'); // You need to add this method to Course model below
        
        // 1. Get Job Details to find the Requirement
        $job = $jobModel->getJobById($job_id);
        
        if (!$job) {
            die("Job not found.");
        }

        $required_course_id = $job['required_course_id'];
        $eligibleStudents = [];

        // 2. Logic: Only show students if there is a required course
        if (!empty($required_course_id)) {
            // Fetch students who have COMPLETED (progress=100) this specific course
            $eligibleStudents = $courseModel->getCompletedStudents($required_course_id);
        } else {
            // Optional: If no course is required, you might show all completed students 
            // of the instructor's courses. For now, let's keep it strict.
            $eligibleStudents = []; 
        }

        $this->view('instructor/recommend_student', [
            'job' => $job,
            'students' => $eligibleStudents
        ]);
    }

    // Submit and NOTIFY Learner immediately
    public function submitRecommendation() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $recModel = $this->model('Recommendation');
            $notifyModel = $this->model('Notification');
            $jobModel = $this->model('Job');
            
            $job_id = $_POST['job_id'];
            $learner_id = $_POST['learner_id'];
            $instructor_id = $_SESSION['user_id'];

            // 1. Try to Add Recommendation
            $added = $recModel->add($job_id, $learner_id, $instructor_id);

            // 2. Logic Fix: Send notification if added OR if it already exists (so you can test)
            // Ideally, you only notify on success, but for testing, let's force it.
            
            $job = $jobModel->getJobById($job_id);
            $jobTitle = $job['title'];
            $link = BASE_URL . "learner/applyForm/" . $job_id;
            $message = "🎉 You have been recommended by your Instructor for the job: $jobTitle. Please apply now!";

            // Create notification regardless of whether recommendation existed or is new
            if ($notifyModel->create($learner_id, $message, $link)) {
                
                // Success Message
                if ($added) {
                    echo "<script>alert('Recommendation sent and Student Notified!'); window.location.href='" . BASE_URL . "instructor/viewJobs';</script>";
                } else {
                    echo "<script>alert('Student was ALREADY recommended, but we sent the notification again!'); window.location.href='" . BASE_URL . "instructor/viewJobs';</script>";
                }
                
            } else {
                die("Error: Could not create notification. Check Notification Model.");
            }
        }
    }







        // Show the requests page
    public function requests() {
        $requestModel = $this->model('CourseRequest');
        $requests = $requestModel->getRequestsByInstructor($_SESSION['user_id']);
        
        $this->view('instructor/requests', ['requests' => $requests]);
    }

    // Handle Approval
    public function handleRequest($request_id, $action) {
        $requestModel = $this->model('CourseRequest');
        $enrollModel = $this->model('Enrollment');
        $notifyModel = $this->model('Notification');

        if ($action == 'approve') {
            // 1. Mark Approved
            $req = $requestModel->approveRequest($request_id);
            
            if ($req) {
                // 2. Enroll the student
                $enrollModel->enroll($req['learner_id'], $req['course_id']);

                // 3. Notify Student
                $message = "🎉 Request Approved! You have been granted a reserved seat.";
                $link = BASE_URL . "learner/progress/" . $req['course_id'];
                $notifyModel->create($req['learner_id'], $message, $link);

                echo "<script>alert('Student Approved & Enrolled!'); window.location.href='" . BASE_URL . "instructor/requests';</script>";
            }
        } elseif ($action == 'reject') {
            $requestModel->rejectRequest($request_id);
            echo "<script>alert('Request Rejected.'); window.location.href='" . BASE_URL . "instructor/requests';</script>";
        }
    }

}