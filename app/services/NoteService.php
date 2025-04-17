<?php

class NoteService {
    private NoteRepository $noteRepository;
    public function __construct() {
        $this->noteRepository = new NoteRepository();
    }

    public function getNotesByAccountIdPaginated(string $accountId, int $page = 1, int $perPage = 10): array {
        return $this->noteRepository->getNotesByAccountIdPaginated($accountId, $page, $perPage);
    }
}
?>