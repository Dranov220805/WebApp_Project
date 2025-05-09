<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    private string $jwtSecret = 'your_secret_key';
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
        $jwt = $_COOKIE['access_token'] ?? null;

        if (!$jwt) {
            return $this->tryRefresh();
        }

        try {
            $decoded = JWT::decode($jwt, new Key($this->jwtSecret, 'HS256'));

            if (isset($decoded->exp) && $decoded->exp < time()) {
                return $this->tryRefresh(); // Token expired → try refresh
            }

            $GLOBALS['user'] = $decoded->data;

            return [
                'status' => true,
                'token_data' => $decoded
            ];

        } catch (Exception $e) {
            return $this->tryRefresh(); // Token invalid → try refresh
        }
    }

    private function tryRefresh()
    {
        $refreshToken = $_COOKIE['refresh_token'] ?? null;
        if (!$refreshToken) {
            return [
                'status' => false,
                'message' => 'No refresh token'
            ];
        }

        $authService = new AuthService();
        $newTokenResult = $authService->refreshAccessToken($refreshToken);

        if (!$newTokenResult['status']) {
            return [
                'status' => false,
                'message' => 'Invalid refresh token'
            ];
        }

        // Set new access token cookie
        setcookie('access_token', $newTokenResult['access_token'], [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        $GLOBALS['user'] = $newTokenResult['data'];

        return ['status' => true, 'token_data' => $newTokenResult['data']];
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
        $checkToken = $this->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->authController->changePassword();
    }
}
