<?php

class Account{
    private string $accountId;
    private string $userName;
    private string $password;
    private string $email;
    private string $roleId;
    private int $isVerified;
    private string $activation_token;
    private string $refresh_token;
    private string $expired_time;
    private string $profilePicture;

    public function __construct(string $accountId, string $userName, string $password, string $email, string $profilePicture, string $activation_token, string $refresh_token, string $expired_time, string $roleId, int $isVerified){
        $this->accountId = $accountId;
        $this->userName = $userName;
        $this->password = $password;
        $this->email = $email;
        $this->profilePicture = $profilePicture;
        $this->activation_token = $activation_token;
        $this->refresh_token = $refresh_token;
        $this->expired_time = $expired_time;
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


    public function getActivationToken(): string
    {
        return $this->activation_token;
    }

    public function setActivationToken(string $activation_token): void
    {
        $this->activation_token = $activation_token;
    }

    public function getRefreshToken(): string
    {
        return $this->refresh_token;
    }

    public function setRefreshToken(string $refresh_token): void
    {
        $this->refresh_token = $refresh_token;
    }

    public function getExpiredTime(): string
    {
        return $this->expired_time;
    }

    public function setExpiredTime(string $expired_time): void
    {
        $this->expired_time = $expired_time;
    }

    public function getProfilePicture(): string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(string $profilePicture): void
    {
        $this->profilePicture = $profilePicture;
    }

}
?>