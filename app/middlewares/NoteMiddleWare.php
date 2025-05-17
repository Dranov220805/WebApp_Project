<?php

class NoteMiddleWare {
    private NoteController $noteController;
    private AuthMiddleware $authMiddleware;

    public function __construct() {
        $this->noteController = new NoteController();
        $this->authMiddleware = new AuthMiddleware();
    }

    public function getNotes() {
        $checkToken = $this->authMiddleware->checkSession();
        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->getNotesPaginated($checkToken['user']);
    }

    public function getPinnedNotes() {
        $checkToken = $this->authMiddleware->checkSession();
        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->getPinnedNotesPaginated($checkToken['user']);
    }

    public function getTrashNote() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->getTrashNotePaginated($checkToken['user']);
    }

    public function SearchNotes() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->SearchNotes($checkToken['user']);
    }

    public function ShareNotes() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->getShareNotes($checkToken['user']);
    }

    public function createNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->createNote_POST($checkToken['user']);
    }

    public function updateNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->updateNote_POST($checkToken['user']);
    }

    public function deleteNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->deleteNote_POST($checkToken['user']);
    }

    public function pinNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->pinNote_POST($checkToken['user']);
    }

    public function unpinNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->unpinNote_POST($checkToken['user']);
    }

    public function restoreNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->restoreNote_POST($checkToken['user']);
    }

    public function hardDeleteNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->hardDeleteNote_POST($checkToken['user']);
    }

    public function createLabel_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->createLabel_POST($checkToken['user']);
    }

    public function updateLabel_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->updateLabel_POST($checkToken['user']);
    }

    public function deleteLabel_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->deleteLabel_POST($checkToken['user']);
    }

    public function getLabelNote($labelName) {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->getLabelNote($checkToken['user'], $labelName);
    }

    public function createNoteLabel_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->createNoteLabel_POST($checkToken['user']);
    }

    public function deleteNoteLabel_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->deleteNoteLabel_POST($checkToken['user']);
    }

    public function createImageNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->createImageForNoteByImageUploadAndNoteId($checkToken['user']);
    }

    public function deleteImageNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            exit();
        }
            $this->noteController->deleteImageForNoteByImageUrlAndNoteId($checkToken['user']);
    }

    public function checkNotePassword_POST() {
        $checkToken = $this->authMiddleware->checkSession();
        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->checkNotePassword_POST($checkToken['user']);
    }

    public function createNotePassword_POST() {
        $checkToken = $this->authMiddleware->checkSession();
        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->createNotePassword_POST($checkToken['user']);
    }

    public function deleteNotePassword_DELETE() {
        $checkToken = $this->authMiddleware->checkSession();
        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->deleteNotePassword_DELETE($checkToken['user']);
    }

    public function changeNotePassword_PUT() {
        $checkToken = $this->authMiddleware->checkSession();
        if ($checkToken['status'] === false) {
            exit();
        }
        $this->noteController->changeNotePassword_PUT($checkToken['user']);
    }

}

?>
