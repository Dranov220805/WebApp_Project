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

    public function getPreferencesByAccountId($accountId) {
        return $this->accountRepository->getPreferencesByAccountId($accountId, [
            'status' => true,
            'preferenceId' => $row['preferenceId'],
            'accountId' => $row['accountId'],
            'layout' => $row['layout'],
            'noteFont' => $row['noteFont'],
            'noteColor' => $row['noteColor'],
            'font' => $row['font'],
            'isDarkTheme' => $row['isDarkTheme'],
            'message' => 'Account preferences found',
        ]);
    }

    public function updatePreferenceByAccountId($account_id, $theme, $fontSize, $noteColor){
        $result = $this->accountRepository->updatePreferenceByAccountId($account_id, $theme, $fontSize, $noteColor);

        if ($result) {
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