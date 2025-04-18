<?php

include "./app/controllers/NoteController.php";
include "./app/middlewares/NoteMiddleWare.php";

// Create instances of controller and middleware
$noteController = new NoteController();
$noteMiddleWare = new NoteMiddleWare();

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Check if we have a param_1 and param_2 in the URL
    if (isset($_GET['param_1']) && isset($_GET['param_2'])) {
        switch ($_GET['param_1']) {
            case 'note':
                // Handling the 'note' endpoint
                if ($_GET['param_2'] == 'list') {
                    // Call a function to return the paginated list of notes
                    $noteMiddleWare->getNotes();
                } else {
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
            case 'note':
                // Handle fetching specific note based on id or other parameters
                // Example: Fetch a specific note by ID (this will depend on your logic)
//                $noteController->getNoteById($_GET['param_2']);
                break;
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
    // Example handling for note creation, etc.
    if (isset($_POST['param_1']) && isset($_POST['param_2']) && isset($_POST['param3'])) {

    } else if (isset($_POST['param_1']) && isset($_POST['param_2'])) {
        switch ($_POST['param_1']) {
            case 'note':
                if ($_POST['param_2'] == 'create') {
                    // Handle creating a new note, for example
                    $noteController->createNote($_POST);
                }
                break;
            // Add more cases as needed for other POST actions
            default:
//                echo json_encode(['error' => 'Invalid action']);
                break;
        }
    } else if (isset($_POST['param_1'])) {
//        echo json_encode(['error' => 'Missing parameters']);
    }
}

?>