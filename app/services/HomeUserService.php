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
        // Prevent sharing to yourself
        if ($email === $newEmail) {
            return [
                'status' => false,
                'message' => 'Cannot share note to yourself'
            ];
        }

        // Check if target email exists
        $targetAccount = $this->accountRepository->getAccountByEmail($newEmail);

        if ($targetAccount === null) {
            return [
                'status' => false,
                'message' => 'Target email does not exist in the system'
            ];
        }

        // Check if the note has already been shared to this account
        $alreadyShared = $this->homeUserRepository->isNoteAlreadySharedTo($noteId, $email);
        if ($alreadyShared) {
            return [
                'status' => false,
                'message' => 'This note has already been shared to this email'
            ];
        }

        // Proceed with sharing
        $result = $this->homeUserRepository->addSharedEmailByNoteIdAndEmailAndNewEmail($noteId, $email, $newEmail);

        return [
            'status' => true,
            'data' => $result,
            'message' => 'Shared this note successfully'
        ];
    }

    public function removeSharedEmailByNoteIdAndEmail($noteId, $email) {
        $result = $this->homeUserRepository->removeSharedEmailByNoteIdAndEmail($noteId, $email);
        if ($result) {
            return [
                'status' => true,
                'data' => $result,
                'message' => 'Remove this share successfully'
            ];
        } else {
            return [
                'status' => false,
                'data' => $result,
                'message' => 'Cannot remove this share'
            ];
        }
    }

    public function updatePermissionByNoteIdAndEmail($noteId, $email, $canEdit) {
        $result = $this->homeUserRepository->updatePermissionByNoteIdAndEmail($noteId, $email, $canEdit);
        if ($result) {
            return [
                'status' => true,
                'data' => $result,
                'message' => 'Update permission successfully'
            ];
        } else {
            return [
                'status' => false,
                'data' => $result,
                'message' => 'Update permission failed'
            ];
        }
    }

    public function getSharedEmailByNoteIdAndEmail($noteId, $email) {
        return $this->homeUserRepository->getSharedEmailByNoteIdAndEmail($noteId, $email);
    }

}