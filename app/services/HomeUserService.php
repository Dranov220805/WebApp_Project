<?php

class HomeUserService {
    private HomeUserRepository $homeUserRepository;
    public function __construct () {
        $this->homeUserRepository = new HomeUserRepository();
    }
    public function test() :void {

    }

    public function getLabelByAccountId($accountId) {
        return $this->homeUserRepository->getLabelByAccountId($accountId);
    }

    public function getLabelNotesByAccountIdAndLabelName($accountId, $labelName) {
        return $this->homeUserRepository->getLabelNotesByAccountIdAndLabelName($accountId, $labelName);
    }

}