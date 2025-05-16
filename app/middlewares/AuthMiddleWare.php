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

<<<<<<< Updated upstream
    // Session-based auth check for protected routes
    public function checkSession()
    {
        $jwt = $_COOKIE['access_token'] ?? null;

        if (!$jwt) {
            return $this->tryRefresh(); // No token, try to refresh
        }

        try {
            $decoded = JWT::decode($jwt, new Key($this->jwtSecret, 'HS256'));

            // Token is expired
            if (isset($decoded->exp) && $decoded->exp < time()) {
                return $this->tryRefresh(); // Attempt refresh
            }
=======

    public function checkSession() {
        if (isset($_SESSION['accountId'])) {

            $user = [
                'accountId' => $_SESSION['accountId'],
                'userName' => $_SESSION['userName'],
                'email' => $_SESSION['email'],
                'profilePicture' => $_SESSION['profilePicture'],
                'refreshToken' => $_SESSION['refreshToken'],
                'expiredTime' => $_SESSION['expiredTime'],
                'roleId' => $_SESSION['roleId'],
                'isDarkTheme' => $_SESSION['isDarkTheme'],
                'isVerified' => $_SESSION['isVerified']
            ];
>>>>>>> Stashed changes

            $GLOBALS['user'] = $decoded->data;

            return [
                'status' => true,
<<<<<<< Updated upstream
                'token_data' => $decoded
            ];

        } catch (Exception $e) {
            // Token is invalid → try refresh
            return $this->tryRefresh();
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

        $newAccessToken = $newTokenResult['access_token'];

        // Set new access token for next requests
        setcookie('access_token', $newAccessToken, [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        // Decode token right away so current request has access
        try {
            $decoded = JWT::decode($newAccessToken, new Key($this->jwtSecret, 'HS256'));
            $GLOBALS['user'] = $decoded->data;

            return [
                'status' => true,
                'token_data' => $decoded
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to decode refreshed token'
=======
                'user' => $user,
                'message' => 'Session found'
>>>>>>> Stashed changes
            ];
        }
        return [
            'status' => false,
            'message' => 'No session found'
        ];
    }

<<<<<<< Updated upstream
    public function getUrlActivationLink()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode([
                'status' => false,
                'message' => 'Method not allowed'
            ]);
=======
//    public function checkSession() {
//        $jwt = $_COOKIE['access_token'] ?? null;
//
//        if (!$jwt) {
//            return $this->tryRefresh();
//        }
//
//        try {
//            $decoded = JWT::decode($jwt, new Key($this->jwtSecret, 'HS256'));
//
//            if (isset($decoded->exp) && $decoded->exp < time()) {
//                return $this->tryRefresh();
//            }
//
//            return [
//                'status' => true,
//                'user' => $decoded->data,
//                'message' => null,
//            ];
//        } catch (Exception $e) {
//            return $this->tryRefresh();
//        }
//    }
//
//    private function tryRefresh() {
//        $refreshToken = $_COOKIE['refresh_token'] ?? null;
//        if (!$refreshToken) {
//            return [
//                'status' => false,
//                'user' => null,
//                'message' => 'No refresh token'
//            ];
//        }
//
//        $newTokenResult = $this->authService->refreshAccessToken($refreshToken);
//
//        if (!$newTokenResult['status']) {
//            return [
//                'status' => false,
//                'user' => null,
//                'message' => 'Invalid refresh token'
//            ];
//        }
//
//        $newAccessToken = $newTokenResult['access_token'];
//
//        $this->setAccessTokenCookie($newAccessToken);
//
//        try {
//            $decoded = JWT::decode($newAccessToken, new Key($this->jwtSecret, 'HS256'));
//
//            return [
//                'status' => true,
//                'user' => $decoded->data,
//                'message' => null
//            ];
//        } catch (Exception $e) {
//            return [
//                'status' => false,
//                'user' => null,
//                'message' => 'Failed to decode refreshed token'
//            ];
//        }
//    }
//
//    private function setAccessTokenCookie(string $token) {
//        setcookie('access_token', $token, [
//            'expires' => time() + 3600,
//            'path' => '/',
//            'secure' => true,
//            'httponly' => true,
//            'samesite' => 'None',
//        ]);
//    }

    public function getUrlActivationLink() {
        $token = $_GET['token'] ?? null;
        if (empty($token)) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Token not provided']);
>>>>>>> Stashed changes
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

    public function checkVerification() {
        $checkToken = $this->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->authController->checkVerification();
    }
}
