<?php
use Repository\RefreshToken;

class RegisterService {
    private AccountRepository $accountRepository;

    public function __construct() {
        $this->accountRepository = new AccountRepository();
    }

    public function register($username, $password, $email) {
        // Try to get the account by username and password
        $isEmailExist = $this->accountRepository->getAccountByEmail($email);
        if ($isEmailExist) {
            return [
                "message" => "Email already exists"
            ];
        }

        $userReg = $this->accountRepository->createAccountByUsernameAndPasswordAndEmail($username, $password, $email);

        // If no account is found, return false
        if (!$userReg) {
            return [
                'status' => false,
            ];
        }

        $user = $this->accountRepository->getAccountByUsernameAndPassword($username, $password);

        // Set session role
        $_SESSION['roleId'] = $user->getRoleId();
        $_SESSION['userName'] = $user->getUsername();
        $_SESSION['email'] = $user->getEmail();

        // Generate access token
        $jwtHandler = new JWTHandler();
        $accessToken = $jwtHandler->generateAccessToken([
            'id' => $user->getAccountId(),
            'username' => $user->getUsername()
        ]);

        $rawToken = bin2hex(random_bytes(64));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        RefreshToken::create($user->getAccountId(), $rawToken, $expiresAt);

        setcookie("refresh_token", $rawToken, time() + 1800, "/", "", false, true); // HttpOnly

        // Generate refresh token and store it
//        $refreshToken = bin2hex(random_bytes(64));
//        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));
//        $refreshToken = Models\RefreshToken::create($user->getAccountId(), $refreshToken, $expiresAt);
//
//        // Set the refresh token as a cookie
//        setcookie("refresh_token", $refreshToken, time() + 1800, "/", "", false, true); // HttpOnly

        // Return access token and role ID
        return [
            'accessToken' => $accessToken,
            'roleId' => $user->getRoleId(),
            'userName' => $user->getUsername(),
            'email' => $user->getEmail()
        ];
    }
}

?>