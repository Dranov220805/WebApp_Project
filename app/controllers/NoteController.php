<?php

class NoteController {
    private NoteService $noteService;
    public function __construct() {
        $this->noteService = new NoteService();
    }

    public function getNotes() {
        $accountId = $_SESSION['accountId'] ?? null;

        if (!$accountId) {
            http_response_code(401);
            echo json_encode(['status' => false, 'message' => 'Missing accountId']);
            return;
        } else {
            $note = $this->noteService->getNotesByAccountId($accountId);

            echo json_encode([
                'status' => true,
                'data' => $note
            ]);
        }
    }

    public function getNotesPaginated() {

        $accountId = $_SESSION['accountId'] ?? null;
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

    public function getPinnedNotesPaginated() {

        $accountId = $_SESSION['accountId'] ?? null;
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
        $notes = $this->noteService->getPinnedNotesByAccountIdPaginated($accountId, (int)$perPage, (int)$offset);
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

    public function getTrashNotePaginated() {

        $accountId = $_SESSION['accountId'] ?? null;
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
        $notes = $this->noteService->getTrashedNotesByAccountIdPaginated($accountId, (int)$perPage, (int)$offset);
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

    public function getPinnedNotes() {

        $accountId = $_SESSION['accountId'] ?? null;
        if (empty($accountId)) {
            echo json_encode([
                'status' => false,
                'message' => 'Missing accountId'
            ]);
        } else {
            $result = $this->noteService->getPinnedNotesByAccountId($accountId);

            if (!$result) {
                echo json_encode([
                    'status' => false,
                    'message' => 'No pinned notes found'
                ]);
            } else {
                echo json_encode([
                    'status' => true,
                    'data' => $result,
                    'message' => 'Load pinned notes'
                ]);
            }
        }
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
                    'noteId' => $result['noteId'],
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

    public function getLabelNoteByLabelName($labelName) {
        $result = $this->noteService->getLabelNoteByLabelName($labelName);

        if ($result) {
            return [
                'data' => $result
            ];
        } else {
            return [
                'data' => 'failed to fetch label note data'
            ];
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
        // Get the raw JSON input
        $input = trim(file_get_contents("php://input"));
        $data = json_decode($input, true);

        // Retrieve accountId from session and noteId from request
        $accountId = $_SESSION['accountId'] ?? null;
        $noteId = $data['noteId'] ?? null;

        // Validate input
        if (empty($accountId) || empty($noteId)) {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => 'Missing accountId or noteId'
            ]);
            return;
        }

        // Call service to delete note
        $result = $this->noteService->deleteNoteByAccountIdAndNoteId($accountId, $noteId);

        // Respond with appropriate status
        if ($result['status'] === true) {
            echo json_encode([
                'status' => true,
                'message' => $result['message']
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => $result['message'] ?? 'Failed to delete note'
            ]);
        }
    }

    public function searchNotes()
    {
        $searchTerm = $_GET['query'] ?? '';

        if (empty($searchTerm)) {
            echo json_encode(['status' => false, 'message' => 'Search term is empty']);
            return;
        }

        $accountId = $_SESSION['accountId'] ?? null;

        if (!$accountId) {
            http_response_code(401);
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            return;
        }

        $notes = $this->noteService->searchNotesByAccountId($accountId, $searchTerm);

        echo json_encode(['status' => true, 'data' => $notes]);
    }

    public function getAllNotes() {
        $notes = $this->noteService->getAllNotes();

        echo json_encode(['data' => $notes]);
    }

    public function pinNote_POST() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);
        $accountId = $_SESSION['accountId'] ?? null;
        $noteId = $data['noteId'] ?? null;
        if (empty($accountId) || empty($noteId)) {
            echo json_encode([
                'status' => false,
                'message' => 'Pinned note not found'
            ]);
        } else {
            $result = $this->noteService->pinNoteByNoteId($noteId);

            if ($result['status'] === true) {
                http_response_code(201);
                echo json_encode ([
                    'status' => true,
                    'message' => $result['message']
                ]);
            } else {
                http_response_code(400);
                echo json_encode ([
                    'status' => false,
                    'message' => $result['message']
                ]);
            }
        }
    }

    public function unpinNote_POST() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);
        $accountId = $_SESSION['accountId'] ?? null;
        $noteId = $data['noteId'] ?? null;
        if (empty($accountId) || empty($noteId)) {
            echo json_encode([
                'status' => false,
                'message' => 'Pinned note not found'
            ]);
        } else {
            $result = $this->noteService->unpinNoteByNoteId($noteId);

            if ($result['status'] === true) {
                http_response_code(201);
                echo json_encode ([
                    'status' => true,
                    'message' => $result['message']
                ]);
            } else {
                http_response_code(400);
                echo json_encode ([
                    'status' => false,
                    'message' => $result['message']
                ]);
            }
        }
    }

    public function restoreNote_POST() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);
        $accountId = $_SESSION['accountId'] ?? null;
        $noteId = $data['noteId'] ?? null;
        if (empty($accountId) || empty($noteId)) {
            echo json_encode([
                'status' => false,
                'message' => 'Pinned note not found'
            ]);
        } else {
            $result = $this->noteService->restoreNoteByAccountIdAndNoteId($accountId, $noteId);

            if ($result['status'] === true) {
                http_response_code(201);
                echo json_encode ([
                    'status' => true,
                    'message' => $result['message']
                ]);
            } else {
                http_response_code(400);
                echo json_encode ([
                    'status' => false,
                    'message' => $result['message']
                ]);
            }
        }
    }

    public function hardDeleteNote_POST() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);
        $accountId = $_SESSION['accountId'] ?? null;
        $noteId = $data['noteId'] ?? null;
        if (empty($accountId) || empty($noteId)) {
            echo json_encode([
                'status' => false,
                'message' => 'Pinned note not found'
            ]);
        } else {
            $result = $this->noteService->hardDeleteNoteByAccountIdAndNoteId($accountId, $noteId);

            if ($result['status'] === true) {
                http_response_code(201);
                echo json_encode ([
                    'status' => true,
                    'message' => $result['message']
                ]);
            } else {
                http_response_code(400);
                echo json_encode ([
                    'status' => false,
                    'message' => $result['message']
                ]);
            }
        }
    }
}

?>