<?php

class HomeUserService {
    private HomeUserRepository $homeUserRepository;
    private AccountRepository $accountRepository;
    public function __construct () {
        $this->homeUserRepository = new HomeUserRepository();
        $this->accountRepository = new AccountRepository();
    }
    public function test() :void {

    }

    public function getLabelByAccountId($accountId) {
        return $this->homeUserRepository->getLabelByAccountId($accountId);
    }

    public function getLabelNotesByAccountIdAndLabelName($accountId, $labelName) {
        return $this->homeUserRepository->getLabelNotesByAccountIdAndLabelName($accountId, $labelName);
    }

    public function addSharedEmailByNoteIdAndEmailAndNewEmail($noteId, $email, $newEmail) {
        $checkEmailExist = $this->accountRepository->getAccountByEmail($email);
        $checkEmail = $checkEmailExist->getEmail();
        if ($checkEmail === $newEmail) {
            return [
                'status' => false,
                'message' => 'Cannot share note to yourself'
            ];
        }
        if (!empty($checkEmailExist)) {
            $result = $this->homeUserRepository->addSharedEmailByNoteIdAndEmailAndNewEmail($noteId, $email, $newEmail);
            return [
                'status' => true,
                'data' => $result,
                'message' => 'Shared this note successfully'
            ];
        }
        return [
            'status' => false,
            'message' => 'Email is not exist'
        ];
    }

    public function getSharedEmailByNoteIdAndEmail($noteId, $email) {
        return $this->homeUserRepository->getSharedEmailByNoteIdAndEmail($noteId, $email);
    }

}