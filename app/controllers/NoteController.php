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
            echo json_encode(['status' => false, 'message' => 'Missing accountId']);
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

    public function getNoteById($noteId) {
        // Fetch a single note by ID
        $note = $this->noteService->getNoteById($noteId);

        // Return the result as a JSON response
        echo json_encode(['data' => $note]);
    }
    public function createNote_POST() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $accountId = $_SESSION['accountId'] ?? null;

        if (!empty($accountId) && !empty($data['title']) && !empty($data['content'])) {
            $noteService = new NoteService();
            $result = $noteService->createNoteByAccountIdAndTitleAndContent($accountId, $data['title'], $data['content']);

            if ($result) {
                http_response_code(201);
                echo json_encode([
                    'status' => true,
                    'accountId' => $result['accountId'],
                    'title' => $result['title'],
                    'content' => $result['content'],
                    'createDate' => $result['createDate'],
                    'message' => $result['message']
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => false,
                    'message' => 'Note could not be created'
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => 'Missing title/content'
            ]);
        }
    }

    public function updateNote_POST() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $accountId = $_SESSION['accountId'] ?? null;
        $noteId = $data['noteId'] ?? null;
        $title = $data['title'] ?? null;
        $content = $data['content'] ?? null;

        if (!empty($accountId) && !empty($noteId) && !empty($title) && !empty($content)) {
            $result = $this->noteService->updateNoteByAccountIdAndNoteId($accountId, $noteId, $title, $content);

            if ($result['status'] === true) {
                http_response_code(201);
                echo json_encode ([
                    'status' => true,
                    'accountId' => $accountId,
                    'noteId' => $noteId,
                    'title' => $title,
                    'content' => $content,
                    'modifiedDate' => $result['modifiedDate'],
                    'message' => $result['message']
                ]);
            } else {
                http_response_code(400);
                echo json_encode ([
                    'status' => false,
                    'message' => $result['message']
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode ([
                'status' => false,
                'message' => 'Missing title/content'
            ]);
        }
    }

    public function deleteNote_POST() {

    }

    public function getAllNotes() {
        // Fetch all notes for the current user
        $notes = $this->noteService->getAllNotes();

        // Return the result as a JSON response
        echo json_encode(['data' => $notes]);
    }
}

?>