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
        return $this->accountRepository->getPreferencesByAccountId($accountId);
    }

}

?>