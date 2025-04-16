<?php

class HomeUserService {
    private HomeUserRepository $homeUserRepository;
    public function __construct () {
        $this->homeUserRepository = new HomeUserRepository();
    }
    public function test() :void {

    }

}