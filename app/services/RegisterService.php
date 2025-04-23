<?php
use Repository\RefreshToken;

require "app/core/mailers/sentEmail.php";

class RegisterService {
    private AccountRepository $accountRepository;
    private AuthService $authService;

    public function __construct() {
        $this->accountRepository = new AccountRepository();
        $this->authService = new AuthService();
    }

    public function register($username, $password, $email) {
        // Try to get the account by username and password
        $isEmailExist = $this->accountRepository->getAccountByEmail($email);
        if ($isEmailExist) {
            return [
                "status" => false,
                "message" => "Email already exists"
            ];
        }

        $user = $this->accountRepository->createAccountByUsernameAndPasswordAndEmail($username, $password, $email);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'Create account failed'
            ];
        }

        // Set session role
        $_SESSION['roleId'] = $user->getRoleId();
        $_SESSION['userName'] = $user->getUsername();
        $_SESSION['email'] = $user->getEmail();

        $activation_token = bin2hex(random_bytes(16));

        if (!sendActivationEmail($user->getEmail(), $activation_token)) {
            return [
                'status' => false,
                'message' => 'Send activation email failed'
            ];
        }

        return [
            'status' => true,
            'roleId' => $user->getRoleId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'message' => 'Account created successfully'
        ];
    }
}

?>