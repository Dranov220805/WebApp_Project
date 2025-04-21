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
        $_SESSION['isVerified'] = $user->getIsVerified();

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

    public function checkVerification($email) {
        $result = $this->accountRepository->getAccountByEmail($email);

        if ($result->getIsVerified()) {
            return [
                'status' => true,
                'message' => 'Account is verified'
            ];
        }

        return [
            'status' => false,
            'message' => 'Account is not verified'
        ];

    }

}

?>