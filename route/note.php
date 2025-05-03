<?php

include "./app/controllers/NoteController.php";
include "./app/middlewares/NoteMiddleWare.php";
$noteController = new NoteController();
$noteMiddleWare = new NoteMiddleWare();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['param_1']) && isset($_GET['param_2']) && isset($_GET['param_3'])) {
        switch ($_GET['param_1']) {
            case 'note':
                if ($_GET['param_2'] == 'pinned') {
                    if (isset($_GET['param_3']) == 'list') {
                        $noteMiddleWare->getPinnedNotes();
                    }
                } else if ($_GET['param_2'] == 'label') {
                    $labelName = urldecode($_GET['label-name']);
                    $noteMiddleWare->getLabelNote($labelName);
                }
        }
    } else if (isset($_GET['param_1']) && isset($_GET['param_2'])) {
        switch ($_GET['param_1']) {
            case 'note':
                if ($_GET['param_2'] == 'list') {
                    $noteMiddleWare->getNotes();
                } else if($_GET['param_2'] == 'pinned-list') {
                    $noteMiddleWare->getPinnedNotes();
                } else if ($_GET['param_2'] == 'trash-list') {
                    $noteMiddleWare->getTrashNote();
                } else if ($_GET['param_2'] == 'search') {
                    $noteMiddleWare->SearchNotes();
                }
                break;
            default:
//                echo json_encode(['error' => 'Invalid endpoint for note']);
                break;
        }
    } else if (isset($_GET['param_1'])) {
        switch ($_GET['param_1']) {

            default:

                break;
        }
    } else {
        $noteController->getAllNotes();
    }
}

// Handle POST requests
else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_GET['param_1']) && isset($_GET['param_2']) && isset($_GET['param3'])) {

    } else if (isset($_GET['param_1']) && isset($_GET['param_2'])) {
        switch ($_GET['param_1']) {
            case 'note':
                if ($_GET['param_2'] == 'create') {
                    $noteMiddleWare->createNote_POST();
                } else if ($_GET['param_2'] == 'update') {
                    $noteMiddleWare->updateNote_POST();
                } else if ($_GET['param_2'] == 'delete') {
                    $noteMiddleWare->deleteNote_POST();
                } else if ($_GET['param_2'] == 'pin') {
                    $noteMiddleWare->pinNote_POST();
                } else if ($_GET['param_2'] == 'unpin') {
                    $noteMiddleWare->unpinNote_POST();
                } else if ($_GET['param_2'] == 'hard-delete') {
                    $noteMiddleWare->hardDeleteNote_POST();
                }
                break;

            case 'label':
                if ($_GET['param_2'] == 'create') {
                    $noteMiddleWare->createLabel_POST();
                } else if ($_GET['param_2'] == 'update') {
                    $noteMiddleWare->updateLabel_POST();
                } else if ($_GET['param_2'] == 'delete') {
                    $noteMiddleWare->deleteLabel_POST();
                }
                break;

            default:
                break;
        }
    } else if (isset($_GET['param_1'])) {
//        echo json_encode(['error' => 'Missing parameters']);
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if (isset($_GET['param_1']) && isset($_GET['param_2']) && isset($_GET['param3'])) {

    } else if (isset($_GET['param_1']) && isset($_GET['param_2'])) {
        switch ($_GET['param_1']) {
            case 'note':
                if ($_GET['param_2'] == 'restore') {
                    $noteMiddleWare->restoreNote_POST();
                }
        }
    }
}

?>