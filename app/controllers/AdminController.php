<?php
class AdminController extends Controller {
    private $adminModel;

    public function __construct() {
        // SECURITY: Only allow Admins here
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
        $this->adminModel = $this->model('Admin');
    }

    public function dashboard() {
        $users = $this->adminModel->getAllUsers();
        $this->view('admin/dashboard', ['users' => $users]);
    }

    // Feature 2: Refer an Email (Add to whitelist)
    public function invite() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);
            
            // 1. FIX: Check if already invited BEFORE trying to insert
            if ($this->adminModel->isInvited($email)) {
                echo "<script>
                        alert('⚠️ Invitation already sent! This email is already on the whitelist.'); 
                        window.location.href='" . BASE_URL . "admin/dashboard';
                      </script>";
                return; // Stop execution here
            }

            // 2. If not invited yet, proceed to insert
            if ($this->adminModel->inviteAdmin($email, $_SESSION['user_id'])) {
                echo "<script>
                        alert('✅ Invitation Sent! The email $email can now register as Admin.'); 
                        window.location.href='" . BASE_URL . "admin/dashboard';
                      </script>";
            } else {
                 echo "<script>
                        alert('❌ Error: Could not send invitation.'); 
                        window.location.href='" . BASE_URL . "admin/dashboard';
                      </script>";
            }
        }
    }


    // Feature 3: Manage Users (Delete)
    public function deleteUser($id) {
        // Prevent deleting yourself
        if ($id == $_SESSION['user_id']) {
            echo "<script>alert('⛔️ You cannot delete yourself!'); window.location.href='" . BASE_URL . "admin/dashboard';</script>";
            exit;
        }

        if ($this->adminModel->deleteUser($id)) {
            header('Location: ' . BASE_URL . 'admin/dashboard');
        }
    }
}
