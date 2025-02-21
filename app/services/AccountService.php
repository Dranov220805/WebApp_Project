<?php

class AccountService{
    private AccountRepository $accountRepository;
    public function __construct(){
        $this->accountRepository = new AccountRepository();
    }

    public function checkLogin($account_username, $account_password): bool{
        if($this->accountRepository->checkAccountByUsernameAndPassword($account_username,$account_password)){
            $account = $this->accountRepository->getAccountByUsernameAndPassword($account_username,$account_password);
            $_SESSION['person_id'] = $account->getPersonId();
            return true;
        }
        return false;
    }

    public function getRoleByUsernameAndPassword($account_username, $account_password): string{
        return $this->accountRepository->getRoleByUsernameAndPassword($account_username, $account_password);
    }
}

?>