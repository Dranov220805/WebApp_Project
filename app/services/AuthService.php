<?php

use Firebase\JWT\JWT;

class AuthService
{
    private string $jwtSecret = 'your_secret_key';
    private int $jwtExpiry = 3600; // Token expiry in seconds (e.g., 1 hour)
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
                'message' => 'Wrong username or password'
            ];
        }

        $accountId = $user->getAccountId();
        $userPreference = $this->accountRepository->getPreferencesByAccountId($accountId);

        if (!$userPreference) {
            return [
                'status' => false,
                'message' => 'Preferences not found'
            ];
        }

        // Generate JWT
        $payload = [
            'iss' => 'your_issuer', // Issuer
            'aud' => 'your_audience', // Audience
            'iat' => time(), // Issued at
            'exp' => time() + $this->jwtExpiry, // Expiry
            'data' => [
                'accountId' => $user->getAccountId(),
                'userName' => $user->getUsername(),
                'email' => $user->getEmail(),
                'profilePicture' => $user->getProfilePicture(),
                'refreshToken' => $user->getRefreshToken(),
                'expiredTime' => $user->getExpiredTime(),
                'roleId' => $user->getRoleId(),
                'isDarkTheme' => $userPreference->isDarkTheme(),
                'isVerified' => $user->getIsVerified()
            ]
        ];

        $jwt = JWT::encode($payload, $this->jwtSecret, 'HS256');

        return [
            'status' => true,
            'token' => $jwt,
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

    public function accountActivate($activation_token) {
        return $this->accountRepository->activateAccountByActivationToken($activation_token);
    }

    public function resetPasswordByEmail($resetEmail) {

        $result = $this->accountRepository->updateAccountPasswordByEmail($resetEmail);

        if (!$result) {
            return [
                'status' => false,
                'message' => 'Reset password failed'
            ];
        }

        $newPassword = $result;

        if (!sendRessetPasswordEmail($resetEmail, $newPassword)) {
            return [
                'status' => false,
                'message' => 'Send new password failed'
            ];
        }

        return [
            'status' => true,
            'message' => 'Email to reset password has been sent'
        ];
    }

    public function updatePasswordByEmail($email, $newPassword) {
        $result = $this->accountRepository->updatePasswordByEmail($email, $newPassword);

        if (!$result) {
            return [
                'status' => false,
                'message' => 'Update password failed'
            ];
        } else {
            return [
                'status' => true,
                'message' => 'Password has been updated'
            ];
        }
    }

}

?>