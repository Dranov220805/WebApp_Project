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

    public function updateProfilePictureByAccountIdAndEmail($account_id, $email, $uploadImage) {
        $result = $this->accountRepository->updateProfilePictureByAccountId($account_id, $uploadImage);
        if (!$result['status'] === false) {
<<<<<<< Updated upstream
=======
<<<<<<< Updated upstream
<<<<<<< Updated upstream
            $userData = $GLOBALS['user'];
            $email = $userData->email;
>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
=======
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
>>>>>>> Stashed changes
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

    public function getPreferencesByAccountId($accountId) {
        return $this->accountRepository->getPreferencesByAccountId($accountId);
    }

    public function updatePreferenceByAccountId($accountId, $userName, $theme, $fontSize, $noteColor) {
        $result = $this->accountRepository->updatePreferenceByAccountId($accountId, $userName, $theme, $fontSize, $noteColor);

        if ($result ) { // assuming $result is a Preference object on success
            try {
                $user = $this->accountRepository->getAccountByAccountId($accountId);
                $userPreference = $this->accountRepository->getPreferencesByAccountId($accountId);
            }
            catch (\Exception $e) {
                return [
                    'status' => false,
                    'data' => $result,
                    'message' => 'Update user preference failed'
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