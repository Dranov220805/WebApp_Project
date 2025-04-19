<?php

class AccountService{
    private AccountRepository $accountRepository;
    public function __construct(){
        $this->accountRepository = new AccountRepository();
    }

    public function checkLogin($account_username, $account_password): bool{
        if($this->accountRepository->checkAccountByUsernameAndPassword($account_username,$account_password)){
            $account = $this->accountRepository->getAccountByUsernameAndPassword($account_username,$account_password);
            $_SESSION['accountId'] = $account->getAccountId();
            $_SESSION['roleId'] = $this->getRoleByUsernameAndPassword($account_username,$account_password);
            return true;
        }
        return false;
    }

    public function getRoleByUsernameAndPassword($account_username, $account_password): int{
        return $this->accountRepository->getRoleByUsernameAndPassword($account_username, $account_password);
    }

    public function createAccountByUsernameAndPasswordAndEmail($account_username, $account_password, $account_email): bool{

        if($this->createAccountByUsernameAndPasswordAndEmail($account_username, $account_password, $account_email)) {
            $account = $this->accountRepository->getAccountByUsernameAndPassword($account_username, $account_password);
            $_SESSION['accountId'] = $account->getAccountId();
            $_SESSION['roleId'] = $this->getRoleByUsernameAndPassword($account_username, $account_password);
            return true;
        }
        return false;
    }
}

?>