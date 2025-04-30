<?php

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

}
?>