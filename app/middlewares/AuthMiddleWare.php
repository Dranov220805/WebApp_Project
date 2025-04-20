<?php

class AuthMiddleware
{
    private AuthController $authController;

    public function __construct()
    {
        $this->authController = new AuthController();
    }

    // Redirect to home if already logged in
    public function login()
    {
        if (isset($_SESSION['userId'])) {
            header('Location: /');
            exit;
        }
    }

    public function logout()
    {
        $this->authController->logout();
    }

    public function login_POST()
    {
        header('Content-Type: application/json');

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

    // Session-based auth check for protected routes
    public function checkSession()
    {
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
        $timeout = 3 * 60; // 30 minutes
        $warning_time = 2 * 60; // Show warning after 25 minutes of inactivity

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

//    public function checkSession()
//    {
//        if (!isset($_SESSION['userId'])) {
//            // Unauthorized access, session doesn't exist
//            http_response_code(401);
//            exit(json_encode(['message' => 'Unauthorized']));
//        }
//
//        // Optional: check session expiration based on activity
//        $timeout = 3 * 60; // 30 minutes session timeout
//        $warning_time = 2 * 60; // Show warning in the last 5 minutes before expiry (25 mins)
//
//        if (isset($_SESSION['last_activity'])) {
//            $elapsedTime = time() - $_SESSION['last_activity'];
//            if ($elapsedTime > $timeout) {
//                // Session expired, clear session
//                session_unset();
//                session_destroy();
//
//                // Redirect to login page
//                header("Location: /login");
//                exit();
//            } elseif ($elapsedTime > $warning_time) {
//                // Show warning (via AJAX or a specific flag)
//                $_SESSION['session_warning'] = true;
//            }
//        }
//
//        // Update last activity timestamp
//        $_SESSION['last_activity'] = time();
//
//        // Optional: regenerate session ID for extra security
//        // session_regenerate_id(true);
//
//        // Return session data
//        return [
//            'userId' => $_SESSION['userId'],
//            'userName' => $_SESSION['userName'],
//            'email' => $_SESSION['email'],
//            'roleId' => $_SESSION['roleId']
//        ];
//    }

    // Session-based auth check for protected routes
//    public static function check()
//    {
//        if (!isset($_SESSION['userId'])) {
//            http_response_code(401);
//            exit(json_encode(['message' => 'Unauthorized']));
//        }
//
//        // Optional: check session expiration based on activity
//        $timeout = 30 * 60;
//        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
//            session_unset();
//            session_destroy();
//            http_response_code(401);
//            exit(json_encode(['message' => 'Session expired']));
//        }
//
//        // Update last activity timestamp
//        $_SESSION['last_activity'] = time();
//
//        return [
//            'userId' => $_SESSION['userId'],
//            'userName' => $_SESSION['userName'],
//            'email' => $_SESSION['email'],
//            'roleId' => $_SESSION['roleId']
//        ];
//    }
}


//class AuthMiddleware {
//    private AuthController $authController;
//    public function __construct()
//    {
//        $this->authController = new AuthController();
//    }
//
//    public function login() {
//        if (isset($_SESSION['accessToken'])) {
//            try {
//                $jwtHandler = new JWTHandler();
//                $decoded = $jwtHandler->decodeToken($_SESSION['accessToken']);
//
//                // Token is valid and not expired → redirect
//                header('Location: /');
//                exit;
//            } catch (Exception $e) {
//                // Token is invalid or expired → clear session and show login
//                unset($_SESSION['accessToken']);
//                unset($_SESSION['roleId']);
//                session_destroy();
//
//                header('Location: /');
//            }
//        } else {
//            header('Location: /');
//        }
//    }
//
//    public function logout() {
//        $this->authController->logout();
//    }
//
//    public function login_POST() {
//        header('Content-Type: application/json');
//
//        $content = trim(file_get_contents("php://input"));
//        $data = json_decode($content, true);
//
//        if (!empty($data['email']) && !empty($data['password'])) {
//            $authService = new AuthService();
//            $result = $authService->login($data['email'], $data['password']);
//
//            if ($result['status'] === true) {
//                echo json_encode([
//                    'status' => true,
//                    'roleId' => $result['roleId'],
//                    'userName' => $result['userName'],
//                    'email' => $result['email'],
//                    'message' => $result['message']
//                ]);
//            } else {
//                http_response_code(401);
//                echo json_encode([
//                    'status' => false,
//                    'message' => 'Wrong username or password'
//                ]);
//            }
//        } else {
//            http_response_code(400);
//            echo json_encode([
//                'status' => false,
//                'message' => 'Missing login credentials'
//            ]);
//        }
//    }
//
//    public function tokenRefresh() {
//        header('Content-Type: application/json');
//
//        $content = trim(file_get_contents("php://input"));
//        $data = json_decode($content, true);
//
//        if (!empty($data['token'])) {
//
//        }
//    }
//
//    // Middleware check for routes needing auth
//    public static function check() {
//        // Fallback for apache_request_headers()
//        $headers = function_exists('apache_request_headers')
//            ? apache_request_headers()
//            : getallheaders();
//
//        if (!isset($headers['Authorization'])) {
//            http_response_code(401);
//            exit(json_encode(['message' => 'Missing token']));
//        }
//
//        $jwt = str_replace('Bearer ', '', $headers['Authorization']);
//
//        try {
//            $data = (new JWTHandler())->decodeToken($jwt);
//            return $data->data; // Contains user info
//        } catch (Exception $e) {
//            http_response_code(401);
//            exit(json_encode(['message' => 'Token expired or invalid']));
//        }
//    }
//}
//
//?>