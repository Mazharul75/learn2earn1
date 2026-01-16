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
                // We must use these exact keys 
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role']; 
                
                // Redirect to the dashboard method [cite: 1241]
                header('Location: ' . BASE_URL . 'dashboard/index');
                exit;
            } else {
                echo "Invalid Email/Password";
            }
        } else {
            $this->view('auth/login');
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => password_hash(trim($_POST['password']), PASSWORD_DEFAULT),
                'role' => $_POST['role']
            ];

            if ($this->userModel->register($data)) {
                header('Location: ' . BASE_URL . '/auth/login');
                exit;
            } else {
                die("Registration failed.");
            }
        } else {
            // ONLY load the view if it is NOT a post request
            $this->view('auth/register');
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