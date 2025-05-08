<?php

use Firebase\JWT\JWT;

class AccountService{
    private string $jwtSecret = 'your_secret_key';
    private int $jwtExpiry = 3600;
    private AccountRepository $accountRepository;
    public function __construct(){
        $this->accountRepository = new AccountRepository();
    }

    public function checkLogin($account_email, $account_password) {
        if($this->accountRepository->checkAccountByEmailAndPassword($account_email,$account_password)){
            return $this->accountRepository->getAccountByEmail($account_email);
        }
        return null;
    }

    public function getRoleByEmail($email){
        return $this->accountRepository->getRoleByEmail($email);
    }

    public function getRoleByUsernameAndPassword($account_username, $account_password): int{
        return $this->accountRepository->getRoleByUsernameAndPassword($account_username, $account_password);
    }

    public function updateProfilePictureByAccountId($account_id, $uploadImage) {
        $result = $this->accountRepository->updateProfilePictureByAccountId($account_id, $uploadImage);
        if (!$result['status'] === false) {
            $userData = $GLOBALS['user'];
            $email = $userData->email;
            $user = $this->accountRepository->getAccountByEmail($email);
            $accountId = $user->getAccountId();
            $userPreference = $this->accountRepository->getPreferencesByAccountId($accountId);

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
                'message' => 'Profile picture updated successfully'
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Failed to update profile picture'
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

    public function getPreferencesByAccountId($accountId) {
        return $this->accountRepository->getPreferencesByAccountId($accountId);
    }

    public function updatePreferenceByAccountId($account_id, $theme, $fontSize, $noteColor) {
        $result = $this->accountRepository->updatePreferenceByAccountId($account_id, $theme, $fontSize, $noteColor);

        if ($result ) { // assuming $result is a Preference object on success
            $userData = $GLOBALS["user"];
            $email = $userData->email;
            $user = $this->accountRepository->getAccountByEmail($email);

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
                    'isDarkTheme' => $theme == 'dark',
                    'isVerified' => $user->getIsVerified()
                ]
            ];

            $jwt = JWT::encode($payload, $this->jwtSecret, 'HS256');

            return [
                'status' => true,
                'token' => $jwt,
                'message' => 'Update user preference successfully'
            ];
        } else {
            return [
                'status' => false,
                'data' => $result,
                'message' => 'Update user preference failed'
            ];
        }
    }

}

?>