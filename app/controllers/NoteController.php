<?php

class NoteController {
    private NoteService $noteService;
    public function __construct() {
        $this->noteService = new NoteService();
    }

    public function getNotes() {
        header('Content-Type: application/json');

        $accountId = $_SESSION['userName'] ?? null;

        $intPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = isset($_GET['limit']) ? $_GET['limit'] : 10;

        // Basic validation
        if (!$accountId) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Missing accountIs']);
            return;
        } else if (!is_numeric($intPage) || !is_numeric($perPage)) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Missing info for pagination']);
            return;
        }

        $offset = ($intPage - 1) * $perPage;

        // Get paginated notes
        $notes = $this->noteService->getNotesByAccountIdPaginated($accountId, (int)$perPage, (int)$offset);
//        $totalCount = $this->noteService->getTotalNotesCount($accountId);

        echo json_encode([
            'status' => true,
            'data' => $notes,
            'pagination' => [
                'currentPage' => (int)$intPage,
                'perPage' => (int)$perPage,
//                'total' => $totalCount,
//                'totalPages' => ceil($totalCount / $perPage)
            ]
        ]);
    }

//    public function getNotesList() {
//        // Assuming pagination parameters are passed (page, limit)
//        $page = isset($_GET['page']) ? $_GET['page'] : 1;
//        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
//
//        // Call your service layer to fetch paginated notes
//        $notes = $this->noteService->getNotesByAccountIdPaginated($accountId, $limit, ($page - 1) * $limit);
//
//        // Return the result as a JSON response
//        echo json_encode(['data' => $notes]);
//    }

    public function getNoteById($noteId) {
        // Fetch a single note by ID
        $note = $this->noteService->getNoteById($noteId);

        // Return the result as a JSON response
        echo json_encode(['data' => $note]);
    }

    public function createNote($data) {
        // Handle note creation from POST data
        // Validate and process the POST data (e.g., title, content, etc.)
        $note = $this->noteService->createNote($data);

        // Return the result as a JSON response
        echo json_encode(['message' => 'Note created successfully', 'data' => $note]);
    }

    public function getAllNotes() {
        // Fetch all notes for the current user
        $notes = $this->noteService->getAllNotes();

        // Return the result as a JSON response
        echo json_encode(['data' => $notes]);
    }
}

?>