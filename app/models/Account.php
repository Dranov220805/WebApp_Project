<?php

class Account{
    private string $account_id;
    private string $account_name;
    private string $account_password;
    private string $account_email;
    private string $account_role;

    public function __construct($account_id, $account_name, $account_password, $account_email, $account_role){
        $this->account_id = $account_id;
        $this->account_name = $account_name;
        $this->account_password = $account_password;
        $this->account_email = $account_email;
        $this->account_role = $account_role;
    }

    public function getAccountId() {
        return $this->account_id;
    }

    public function setAccountId($account_id) {
        $this->account_id = $account_id;
    }

    public function getAccountName() {
        return $this->account_name;
    }

    public function setAccountName($account_name) {
        $this->account_name = $account_name;
    }

    public function getAccountPassword() {
        return $this->account_password;
    }

    public function setAccountPassword($account_password) {
        $this->account_password = $account_password;
    }

    public function getAccountRole() {
        return $this->account_role;
    }

    public function setAccountRole($account_role) {
        $this->account_role = $account_role;
    }

    public function getAccountEmail() {
        return $this->account_email;
    }

    public function setAccountEmail($account_email) {
        $this->account_email = $account_email;
    }
}

?>