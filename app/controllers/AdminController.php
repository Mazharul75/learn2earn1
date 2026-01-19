<?php
require_once '../app/models/Admin.php';

class AdminController {
    private $adminModel;

    public function __construct() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        $this->adminModel = new Admin();
    }

    public function dashboard() {
        $this->loadView('admin/dashboard', ['users' => $this->adminModel->getAllUsers()]);
    }

    public function invite() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');
            
            if (empty($email)) {
                 echo "<script>alert('Email cannot be empty'); window.location.href='" . BASE_URL . "admin/dashboard';</script>";
                 return;
            }

            if ($this->adminModel->isInvited($email)) {
                echo "<script>alert('Already invited!'); window.location.href='" . BASE_URL . "admin/dashboard';</script>";
                return;
            }
            $this->adminModel->inviteAdmin($email, $_SESSION['user_id']);
            echo "<script>alert('Invitation Sent!'); window.location.href='" . BASE_URL . "admin/dashboard';</script>";
        }
    }

    public function deleteUser($id) {
        if ($id == $_SESSION['user_id']) {
            echo "<script>alert('Cannot delete yourself!'); window.location.href='" . BASE_URL . "admin/dashboard';</script>";
            exit;
        }
        $this->adminModel->deleteUser($id);
        header('Location: ' . BASE_URL . 'admin/dashboard');
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