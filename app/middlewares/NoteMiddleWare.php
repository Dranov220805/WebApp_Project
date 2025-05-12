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
            header('location:/log/login');
            exit();
        }
        $this->noteController->getNotesPaginated();
    }

    public function getPinnedNotes() {
        $checkToken = $this->authMiddleware->checkSession();
        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->getPinnedNotesPaginated();
    }

    public function getTrashNote() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->getTrashNotePaginated();
    }

    public function SearchNotes() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->SearchNotes();
    }

    public function ShareNotes() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->getShareNotes();
    }

    public function createNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->createNote_POST();
    }

    public function updateNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->updateNote_POST();
    }

    public function deleteNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->deleteNote_POST();
    }

    public function pinNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->pinNote_POST();
    }

    public function unpinNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->unpinNote_POST();
    }

    public function restoreNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->restoreNote_POST();
    }

    public function hardDeleteNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->hardDeleteNote_POST();
    }

    public function createLabel_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->createLabel_POST();
    }

    public function updateLabel_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->updateLabel_POST();
    }

    public function deleteLabel_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->deleteLabel_POST();
    }

    public function getLabelNote($labelName) {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->getLabelNote($labelName);
    }

    public function createNoteLabel_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->createNoteLabel_POST();
    }

    public function deleteNoteLabel_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->deleteNoteLabel_POST();
    }

    public function createImageNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
        $this->noteController->createImageForNoteByImageUploadAndNoteId();
    }

    public function deleteImageNote_POST() {
        $checkToken = $this->authMiddleware->checkSession();

        if ($checkToken['status'] === false) {
            header('location:/log/login');
            exit();
        }
            $this->noteController->deleteImageForNoteByImageUrlAndNoteId();
    }

}

?>
