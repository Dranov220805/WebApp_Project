<?php

class AuthService
{
    private AccountRepository $accountRepository;
    private AccountService $accountService;

    public function __construct()
    {
        $this->accountRepository = new AccountRepository();
        $this->accountService = new AccountService();
    }

    public function login($email, $password)
    {
        $user = $this->accountRepository->getAccountByEmail($email);

        if (!$user || !$this->accountService->checkLogin($email, $password)) {
            return [
                'status' => false,
                'message' => 'Login failed'
            ];
        }

        // Store all required user data in session
        $_SESSION['accountId'] = $user->getAccountId();
        $_SESSION['userName'] = $user->getUsername();
        $_SESSION['email'] = $user->getEmail();
        $_SESSION['roleId'] = $user->getRoleId();
        $_SESSION['last_activity'] = time(); // Track activity for inactivity logout

        return [
            'status' => true,
            'roleId' => $user->getRoleId(),
            'userName' => $user->getUsername(),
            'email' => $user->getEmail(),
            'last_activity' => $_SESSION['last_activity'],
            'message' => 'Login successfully'
        ];
    }

    // Called to renew session if user is active
    public function refreshSession()
    {
        if (!isset($_SESSION['userId'])) {
            http_response_code(401);
            echo json_encode(['message' => 'User not logged in']);
            return;
        }

        // Set timeout (30 minutes)
        $timeout = 3 * 60;

        // Check inactivity timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
            session_unset();
            session_destroy();
            http_response_code(401);
            echo json_encode(['message' => 'Session expired due to inactivity']);
            return;
        }

        // Renew session activity
        $_SESSION['last_activity'] = time();

        // Optional: return some data to frontend
        echo json_encode([
            'status' => true,
            'message' => 'Session renewed',
            'userName' => $_SESSION['userName']
        ]);
    }
}


//use Repository\RefreshToken;
//
//class AuthService {
//    private AccountRepository $accountRepository;
//    private AccountService $accountService;
//
//    public function __construct() {
//        $this->accountRepository = new AccountRepository();
//        $this->accountService = new AccountService();
//    }
//
//    public function login($email, $password) {
//        // Try to get the account by username and password
//        $user = $this->accountRepository->getAccountByEmail($email);
//
//        // If no account is found, return false
////        if (!$user) {
////            return [
////                'status' => false,
////                'message' => 'Account not found'
////            ];
////        }
//
//        if (!$this->accountService->checkLogin($email, $password)) {
//            return [
//                'status' => false,
//                'message' => 'Login failed'
//            ];
//        }
//
//        // Set session role
//        $_SESSION['roleId'] = $user->getRoleId();
//        $_SESSION['userName'] = $user->getUsername();
//        $_SESSION['email'] = $user->getEmail();
//
//        // Generate access token
//        $jwtHandler = new JWTHandler();
//        $accessToken = $jwtHandler->generateAccessToken([
//            'id' => $user->getAccountId(),
//            'username' => $user->getUsername()
//        ]);
//
//        $rawToken = bin2hex(random_bytes(64));
//        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));
//        RefreshToken::create($user->getAccountId(), $rawToken, $expiresAt);
//
//        setcookie("refresh_token", $rawToken, time() + 1800, "/", "", false, true); // HttpOnly
//
//        return [
//            'status' => true,
//            'accessToken' => $accessToken,
//            'roleId' => $user->getRoleId(),
//            'userName' => $user->getUsername(),
//            'email' => $user->getEmail(),
//            'message' => 'Login successfully'
//        ];
//    }
//
//    public function refreshSession() {
//        $refreshToken = $_COOKIE['refresh_token'] ?? null;
//        error_log("Refresh token from cookie: " . ($refreshToken ?? 'none'));
//
//        if (!$refreshToken) {
//            http_response_code(401);
//            echo json_encode(['message' => 'Missing refresh token']);
//            return;
//        }
//
//        try {
//            $storedToken = RefreshToken::findValid($refreshToken);
//            error_log("Stored token found: " . json_encode($storedToken));
//
//            if (!$storedToken) {
//                http_response_code(401);
//                echo json_encode(['message' => 'Refresh token invalid or expired']);
//                return;
//            }
//
//            // Generate new access token
//            $jwtHandler = new JWTHandler();
//            $accessToken = $jwtHandler->generateAccessToken([
//                'id' => $storedToken['accountId']
//            ]);
//
//            // Update expiration and usage timestamp of the refresh token
//            $newExpires = date('Y-m-d H:i:s', strtotime('+30 minutes'));
//            RefreshToken::updateUsage($storedToken['id'], $newExpires);
//
//            // Refresh the cookie with the same token, just to reset client expiry
//            setcookie("refresh_token", $refreshToken, time() + 1800, "/", "", false, true); // Optional refresh
//
//            // Return new access token
//            header('Content-Type: application/json');
//            echo json_encode(['accessToken' => $accessToken]);
//
//        } catch (\Exception $e) {
//            http_response_code(500);
//            echo json_encode(['message' => 'Internal Server Error']);
//            error_log("Refresh session error: " . $e->getMessage());
//        }
//    }
//}
//
//?>