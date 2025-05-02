<?php

class AccountService{
    private AccountRepository $accountRepository;
    public function __construct(){
        $this->accountRepository = new AccountRepository();
    }

    public function checkLogin($account_email, $account_password): bool{
        if($this->accountRepository->checkAccountByEmailAndPassword($account_email,$account_password)){
            $account = $this->accountRepository->getAccountByEmail($account_email);
            $_SESSION['email'] = $account->getEmail();
            $_SESSION['accountId'] = $account->getAccountId();
            $_SESSION['roleId'] = $this->getRoleByEmail($_SESSION['email']);
            return true;
        }
        return false;
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
            return [
                'status' => true,
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

        if ($result && is_array($result) === false) { // assuming $result is a Preference object on success
            // Overwrite session isDarkTheme value
            $_SESSION['isDarkTheme'] = ($theme === 'dark') ? 1 : 0;

            return [
                'status' => true,
                'data' => $result,
                'message' => 'Update user preference successfully'
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Update user preference failed'
            ];
        }
    }

}

?>