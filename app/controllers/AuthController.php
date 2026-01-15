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
