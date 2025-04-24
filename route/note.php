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
                }
        }
    } else if (isset($_GET['param_1']) && isset($_GET['param_2'])) {
        switch ($_GET['param_1']) {
            case 'note':
                if ($_GET['param_2'] == 'list') {
                    $noteMiddleWare->getNotes();
                } else if ($_GET['param_2'] == 'search') {
                    $noteMiddleWare->SearchNotes();
                    // Return an error or other specific handling if necessary
//                    echo json_encode(['error' => 'Invalid note action']);
                }
                break;
            default:
//                echo json_encode(['error' => 'Invalid endpoint for note']);
                break;
        }
    } else if (isset($_GET['param_1'])) {
        // Handling a single parameter, e.g., specific note fetching
        switch ($_GET['param_1']) {
//            case 'note':
                // Handle fetching specific note based on id or other parameters
                // Example: Fetch a specific note by ID (this will depend on your logic)
//                $noteController->getNoteById($_GET['param_2']);
//                break;
            default:
//                echo json_encode(['error' => 'Invalid endpoint for note']);
                break;
        }
    } else {
        // Handle default case, such as listing all notes
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
                }else if ($_GET['param_2'] == 'delete') {
                    $noteMiddleWare->deleteNote_POST();
                } else if ($_GET['param_2'] == 'pin') {
                    $noteMiddleWare->pinNote_POST();
                } else if ($_GET['param_2'] == 'unpin') {
                    $noteMiddleWare->unpinNote_POST();
                }
                break;
            default:
//                echo json_encode(['error' => 'Invalid action']);
                break;
        }
    } else if (isset($_GET['param_1'])) {
//        echo json_encode(['error' => 'Missing parameters']);
    }
}

?>