<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    private string $jwtSecret = 'your_secret_key'; // Replace with the same key used in AuthService
    private AuthController $authController;

    public function __construct()
    {
        $this->authController = new AuthController();
    }

    // Redirect to home if already logged in
    public function index()
    {
        $checkToken = $this->checkSession();

        if ($checkToken['status'] === true) {
            header('location:/');
            exit();
        }
        $this->authController->index();
    }

    public function redirect()
    {
        header('Location: /home');
    }

    public function logout()
    {
        $this->authController->logout();
    }

    public function login_POST()
    {
        $this->authController->login_POST();
    }

    // Session-based auth check for protected routes
    public function checkSession()
    {
        $jwt = null;

        // 1. Check Authorization header
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? null;

        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            $jwt = str_replace('Bearer ', '', $authHeader);
        } else if (isset($_COOKIE['jwt_token'])) {
            $jwt = $_COOKIE['jwt_token'];
        } else if (isset($_GET['token'])) {
            $jwt = $_GET['token'];
        }

        if (!$jwt) {
            return ['status' => false, 'message' => 'Unauthorized - No authentication token found'];
        }

        try {
            $decoded = JWT::decode($jwt, new Key($this->jwtSecret, 'HS256'));
            $GLOBALS['user'] = $decoded->data;
            return ['status' => true,
                'token_data' => $decoded
            ];
        } catch (Exception $e) {
            return ['status' => false, 'message' => 'Invalid or expired token'];
        }
    }

    public function getUrlActivationLink()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode([
                'status' => false,
                'message' => 'Method not allowed'
            ]);
            exit();
        }

        if (!isset($_GET['token']) || empty($_GET['token'])) {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => 'Token not provided'
            ]);
            exit();
        }

        $token = $_GET['token'];
        $this->authController->accountActivate($token);
    }

     public function accountActivate()
     {
         $this->authController->accountActivate();
     }

    public function forgotPassword()
    {
        $this->authController->forgotPassword();
    }

    public function resetPassword()
    {
        $this->authController->resetPassword();
    }

    public function changePassword()
    {
        $this->authController->changePassword();
    }
}
