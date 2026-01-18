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
                
                if ($user['role'] == 'admin') {
                    header('Location: ' . BASE_URL . 'admin/dashboard');
                } elseif ($user['role'] == 'instructor') {
                    header('Location: ' . BASE_URL . 'instructor/index');
                } elseif ($user['role'] == 'client') {
                    header('Location: ' . BASE_URL . 'client/index');
                } else {
                    header('Location: ' . BASE_URL . 'dashboard/index');
                }
                exit;
            } else {
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

            // 1. Validation
            if (empty($name) || empty($email) || empty($password)) {
                $this->view('auth/register', ['error' => 'Please fill in all fields.']);
                return;
            }

            // 2. Validate Role (Security)
            $allowed_roles = ['learner', 'instructor', 'client', 'admin'];
            if (!in_array($role, $allowed_roles)) {
                $role = 'learner'; // Default fallback
            }

            if ($this->userModel->findUserByEmail($email)) {
                $this->view('auth/register', ['error' => 'User with this email already exists.']);
                return;
            }

            // 3. Admin Invite Logic
            $adminModel = $this->model('Admin');
            $is_admin_invite = false;

            if ($adminModel->isInvited($email)) {
                $role = 'admin'; 
                $is_admin_invite = true;
            }

            $data = [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role
            ];

            if ($this->userModel->register($data)) {
                // Remove invite from whitelist so it can't be used again
                if ($is_admin_invite) {
                    $adminModel->consumeInvite($email);
                }

                header('Location: ' . BASE_URL . 'auth/login');
                exit;
            } else {
                $this->view('auth/register', ['error' => 'Registration failed.']);
            }
        } else {
            $this->view('auth/register');
        }
    }

    // ... (Keep apiCheckEmail, profile, updateProfile, logout exactly as they were) ...
    // They were correct in your code.
    public function apiCheckEmail() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $email = trim($data['email'] ?? '');
            
            $adminModel = $this->model('Admin');

            if (empty($email)) {
                echo json_encode(['status' => 'error', 'message' => 'Email cannot be empty']);
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['status' => 'invalid', 'message' => 'Invalid email format']);
            } elseif ($this->userModel->findUserByEmail($email)) {
                echo json_encode(['status' => 'taken', 'message' => '❌ Email is already registered']);
            } elseif ($adminModel->isInvited($email)) {
                echo json_encode(['status' => 'available', 'message' => '✅ Email available', 'is_admin_invite' => true]);
            } else {
                echo json_encode(['status' => 'available', 'message' => '✅ Email available', 'is_admin_invite' => false]);
            }
            exit;
        }
    }

    public function profile() {
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        $this->view('auth/profile', ['user' => $user]);
    }
    
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $current_password = $_POST['current_password'];
            $new_password = !empty($_POST['new_password']) ? trim($_POST['new_password']) : null;
            $user_id = $_SESSION['user_id'];

            $currentUser = $this->userModel->getUserById($user_id);

            if (!password_verify($current_password, $currentUser['password'])) {
                $this->view('auth/profile', ['user' => $currentUser, 'error' => '❌ Incorrect Current Password.']);
                return;
            }

            $data = [
                'id' => $user_id,
                'name' => $name,
                'email' => $email,
                'password' => $new_password ? password_hash($new_password, PASSWORD_DEFAULT) : null
            ];

            if ($this->userModel->updateProfile($data)) {
                $_SESSION['user_name'] = $name;
                echo "<script>alert('✅ Profile Updated!'); window.location.href='" . BASE_URL . "auth/profile';</script>";
            } else {
                 $this->view('auth/profile', ['user' => $currentUser, 'error' => '❌ Update failed.']);
            }
        }
    }
    
    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }
}