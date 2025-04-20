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
            $this->noteController->getNotes();
        }
    }

    public function createNote_POST() {
        if (!isset($_SESSION['roleId'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->createNote_POST();
        }
    }

}

?>
