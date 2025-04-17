<?php

class NoteMiddleWare {
    private NoteController $noteController;
    public function __construct() {
        $this->noteController = new NoteController();
    }

    public function getNotes() {
        if (!isset($_SESSION['accessToken'])) {
            throw new Exception("Unauthenticated");
        } else {
            $this->noteController->getNotes();
        }
    }
}

?>
