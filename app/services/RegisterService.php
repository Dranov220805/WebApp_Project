<?php

class RegisterService {
    private AccountRepository $accountRepository;
    public function __construct() {
        $this->accountRepository = new AccountRepository();
    }
}

?>