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

                $this->authController->login();
            }
        } else {
            $this->authController->login();
        }
    }

    public function logout() {
        $this->authController->logout();
    }

    public function login_POST() {
        header('Content-Type: application/json');

        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        if (!empty($data['username']) && !empty($data['password'])) {
            $authService = new AuthService();
            $result = $authService->login($data['username'], $data['password']);

            if ($result) {
                echo json_encode([
                    'status' => true,
                    'accessToken' => $result['accessToken'],
                    'roleId' => $result['roleId'],
                    'message' => 'Đăng nhập thành công'
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    'status' => false,
                    'message' => 'Tên đăng nhập hoặc mật khẩu sai'
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => 'Thiếu thông tin đăng nhập'
            ]);
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