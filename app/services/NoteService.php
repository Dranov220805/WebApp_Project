<?php

class NoteService {
    private NoteRepository $noteRepository;

    public function __construct() {
        $this->noteRepository = new NoteRepository();
    }

    public function getNotesByAccountIdPaginated(string $accountId, int $page = 1, int $perPage = 10): array {
        return $this->noteRepository->getNotesByAccountIdPaginated($accountId, $page, $perPage);
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
            'title' => $result->getTitle(),
            'content' => $result->getContent(),
            'createDate' => $result->getCreateDate(),
            'message' => 'Note created'
        ];
    }
}
?>