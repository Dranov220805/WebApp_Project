<?php

class AuthService
{
    private int $jwtExpiry = 3600; // Token expiry in seconds (1 hour)
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

        $refreshToken = bin2hex(random_bytes(16));
        $refreshTokenExpiry = date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60));

        $this->accountRepository->saveRefreshToken($accountId, $refreshToken, $refreshTokenExpiry);

        return [
            'status' => true,
            'accountId' => $user->getAccountId(),
            'userName' => $user->getUsername(),
            'email' => $user->getEmail(),
            'profilePicture' => $user->getProfilePicture(),
            'refreshToken' => $user->getRefreshToken(),
            'expiredTime' => $user->getExpiredTime(),
            'roleId' => $user->getRoleId(),
            'isDarkTheme' => $userPreference->isDarkTheme(),
            'isVerified' => $user->getIsVerified(),
            'message' => 'Login successfully'
        ];
    }
    
    // Called to renew session if user is active
    public function refreshAccessToken($refreshToken)
    {
        $user = $this->accountRepository->getAccountByRefreshToken($refreshToken);
        $userPreference = $this->accountRepository->getPreferencesByAccountId($user->getAccountId());

        if (!$user || time() > $user->getExpiredTime()) {
            return ['status' => false];
        }

        // Generate new access token
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600,
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

        $newJwt = JWT::encode($payload, $this->jwtSecret, 'HS256');

        return [
            'status' => true,
            'access_token' => $newJwt,
            'data' => $payload['data']
        ];
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

    public function checkVerification($email) {
        $result = $this->accountRepository->getAccountByEmail($email);

        if ($result->getIsVerified()) {
            $_SESSION['isVerified'] = true;
            return [
                'status' => true,
                'message' => 'Account is verified'
            ];
        } else {
            $_SESSION['isVerified'] = false;
            return [
                'status' => false,
                'message' => 'Account is not verified'
            ];
        }
    }

}

?>