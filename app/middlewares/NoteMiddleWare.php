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

}

?>
