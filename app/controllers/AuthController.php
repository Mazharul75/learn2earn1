<?php
require_once "../app/core/Controller.php";

class AuthController extends Controller {

    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'role' => $_POST['role']
            ];
    
            if ($this->userModel->register($data)) {
                header("Location: " . BASE_URL . "/auth/login");
                exit;
            } else {
                echo "Registration failed";
            }
        }
    }    

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $this->userModel->login($email, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                header("Location: " . BASE_URL . "/dashboard");
                exit;
            } else {
                echo "Invalid email or password";
            }
        }
    }
}
