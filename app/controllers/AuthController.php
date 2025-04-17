<?php

class AuthController {
    private AuthService $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function login() {
        header('Content-Type: application/json');

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $result = $this->authService->login($username, $password);

        if ($result) {
            echo json_encode([
                'status' => true,
                'accessToken' => $result['accessToken'],
                'roleId' => $result['roleId'],
                'userName' => $result['userName'],
                'email' => $result['email'],
                'message' => 'Đăng nhập thành công'
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                'status' => false,
                'message' => 'Tên đăng nhập hoặc mật khẩu sai'
            ]);
        }
    }

    public function logout() {
        $_SESSION = []; // Clear session variables

        session_destroy(); // Fully destroy the session

        // Perform the redirect
        header('Location: /'); // Make sure no output is sent before this
        exit();
    }

    public function refreshToken() {
        $this->authService->refreshSession();
    }

    public function register() {
        header('Content-Type: application/json');

    }
}

?>
