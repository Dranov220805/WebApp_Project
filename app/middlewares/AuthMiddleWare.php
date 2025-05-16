<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    private string $jwtSecret = 'your_secret_key';
    private AuthController $authController;
    private AuthService $authService;

    public function __construct() {
        $this->authController = new AuthController();
        $this->authService = new AuthService();
    }

    public function index() {
        $check = $this->checkSession();

        if ($check['status']) {
            header('Location: /');
            exit();
        }

        $this->authController->index();
    }

    public function redirect() {
        header('Location: /home');
        exit();
    }

    public function logout() {
        $this->authController->logout();
    }

    public function login_POST() {
        $this->authController->login_POST();
    }

<<<<<<< Updated upstream
    public function checkSession(): array
=======
<<<<<<< Updated upstream
<<<<<<< Updated upstream
    // Session-based auth check for protected routes
    public function checkSession()
>>>>>>> Stashed changes
    {
        $jwt = $_COOKIE['access_token'] ?? null;

        if (!$jwt) {
            return $this->tryRefresh();
        }

        try {
            $decoded = JWT::decode($jwt, new Key($this->jwtSecret, 'HS256'));

            if (isset($decoded->exp) && $decoded->exp < time()) {
                // Token expired, try refresh
                return $this->tryRefresh();
            }
<<<<<<< Updated upstream
=======
=======

    public function checkSession() {
        if (isset($_SESSION['accountId'])) {

=======

    public function checkSession() {
        if (isset($_SESSION['accountId'])) {

>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
>>>>>>> Stashed changes

            $GLOBALS['user'] = $decoded->data;
>>>>>>> Stashed changes

            return [
                'status' => true,
                'user' => $decoded->data,
                'message' => null
            ];
        } catch (Exception $e) {
            // Token invalid or decode error, try refresh
            return $this->tryRefresh();
        }
    }

    private function tryRefresh(): array
    {
        $refreshToken = $_COOKIE['refresh_token'] ?? null;

        if (!$refreshToken) {
            return [
                'status' => false,
                'user' => null,
                'message' => 'No refresh token'
            ];
        }

        $authService = new AuthService();
        $newTokenResult = $authService->refreshAccessToken($refreshToken);

        if (!$newTokenResult['status']) {
            return [
                'status' => false,
                'user' => null,
                'message' => 'Invalid refresh token'
            ];
        }

        $newAccessToken = $newTokenResult['access_token'];

        setcookie('access_token', $newAccessToken, [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None'
        ]);

        try {
            $decoded = JWT::decode($newAccessToken, new Key($this->jwtSecret, 'HS256'));

            return [
                'status' => true,
                'user' => $decoded->data,
                'message' => null
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'user' => null,
                'message' => 'Failed to decode refreshed token'
<<<<<<< Updated upstream
=======
=======
                'user' => $user,
                'message' => 'Session found'
>>>>>>> Stashed changes
=======

            return [
                'status' => true,
                'user' => $user,
                'message' => 'Session found'
>>>>>>> Stashed changes
>>>>>>> Stashed changes
            ];
        }
    }

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

    private function setAccessTokenCookie(string $token) {
        setcookie('access_token', $token, [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None',
        ]);
    }

    public function getUrlActivationLink() {
        $token = $_GET['token'] ?? null;
        if (empty($token)) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Token not provided']);
            exit();
        }

        $this->authController->accountActivate($token);
    }

    public function accountActivate() {
        $this->authController->accountActivate();
    }

    public function forgotPassword() {
        $this->authController->forgotPassword();
    }

    public function resetPassword() {
        $this->authController->resetPassword();
    }

    public function changePassword() {
        $check = $this->checkSession();
        if (!$check['status']) {
            header('Location: /log/login');
            exit();
        }

        $this->authController->changePassword($check['user']);
    }

    public function checkVerification() {
        $check = $this->checkSession();
        if (!$check['status']) {
            exit();
        }
        $this->authController->checkVerification($check['user']);
    }
}
