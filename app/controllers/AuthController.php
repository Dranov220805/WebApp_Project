<?php

class AuthController {
    private AuthService $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function index() {
        include "./views/log/login.php";
    }

    public function login_POST() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        if (!empty($data['email']) && !empty($data['password'])) {
            $authService = new AuthService();
            $result = $authService->login($data['email'], $data['password']);

            if ($result['status'] === true) {
                echo json_encode([
                    'status' => true,
                    'roleId' => $result['roleId'],
                    'userName' => $result['userName'],
                    'email' => $result['email'],
                    'last_activity' => $result['last_activity'],
                    'message' => $result['message']
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    'status' => false,
                    'message' => $result['message']
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => 'Missing login credentials'
            ]);
        }
    }

    public function checkVerification() {
        $email = $_SESSION['email'];

        $result = $this->authService->checkVerification($email);

        if($result['status'] === true) {
            return [
                'status' => true,
                'message' => $result['message']
            ];
        } else {
            return [
                'status' => false,
                'message' => $result['message']
            ];
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
    }
}

?>
