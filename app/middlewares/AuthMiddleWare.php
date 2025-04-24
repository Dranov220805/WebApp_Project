<?php

class AuthMiddleware
{
    private AuthController $authController;

    public function __construct()
    {
        $this->authController = new AuthController();
    }

    // Redirect to home if already logged in
    public function index()
    {
        if (isset($_SESSION['accountId'])) {
            header('Location: /home');
        } else {
            $this->authController->index();
        }
    }

    public function logout()
    {
        $this->authController->logout();
    }

    public function login_POST() {
        $this->authController->login_POST();
    }

    // Session-based auth check for protected routes
    public function checkSession() {
        session_start();

        // Check if user is logged in
        if (!isset($_SESSION['userId'])) {
            http_response_code(401);
            echo json_encode([
                'sessionExpired' => true,
                'message' => 'Unauthorized'
            ]);
        }

        // Timeouts (in seconds)
        $timeout = 30 * 60; // 30 minutes
        $warning_time = 25 * 60; // Show warning after 25 minutes of inactivity

        header('Content-Type: application/json');

        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        // Check last activity time
        if (isset($_SESSION['last_activity'])) {
            $elapsed = time() - $_SESSION['last_activity'];

            if ($elapsed > $timeout) {
                session_unset();
                session_destroy();
                http_response_code(401);
                echo json_encode([
                    'sessionExpired' => true,
                    'message' => 'Session expired'
                ]);
            }

            // Trigger warning flag
            if ($elapsed > $warning_time) {
                $_SESSION['session_warning'] = true;
            }
        }

        // Update activity timestamp
        $_SESSION['last_activity'] = time();

        return [
            'userId' => $_SESSION['userId'],
            'userName' => $_SESSION['userName'],
            'email' => $_SESSION['email'],
            'roleId' => $_SESSION['roleId'],
            'last_activity' => $_SESSION['last_activity']
        ];
    }

    public function checkVerification() {
        if (!isset($_SESSION['userId'])) {
            http_response_code(401);
            echo json_encode([
                'status' => false,
                'message' => 'Unauthorized'
            ]);
        }

        $this->authController->checkVerification();
    }


    public function getUrlActivationLink() {
        if (!$_SERVER['REQUEST_METHOD'] == 'GET') {
            http_response_code(405);
            echo json_encode([
                'status' => false,
                'message' => 'Method not allowed'
            ]);
            exit();
        } else {
            if (!isset($_GET['token']) && empty($_GET['token'])) {
                http_response_code(400);
                echo json_encode([
                    'status' => false,
                    'message' => 'Token not provided'
                ]);
            }
            $token = $_GET['token'];

            $this->authController->accountActivate($token);
        }
    }

    public function accountActivate() {
        $this->authController->accountActivate();
    }

    public function forgotPassword() {
        $this->authController->forgotPassword();
    }

    public function resetPassword() {
//        echo json_encode([
//            'status' => true,
//            'message' => 'OK'
//        ]);
        $this->authController->resetPassword();
    }

}
?>