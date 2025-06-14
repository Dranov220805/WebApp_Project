<?php

class NoteController {
    private NoteService $noteService;
    public function __construct() {
        $this->noteService = new NoteService();
    }

    public function getNotes($user) {
        $accountId = $user['accountId'] ?? null;

        if (!$accountId) {
            http_response_code(401);
            echo json_encode([
                'status' => false,
                'message' => 'Missing accountId'
            ]);
            return;
        } else {
            $note = $this->noteService->getNotesByAccountId($accountId);

            echo json_encode([
                'status' => true,
                'data' => $note
            ]);
        }
    }

    public function getNotesPaginated($user) {
        $accountId = $user['accountId'];

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

    public function getPinnedNotesPaginated($user) {
        $accountId = $user['accountId'];

        $intPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = isset($_GET['limit']) ? $_GET['limit'] : 100;

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

        $notes = $this->noteService->getPinnedNotesByAccountIdPaginated($accountId, (int)$perPage, (int)$offset);

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

    public function getTrashNotePaginated($user) {
        $accountId = $user['accountId'] ?? null;

        $intPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = isset($_GET['limit']) ? $_GET['limit'] : 100;

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

        $notes = $this->noteService->getTrashedNotesByAccountIdPaginated($accountId, (int)$perPage, (int)$offset);

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

    public function getPinnedNotes($user) {
        $accountId = $user['accountId'] ?? null;

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

    public function getShareNotes($user) {
        $accountId = $user['accountId'] ?? null;

        if (empty($accountId)) {
            echo json_encode([
                'status' => false,
                'message' => 'Missing accountId'
            ]);
        } else {
            $result = $this->noteService->getSharedNoteByAccountId($accountId);
            if (!$result) {
                echo json_encode([
                    'status' => false,
                    'message' => 'No shared notes found'
                ]);
            } else {
                echo json_encode([
                    'status' => true,
                    'data' => $result,
                    'message' => 'Load shared notes'
                ]);
            }
        }
    }

    public function createNote_POST($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $accountId = $user['accountId'] ?? null;

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

    public function updateNote_POST($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $accountId = $user['accountId'] ?? null;

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

    public function deleteNote_POST($user) {
        $input = trim(file_get_contents("php://input"));
        $data = json_decode($input, true);

        $accountId = $user['accountId'] ?? null;
        $noteId = $data['noteId'] ?? null;

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

    public function searchNotes($user)
    {
        $searchTerm = $_GET['query'] ?? '';

        if (empty($searchTerm)) {
            echo json_encode([
                'status' => false,
                'message' => 'Search term is empty'
            ]);
            return;
        }

        $accountId = $user['accountId'] ?? null;

        if (!$accountId) {
            http_response_code(401);
            echo json_encode([
                'status' => false,
                'message' => 'Unauthorized'
            ]);
            return;
        }

        $notes = $this->noteService->searchNotesByAccountId($accountId, $searchTerm);

        if (!$notes) {
            echo json_encode([
                'status' => false,
                'message' => 'No notes found'
            ]);
        } else {
            echo json_encode([
                'status' => true,
                'data' => $notes,
                'message' => 'Search note found'
            ]);
        }
    }

    public function pinNote_POST($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $accountId = $user['accountId'] ?? null;
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

    public function unpinNote_POST($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $accountId = $user['accountId'] ?? null;
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

    public function restoreNote_POST($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $accountId = $user['accountId'] ?? null;
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

    public function hardDeleteNote_POST($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $accountId = $user['accountId'] ?? null;
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

    public function getLabelNote($user, $labelName) {
        $accountId = $user['accountId'];

        $result = $this->noteService->getLabelNoteByLabelName($labelName, $accountId);
        if ($result) {
            echo json_encode([
                'status' => true,
                'data' => $result,
                'message' => 'Get label note successfully'
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => 'Get label note failed'
            ]);
        }
    }

    public function createLabel_POST($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);
        $accountId = $user['accountId'] ?? null;
        $labelName = $data['labelName'] ?? null;
        if (empty($accountId) || empty($labelName)) {
            echo json_encode([
                'status' => false,
                'message' => 'Label name is empty'
            ]);
        } else {
            $result = $this->noteService->createLabelByLabelName($labelName, $accountId);
            if ($result) {
                http_response_code(201);
                echo json_encode ([
                    'status' => true,
                    'data' => $result,
                    'message' => 'Label created successfully'
                ]);
            } else {
                http_response_code(400);
                echo json_encode ([
                    'status' => false,
                    'message' => 'Failed to create label'
                ]);
            }
        }
    }

    public function updateLabel_POST($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);
        $accountId = $user['accountId'] ?? null;
        $oldLabelName = $data['oldLabel'] ?? null;
        $newLabelName = $data['newLabel'] ?? null;
        if (empty($accountId) || empty($newLabelName || empty($oldLabelName))) {
            echo json_encode([
                'status' => false,
                'message' => 'Label name is empty'
            ]);
        } else {
            $result = $this->noteService->updateLabelByLabelName($oldLabelName, $newLabelName);
            if ($result) {
                http_response_code(201);
                echo json_encode ([
                    'status' => true,
                    'data' => $result,
                    'message' => 'Label updated successfully'
                ]);
            } else {
                http_response_code(400);
                echo json_encode ([
                    'status' => false,
                    'message' => 'Failed to update label'
                ]);
            }
        }
    }

    public function deleteLabel_POST($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);
        $accountId = $user['accountId'] ?? null;
        $labelName = $data['labelName'] ?? null;
        if (empty($accountId) || empty($labelName)) {
            echo json_encode([
                'status' => false,
                'message' => 'Label name is empty'
            ]);
        } else {
            $result = $this->noteService->deleteLabelByLabelNameAndAccountId($labelName, $accountId);
            if ($result) {
                http_response_code(201);
                echo json_encode ([
                    'status' => true,
                    'data' => $result,
                    'message' => 'Label deleted successfully'
                ]);
            } else {
                http_response_code(400);
                echo json_encode ([
                    'status' => false,
                    'message' => 'Failed to delete label'
                ]);
            }
        }
    }

    public function createNoteLabel_POST($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);
        $accountId = $user['accountId'] ?? null;
        $labelName = $data['labelName'] ?? null;
        $noteId = $data['noteId'] ?? null;
        if (empty($accountId) || empty($labelName) || empty($noteId)) {
            echo json_encode([
                'status' => false,
                'message' => 'Label name or note id is empty'
            ]);
        } else {
            $result = $this->noteService->createNoteLabelByLabelNameAndNoteIdAndAccountId($labelName, $noteId, $accountId);

            if ($result) {
                echo json_encode ([
                    'status' => true,
                    'data' => $result,
                    'message' => 'Add note to label created successfully'
                ]);
            } else {
                echo json_encode ([
                    'status' => false,
                    'message' => 'Failed to add note to label'
                ]);
            }
        }

    }

    public function deleteNoteLabel_POST($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);
        $accountId = $user['accountId'] ?? null;
        $labelName = $data['labelName'] ?? null;
        $noteId = $data['noteId'] ?? null;
        if (empty($accountId) || empty($labelName) || empty($noteId)) {
            echo json_encode([
                'status' => false,
                'message' => 'Label name or note id is empty'
            ]);
        } else {
            $result = $this->noteService->deleteNoteLabelByLabelNameAndNoteIdAndAccountId($labelName, $noteId, $accountId);

            if ($result) {
                echo json_encode ([
                    'status' => true,
                    'data' => $result,
                    'message' => 'Delete note label successfully'
                ]);
            } else {
                echo json_encode ([
                    'status' => false,
                    'message' => 'Failed to delete note to label'
                ]);
            }
        }
    }

    public function createImageForNoteByImageUploadAndNoteId($user) {
        $accountId = $user['accountId'] ?? null;
        $noteId = $_POST['noteId'] ?? null;
        $imageUpload = $_FILES['image'] ?? null;

        if (empty($accountId) || empty($noteId)) {
            echo json_encode([
                'status' => false,
                'message' => 'Account ID or Note ID is missing'
            ]);
            return;
        }

        if (empty($imageUpload) || $imageUpload['error'] !== UPLOAD_ERR_OK) {
            echo json_encode([
                'status' => false,
                'message' => 'No valid image uploaded'
            ]);
            return;
        }

        try {
            $uploadResult = uploadNoteImageToCloudinary($imageUpload['tmp_name']);

            if ($uploadResult['status'] === false) {
                echo json_encode([
                    'status' => false,
                    'message' => $uploadResult['message']
                ]);
                return;
            }

            // Save image to DB via your service
            $imageUrl = $uploadResult['url'];
            $this->noteService->createImageForNoteByImageUrlAndNoteId($imageUrl, $noteId);

            echo json_encode([
                'status' => true,
                'url' => $imageUrl,
                'message' => 'Image uploaded successfully!'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteImageForNoteByImageUrlAndNoteId($user) {
        $accountId = $user['accountId'] ?? null;
        $noteId = $_POST['noteId'] ?? null;
        $imageUrl = $_POST['imageUrl'] ?? null;

        if (empty($accountId) || empty($noteId) || empty($imageUrl)) {
            echo json_encode([
                'status' => false,
                'message' => 'Account ID or Note ID or ImageUrl is missing'
            ]);
            return;
        }

        try {

            $uploadResult = deleteImageByImageUrl($imageUrl);

            if ($uploadResult['status'] === false) {
                echo json_encode([
                    'status' => false,
                    'message' => $uploadResult['message']
                ]);
                return;
            }

            $this->noteService->deleteImageForNoteByImageUrlAndNoteId($imageUrl, $noteId);

            echo json_encode([
                'status' => true,
                'url' => $imageUrl,
                'message' => 'Image deleted successfully!'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Error deleting image: ' . $e->getMessage()
            ]);
        }
    }

    public function checkNotePassword_POST($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $accountId = $user['accountId'] ?? null;
        $noteId = $data['noteId'] ?? null;
        $password = $data['password'] ?? null;
        if (empty($accountId) || empty($noteId) || empty($password)) {
            echo json_encode([
                'status' => false,
                'message' => 'AccountId or Password is missing'
            ]);
        } else {
            $result = $this->noteService->checkNotePasswordByNoteIdAndPassword($noteId, $accountId, $password);
            if ($result['status'] === false) {
                echo json_encode([
                    'status' => false,
                    'message' => $result['message']
                ]);
            } else {
                echo json_encode ([
                    'status' => true,
                    'message' => $result['message']
                ]);
            }
        }
    }

    public function createNotePassword_POST($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $accountId = $user['accountId'] ?? null;
        $noteId = $data['noteId'] ?? null;
        $password = $data['password'] ?? null;
        if (empty($accountId) || empty($noteId) || empty($password)) {
            echo json_encode([
                'status' => false,
                'message' => 'AccountId or Note password is missing'
            ]);
        } else {
            $result = $this->noteService->protectedNoteByNoteIdAndAccountId($noteId, $accountId, $password);
            if ($result['status'] === true) {
                echo json_encode ([
                    'status' => true,
                    'message' => $result['message'],
                ]);
            } else {
                echo json_encode ([
                    'status' => false,
                    'message' => $result['message']
                ]);
            }
        }
    }

    public function deleteNotePassword_DELETE($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $accountId = $user['accountId'] ?? null;
        $noteId = $data['noteId'] ?? null;
        $password = $data['password'] ?? null;

        if (empty($accountId) || empty($noteId) || empty($password)) {
            echo json_encode([
                'status' => false,
                'message' => 'AccountId or NoteId or Password is missing'
            ]);
        } else {
            $result = $this->noteService->deletePasswordNoteByNoteIdAndAccountId($noteId, $accountId, $password);
            if ($result['status'] === true) {
                echo json_encode ([
                    'status' => true,
                    'message' => $result['message']
                ]);
            } else {
                echo json_encode ([
                    'status' => false,
                    'message' => $result['message']
                ]);
            }
        }
    }

    public function changeNotePassword_PUT($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $accountId = $user['accountId'] ?? null;
        $noteId = $data['noteId'] ?? null;
        $password = $data['password'] ?? null;
        $newPassword = $data['newPassword'] ?? null;
        if (empty($accountId) || empty($noteId) || empty($password) || empty($newPassword)) {
            echo json_encode([
                'status' => false,
                'message' => 'AccountId or NoteId or Password is missing'
            ]);
        } else {
            $result = $this->noteService->changeNotePasswordByNoteIdAndAccountId($noteId, $accountId, $password, $newPassword);
            if ($result['status'] === true) {
                echo json_encode ([
                    'status' => true,
                    'message' => $result['message']
                ]);
            } else {
                echo json_encode ([
                    'status' => false,
                    'message' => $result['message']
                ]);
            }
        }
    }

}

?>