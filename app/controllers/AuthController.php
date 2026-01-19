<?php
require_once '../app/models/User.php';
require_once '../app/models/Admin.php';

class AuthController {
    private $userModel;
    private $adminModel;

    public function __construct() {
        $this->userModel = new User();
        $this->adminModel = new Admin();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Teacher Style: Null Coalescing Operator
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            
            $user = $this->userModel->login($email, $password);

            if ($user) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                
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
                $this->loadView('auth/login', ['error' => '❌ Invalid Email or Password']);
            }
        } else {
            $this->loadView('auth/login');
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $role = $_POST['role'] ?? 'learner';

            if (empty($name) || empty($email) || empty($password)) {
                $this->loadView('auth/register', ['error' => 'Please fill in all fields.']);
                return;
            }

            if ($this->userModel->findUserByEmail($email)) {
                $this->loadView('auth/register', ['error' => 'User with this email already exists.']);
                return;
            }

            $is_admin_invite = $this->adminModel->isInvited($email);
            if ($is_admin_invite) {
                $role = 'admin'; 
            }

            $data = [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role
            ];

            if ($this->userModel->register($data)) {
                if ($is_admin_invite) {
                    $this->adminModel->consumeInvite($email);
                }
                header('Location: ' . BASE_URL . 'auth/login');
                exit;
            } else {
                $this->loadView('auth/register', ['error' => 'Registration failed.']);
            }
        } else {
            $this->loadView('auth/register');
        }
    }

    public function apiCheckEmail() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $email = trim($data['email'] ?? '');
            
            // 1. Check if Empty
            if (empty($email)) {
                echo json_encode(['status' => 'taken', 'message' => '❌ Email cannot be empty']);
            } 
            // 2. Check Format (Force 'taken' status so frontend shows Red Error)
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['status' => 'taken', 'message' => '❌ Invalid email format']);
            } 
            // 3. Check if Registered
            elseif ($this->userModel->findUserByEmail($email)) {
                echo json_encode(['status' => 'taken', 'message' => '❌ Email is already registered']);
            } 
            // 4. Check Admin Invite
            elseif ($this->adminModel->isInvited($email)) {
                echo json_encode(['status' => 'available', 'message' => '✅ Email available (Admin Invite)', 'is_admin_invite' => true]);
            } 
            // 5. Available
            else {
                echo json_encode(['status' => 'available', 'message' => '✅ Email available', 'is_admin_invite' => false]);
            }
            exit;
        }
    }

    public function profile() {
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        $this->loadView('auth/profile', ['user' => $user]);
    }
    
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $current_password = $_POST['current_password'] ?? '';
            $new_password = !empty($_POST['new_password']) ? trim($_POST['new_password']) : null;
            $user_id = $_SESSION['user_id'];

            $currentUser = $this->userModel->getUserById($user_id);

            if (!password_verify($current_password, $currentUser['password'])) {
                $this->loadView('auth/profile', ['user' => $currentUser, 'error' => '❌ Incorrect Current Password.']);
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
                 $this->loadView('auth/profile', ['user' => $currentUser, 'error' => '❌ Update failed.']);
            }
        }
    }

    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
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