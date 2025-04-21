<?php

class Account{
    private string $accountId;
    private string $userName;
    private string $password;
    private string $email;
    private string $roleId;
    private int $isVerified;

    public function __construct(string $accountId, string $userName, string $password, string $email, string $roleId, int $isVerified){
        $this->accountId = $accountId;
        $this->userName = $userName;
        $this->password = $password;
        $this->email = $email;
        $this->roleId = $roleId;
        $this->isVerified = $isVerified;
    }

    public function getAccountId(): string{
        return $this->accountId;
    }

    public function getUserName(): string{
        return $this->userName;
    }

    public function getPassword(): string{
        return $this->password;
    }

    public function getEmail(): string{
        return $this->email;
    }

    public function getRoleId(): string{    
        return $this->roleId;
    }

    public function setAccountId(string $accountId){
        $this->accountId = $accountId;
    }

    public function setUserName(string $userName){
        $this->userName = $userName;
    }

    public function setPassword(string $password){
        $this->password = $password;
    }

    public function setEmail(string $email){
        $this->email = $email;
    }

    public function setRoleId(string $roleId){
        $this->roleId = $roleId;
    }

    public function getIsVerified(): int
    {
        return $this->isVerified;
    }

    public function setIsVerified(int $isVerified): void
    {
        $this->isVerified = $isVerified;
    }

}
?>