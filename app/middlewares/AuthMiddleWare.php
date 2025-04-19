<?php

class AuthMiddleware {
    private AuthController $authController;
    public function __construct()
    {
        $this->authController = new AuthController();
    }

    public function login() {
        if (isset($_SESSION['accessToken'])) {
            try {
                $jwtHandler = new JWTHandler();
                $decoded = $jwtHandler->decodeToken($_SESSION['accessToken']);

                // Token is valid and not expired → redirect
                header('Location: /');
                exit;
            } catch (Exception $e) {
                // Token is invalid or expired → clear session and show login
                unset($_SESSION['accessToken']);
                unset($_SESSION['roleId']);
                session_destroy();

                header('Location: /');
            }
        } else {
            header('Location: /');
        }
    }

    public function logout() {
        $this->authController->logout();
    }

    public function login_POST() {
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
                    'message' => $result['message']
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    'status' => false,
                    'message' => 'Wrong username or password'
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

//    public function login_POST() {
//        header('Content-Type: application/json');
//
//        $content = trim(file_get_contents("php://input"));
//        $data = json_decode($content, true);
//
//        if (!empty($data['username']) && !empty($data['password'])) {
//            $result = $this->authController->login();
//
//            if ($result) {
//                echo json_encode([
//                    'status' => true,
//                    'accessToken' => $result['accessToken'],
//                    'roleId' => $result['roleId'],
//                    'message' => 'Login successful'
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

//    public function login_POST() {
//        header('Content-Type: application/json');
//
//        $content = trim(file_get_contents("php://input"));
//        $data = json_decode($content, true);
//
//        if (!empty($data['username']) && !empty($data['password'])) {
//            $result = $this->authController->login_POST();
//
//            if ($result) {
//                echo json_encode([
//                    'status' => true,
//                    'accessToken' => $result['accessToken'],
//                    'roleId' => $result['roleId'],
//                    'message' => 'Login successful'
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

    public function tokenRefresh() {
        header('Content-Type: application/json');

        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        if (!empty($data['token'])) {

        }
    }

    // Middleware check for routes needing auth
    public static function check() {
        // Fallback for apache_request_headers()
        $headers = function_exists('apache_request_headers')
            ? apache_request_headers()
            : getallheaders();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            exit(json_encode(['message' => 'Missing token']));
        }

        $jwt = str_replace('Bearer ', '', $headers['Authorization']);

        try {
            $data = (new JWTHandler())->decodeToken($jwt);
            return $data->data; // Contains user info
        } catch (Exception $e) {
            http_response_code(401);
            exit(json_encode(['message' => 'Token expired or invalid']));
        }
    }
}

?>