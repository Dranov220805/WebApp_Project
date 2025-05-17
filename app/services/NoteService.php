<?php

include_once "./app/core/uploadImage/cloudinary_upload.php";

class NoteService {
    private NoteRepository $noteRepository;

    public function __construct() {
        $this->noteRepository = new NoteRepository();
    }

    public function getNotesByAccountId($accountId) {
        return $this->noteRepository->getNotesByAccountId($accountId);
    }

    public function getNotesByAccountIdPaginated(string $accountId, int $page = 1, int $perPage = 10): array {
        return $this->noteRepository->getNotesByAccountIdPaginated($accountId, $page, $perPage);
    }

    public function getPinnedNotesByAccountId(string $accountId) {
        return $this->noteRepository->getPinnedNotesByAccountId($accountId);
    }

    public function getPinnedNotesByAccountIdPaginated(string $accountId, int $limit, int $offset): array {
        return $this->noteRepository->getPinnedNotesByAccountIdPaginated($accountId, $limit, $offset);
    }

    public function getTrashedNotesByAccountIdPaginated(string $accountId, int $limit, int $offset): array {
        return $this->noteRepository->getTrashedNotesByAccountIdPaginated($accountId, $limit, $offset);
    }

    public function getTrashedNotesByAccountId(string $accountId) {
        return $this->noteRepository->getTrashedNotesByAccountId($accountId);
    }

    public function getLabelNoteByLabelName(string $labelName, string $accountId) {
        return $this->noteRepository->getLabelNoteByLabelName($labelName, $accountId);
    }

    public function getSharedNoteByAccountId(string $accountId) {
        return $this->noteRepository->getSharedNoteByAccountId($accountId);
    }

    public function shareNoteBySharedAccountIdAndReceivedAccountId(string $accountId, string $sharedAccountId, string $receivedAccountId) {
        return $this->noteRepository->shareNoteBySharedAccountIdAndReceivedAccountId($accountId, $sharedAccountId, $receivedAccountId);
    }

    public function getNotesSharedByEmail(string $email) {
        return $this->noteRepository->getNotesSharedByEmail($email);
    }

    public function createNoteByAccountIdAndTitleAndContent($accountId, $title, $content)
    {
        $result = $this->noteRepository->createNoteByAccountIdAndTitleAndContent($accountId, $title, $content);

        if (!$result) {
            return [
                'status' => false,
                'message' => 'Note could not be created'
            ];
        }

        return [
            'status' => true,
            'accountId' => $result->getAccountId(),
            'noteId' => $result->getNoteId(),
            'title' => $result->getTitle(),
            'content' => $result->getContent(),
            'createDate' => $result->getCreateDate(),
            'message' => 'Note created'
        ];
    }

    public function updateNoteByAccountIdAndNoteId($accountId, $noteId, $noteTitle, $noteContent) {
        $result = $this->noteRepository->updateNoteByAccountIdAndNoteId($accountId, $noteId, $noteTitle, $noteContent);

        if (!$result) {
            return [
                'status' => false,
                'message' => 'Note could not be updated'
            ];
        }

        return [
            'status' => true,
            'accountId' => $result->getAccountId(),
            'noteId' => $result->getNoteId(),
            'noteTitle' => $result->getTitle(),
            'noteContent' => $result->getContent(),
            'modifiedDate' => $result->getCreateDate(),
            'message' => 'Note updated'
        ];
    }

    public function searchNotesByAccountId(string $accountId, string $searchTerm): array
    {
        return $this->noteRepository->searchNotesByAccountId($accountId, $searchTerm);
    }

    public function deleteNoteByAccountIdAndNoteId($accountId, $noteId) {
        // Call the repository method to delete the note
        $result = $this->noteRepository->deleteNoteByAccountIdAndNoteId($accountId, $noteId);

        // Check if the result is null or false, indicating failure
        if (!$result) {
            return [
                'status' => false,
                'message' => 'Note could not be deleted'
            ];
        }

        // If the deletion was successful, return a success message
        return [
            'status' => true,
            'message' => 'Note deleted successfully'
        ];
    }

    public function pinNoteByNoteId($noteId) {
        $result = $this->noteRepository->pinNoteByNoteId($noteId);

        if (!$result) {
            return [
                'status' => false,
                'message' => 'Note could not be pinned'
            ];
        } else {
            return [
                'status' => true,
                'message' => 'Pinned note successfully'
            ];
        }
    }

    public function unpinNoteByNoteId($noteId) {
        $result = $this->noteRepository->unpinNoteByNoteId($noteId);

        if (!$result) {
            return [
                'status' => false,
                'message' => 'Note could not be unpinned'
            ];
        } else {
            return [
                'status' => true,
                'message' => 'Unpinned note successfully'
            ];
        }
    }

    public function restoreNoteByAccountIdAndNoteId($accountId, $noteId) {
        // Call the repository method to restore the note
        $result = $this->noteRepository->restoreNoteByAccountIdAndNoteId($accountId, $noteId);

        // Check if the result is null or false, indicating failure
        if (!$result) {
            return [
                'status' => false,
                'message' => 'Note could not be restored'
            ];
        }

        // If the restoration was successful, return a success message
        return [
            'status' => true,
            'message' => 'Note restored successfully'
        ];
    }

    public function hardDeleteNoteByAccountIdAndNoteId($accountId, $noteId) {
        // Call the repository method to delete the note
        $result = $this->noteRepository->hardDeleteNoteByAccountIdAndNoteId($accountId, $noteId);

        // Check if the result is null or false, indicating failure
        if ($result === false) {
            return [
                'status' => false,
                'message' => 'Note could not be deleted'
            ];
        }

        // If the deletion was successful, return a success message
        return [
            'status' => true,
            'message' => 'Note deleted successfully'
        ];
    }

    public function updateLabelByLabelName($oldLabelName, $newLabelName) {
        return $this->noteRepository->updateLabelByLabelName($oldLabelName, $newLabelName);
    }

    public function createLabelByLabelName($labelName, $accountId) {
        return $this->noteRepository->createLabelByLabelName($labelName, $accountId);
    }

    public function deleteLabelByLabelNameAndAccountId($labelName, $accountId) {
        return $this->noteRepository->deleteLabelByLabelNameAndAccountId($labelName, $accountId);
    }

    public function createNoteLabelByLabelNameAndNoteIdAndAccountId($labelName, $noteId, $accountId) {
        return $this->noteRepository->createNoteLabelByLabelNameAndNoteIdAndAccountId($labelName, $noteId, $accountId);
    }

    public function deleteNoteLabelByLabelNameAndNoteIdAndAccountId($labelName, $noteId, $accountId) {
        return $this->noteRepository->deleteNoteLabelByLabelNameAndNoteIdAndAccountId($labelName, $noteId, $accountId);
    }

    public function createImageForNoteByImageUrlAndNoteId($imageUrl, $noteId) {
        return $this->noteRepository->createImageForNoteByImageUrlAndNoteId($imageUrl, $noteId);
    }

    public function deleteImageForNoteByImageUrlAndNoteId($imageUrl, $noteId) {
        return $this->noteRepository->deleteImageForNoteByImageUrlAndNoteId($imageUrl, $noteId);
    }

    public function protectedNoteByNoteIdAndAccountId($noteId, $accountId, $password) {
        $result = $this->noteRepository->protectedNoteByNoteIdAndAccountId($noteId, $accountId, $password);
        if (!$result) {
            return [
                'status' => false,
                'message' => 'Set password to this note failed'
            ];
        }
        return [
            'status' => true,
            'message' => 'Password for this note have been set'
        ];
    }

    public function checkPasswordNoteByNoteIdAndAccountId($noteId, $accountId, $password) {
        $result = $this->noteRepository->checkPasswordNoteByNoteIdAndAccountId($noteId, $accountId, $password);
        if (!$result) {
            return [
                'status' => false,
                'message' => 'Wrong note password'
            ];
        } else {
            return [
                'status' => true,
                'message' => 'Password for this note is correct'
            ];
        }
    }

    public function deletePasswordNoteByNoteIdAndAccountId($noteId, $accountId, $inputPassword) {
        $result = $this->noteRepository->deletePasswordNoteByNoteIdAndAccountId($noteId, $accountId, $inputPassword);
        if (!$result) {
            return [
                'status' => false,
                'message' => 'Delete note password failed'
            ];
        } else {
            return [
                'status' => true,
                'message' => 'Note password deleted successfully'
            ];
        }
    }

    public function changeNotePasswordByNoteIdAndAccountId($noteId, $accountId, $password, $newPassword) {
        $result = $this->noteRepository->changeNotePasswordByNoteIdAndAccountId($noteId, $accountId, $password, $newPassword);
        if (!$result) {
            return [
                'status' => false,
                'message' => 'Change note password failed'
            ];
        } else {
            return [
                'status' => true,
                'message' => 'Note password changed successfully'
            ];
        }
    }

}
?>