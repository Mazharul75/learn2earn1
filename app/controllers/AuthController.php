<?php
class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            
            $user = $this->userModel->login($email, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role']; 
                
                // Redirect based on role
                if ($user['role'] == 'admin') {
                    header('Location: ' . BASE_URL . 'admin/dashboard');
                } else {
                    header('Location: ' . BASE_URL . 'dashboard/index');
                }
                exit;
            }else {
                $this->view('auth/login', ['error' => '❌ Invalid Email or Password']);
            }
        } else {
            $this->view('auth/login');
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $role = $_POST['role'];

            // 1. PHP VALIDATION: Check for Empty Fields
            if (empty($name) || empty($email) || empty($password)) {
                $this->view('auth/register', ['error' => 'Please fill in all fields.']);
                return;
            }

            // 2. PHP VALIDATION: Email Format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->view('auth/register', ['error' => 'Invalid email format.']);
                return;
            }

            // 3. PHP VALIDATION: Duplicate Check
            if ($this->userModel->findUserByEmail($email)) {
                $this->view('auth/register', ['error' => 'User with this email already exists.']);
                return;
            }

            // 4. PHP VALIDATION: Password Strength
            if (strlen($password) < 6) {
                $this->view('auth/register', ['error' => 'Password must be at least 6 characters.']);
                return;
            }

             // --- NEW: MAGIC ADMIN CHECK ---
            // Check if this email was invited by an Admin
            $adminModel = $this->model('Admin');
            if ($adminModel->isInvited($email)) {
                $role = 'admin'; // ⚡️ FORCE OVERRIDE: They become Admin automatically
            }


            $data = [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role
            ];

            if ($this->userModel->register($data)) {
                header('Location: ' . BASE_URL . 'auth/login');
                exit;
            } else {
                $this->view('auth/register', ['error' => 'Registration failed.']);
            }
        } else {
            $this->view('auth/register');
        }
    }


    public function apiCheckEmail() {
        // Only accept POST requests for security
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get raw JSON input (standard for modern AJAX)
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            $email = trim($data['email'] ?? '');
            
            // Logic
            if (empty($email)) {
                echo json_encode(['status' => 'error', 'message' => 'Email cannot be empty']);
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['status' => 'invalid', 'message' => 'Invalid email format']);
            } elseif ($this->userModel->findUserByEmail($email)) {
                echo json_encode(['status' => 'taken', 'message' => '❌ Email is already registered']);
            } else {
                echo json_encode(['status' => 'available', 'message' => '✅ Email is available']);
            }
            exit; // Stop script so only JSON is returned
        }
    }

    public function profile() {
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        $this->view('auth/profile', ['user' => $user]);
    }
    
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // 1. Sanitize Input
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $current_password = $_POST['current_password'];
            $new_password = !empty($_POST['new_password']) ? trim($_POST['new_password']) : null;
            $user_id = $_SESSION['user_id'];

            // 2. Fetch User to get the REAL password hash
            $currentUser = $this->userModel->getUserById($user_id);

            // 3. SECURITY CHECK: Verify Current Password
            if (!password_verify($current_password, $currentUser['password'])) {
                // Return to view with Error
                $this->view('auth/profile', [
                    'user' => $currentUser, 
                    'error' => '❌ Incorrect Current Password. Changes not saved.'
                ]);
                return;
            }

            // 4. Prepare Data for Update
            $data = [
                'id' => $user_id,
                'name' => $name,
                'email' => $email,
                'password' => $new_password ? password_hash($new_password, PASSWORD_DEFAULT) : null
            ];

            // 5. Attempt Update
            if ($this->userModel->updateProfile($data)) {
                // Update Session Name if changed
                $_SESSION['user_name'] = $name;
                
                // Success: Reload page showing new info
                echo "<script>alert('✅ Profile Updated Successfully!'); window.location.href='" . BASE_URL . "auth/profile';</script>";
            } else {
                 $this->view('auth/profile', [
                    'user' => $currentUser, 
                    'error' => '❌ Update failed. Email address might be already taken.'
                ]);
            }
        }
    }
    
    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }
}