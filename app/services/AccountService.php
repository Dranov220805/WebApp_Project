<?php

class AccountService{
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
            return [
                'status' => true,
                'token' => $result,
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

        if ($result ) {
            return [
                'status' => true,
                'data' => $result,
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