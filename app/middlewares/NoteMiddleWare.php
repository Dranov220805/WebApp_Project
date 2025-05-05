<?php

class NoteMiddleWare {
    private NoteController $noteController;
    public function __construct() {
        $this->noteController = new NoteController();
    }

    public function getNotes() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->getNotesPaginated();
        }
    }

    public function getPinnedNotes() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->getPinnedNotesPaginated();
        }
    }

    public function getTrashNote() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->getTrashNotePaginated();
        }
    }

    public function SearchNotes() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->SearchNotes();
        }
    }

//    public function getPinnedNotes() {
//        if (!isset($_SESSION['roleId'])) {
//            throw new Exception("Unauthenticated");
//        } else {
//            $this->noteController->getPinnedNotes();
//        }
//    }

    public function createNote_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->createNote_POST();
        }
    }

    public function updateNote_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->updateNote_POST();
        }
    }

    public function deleteNote_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->deleteNote_POST();
        }
    }

    public function pinNote_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->pinNote_POST();
        }
    }

    public function unpinNote_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->unpinNote_POST();
        }
    }

    public function restoreNote_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->restoreNote_POST();
        }
    }

    public function hardDeleteNote_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->hardDeleteNote_POST();
        }
    }

    public function createLabel_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->createLabel_POST();
        }
    }

    public function updateLabel_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->updateLabel_POST();
        }
    }

    public function deleteLabel_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->deleteLabel_POST();
        }
    }

    public function getLabelNote($labelName) {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->getLabelNote($labelName);
        }
    }

    public function createNoteLabel_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->createNoteLabel_POST();
        }
    }

    public function deleteNoteLabel_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->deleteNoteLabel_POST();
        }
    }

    public function createImageNote_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->createImageForNoteByImageUploadAndNoteId();
        }
    }

    public function deleteImageNote_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->deleteImageForNoteByImageUrlAndNoteId();
        }
    }

}

?>
