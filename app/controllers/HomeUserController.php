<?php
include_once "./app/controllers/Base/BaseController.php";
include_once "./app/core/uploadImage/cloudinary_upload.php";
class HomeUserController extends BaseController{
    private HomeUserService $homeUserService;
    private AccountService $accountService;
    private NoteService $noteService;

    public function __construct(){
        parent::__construct();
        $this->homeUserService = new HomeUserService();
        $this->noteService = new NoteService();
        $this->accountService = new AccountService();
    }
    public function index()
    {
<<<<<<< Updated upstream
        $user = $GLOBALS['user'];
        $accountId = $user->accountId;
=======
        $accountId = $user['accountId'];
>>>>>>> Stashed changes
        $intPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = isset($_GET['limit']) ? $_GET['limit'] : 10;

        if (!isset($GLOBALS['user']) || empty($GLOBALS['user']->accountId)) {
            http_response_code(401);
            echo json_encode([
                'status' => false,
                'message' => 'Unauthorized'
            ]);
            exit();
        }

        $offset = ($intPage - 1) * $perPage;

        try {
            $pinnedNotes = $this->noteService->getPinnedNotesByAccountId($accountId);
            $otherNotes = $this->noteService->getNotesByAccountId($accountId);

            $this->Views('home', [
                'status' => true,
                'pinnedNotes' => $pinnedNotes,
                'otherNotes' => $otherNotes,
                'pagination' => [
                    'currentPage' => (int)$intPage,
                    'perPage' => (int)$perPage,
                ]
            ]);
        } catch (Exception $e) {
            error_log("Error fetching notes: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => false,
                'message' => 'Failed to load notes'
            ]);
        }
    }
    public function redirectToIndex() {
        $content = 'home';
        $footer = 'home';
        include "./views/layout/index.php";
    }
    public function showError() {
        include "./views/error/404.php";
    }
    public function homeReference() {
        $content = 'home-user-preference';
        $footer = 'home';
        include "./views/layout/index.php";
    }
    public function homeLabel() {
        $content = 'home-user-label';
        $footer = 'home';
        include "./views/layout/index.php";
    }
<<<<<<< Updated upstream
    public function getUserLabel() {
        $user = $GLOBALS['user'];
        $accountId = $user->accountId;
=======
    public function getUserLabel($user) {
        $accountId = $user['accountId'];
>>>>>>> Stashed changes
        $labelNotes = $this->homeUserService->getLabelByAccountId($accountId);

        return $labelNotes;
    }
<<<<<<< Updated upstream
    public function homeLabel_POST($labelName) {
        $user = $GLOBALS['user'];
        $accountId = $user->accountId;
=======
    public function homeLabel_POST($user, $labelName) {
        $accountId = $user['accountId'];
>>>>>>> Stashed changes

        if (!$accountId) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Missing accountId']);
            return;
        }

        $result = $this->noteService->getLabelNoteByLabelName($labelName, $accountId);

        // Pass data to the view
        $this->Views('home-user-label', [
            'status' => true,
            'labelName' => $labelName,
            'data' => $result,
            'message' => 'Get label view for this account successfully'
        ]);
    }
<<<<<<< Updated upstream
    public function homeShare() {
        $user = $GLOBALS['user'];
        $email = $user->email;
=======
    public function homeShare($user) {
        $email = $user['email'];
>>>>>>> Stashed changes

        $result = $this->noteService->getNotesSharedByEmail($email);

        $this->Views('home-user-share', [
            'status' => true,
            'data' => $result,
            'message' => 'Get shared view for this account successfully'
        ]);
    }
<<<<<<< Updated upstream
    public function homeTrash() {
        $user = $GLOBALS['user'];
        $accountId = $user->accountId;
=======
    public function homeTrash($user) {
        $accountId = $user['accountId'];
>>>>>>> Stashed changes

        if (!$accountId) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Missing accountId']);
            return;
        }

        $trashNotes = $this->noteService->getTrashedNotesByAccountId($accountId);

        $this->Views('home-user-trash', [
            'status' => true,
            'trashNotes' => $trashNotes,
            'message' => 'Get trashed notes for this account successfully'
        ]);

    }

    public function uploadAvatar() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            $fileTmpPath = $_FILES['avatar']['tmp_name'];
<<<<<<< Updated upstream
            $accountId = $GLOBALS['user']->accountId ?? null;
            $oldImage = $GLOBALS['user']->profilePicture ?? null;
=======
            $accountId = $user['accountId'] ?? null;
            $email = $user['email'] ?? null;
            $oldImage = $user->profilePicture ?? null;
>>>>>>> Stashed changes

            if ($oldImage) {
                deleteImageByImageUrl($oldImage);
            }

            $uploadResponse = uploadAvatarToCloudinary($fileTmpPath);

            if ($uploadResponse['status'] === true) {
                $result = $this->accountService->updateProfilePictureByAccountId($accountId, $uploadResponse['url']);
                if ($result['status'] === true) {

<<<<<<< Updated upstream
                    setcookie('access_token', $result['token'], [
                        'expires' => time() + 3600, // 1 hour (match your JWT expiry)
                        'path' => '/',
                        'secure' => true, // Set to true if using HTTPS
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
=======
                    $_SESSION['profilePicture'] = $uploadResponse['url'];
>>>>>>> Stashed changes

                    echo json_encode([
                        'status' => true,
                        'picture' => $uploadResponse['url'],
                        'message' => $result['message']
                    ]);
                } else {
                    echo json_encode([
                        'status' => false,
                        'message' => $result['message']
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => $uploadResponse['message']
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid request'
            ]);
        }
    }

    public function getPreferencesByAccountId() {
        header('Content-Type: application/json');

<<<<<<< Updated upstream
        $accountId = $_SESSION['accountId'] ?? null;
=======
        $accountId = $user['accountId'] ?? null;
>>>>>>> Stashed changes
        $result = $this->accountService->getPreferencesByAccountId($accountId);
        if(!$result['status'] === true) {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => 'Failed to retrieve preferences'
            ]);
        } else {
            http_response_code(200);
            echo json_encode([
                'status' => true,
                'data' => $result,
                'message' => 'Get preferences successfully'
            ]);
        }
    }

    public function updatePreference() {
        header('Content-Type: application/json');
<<<<<<< Updated upstream
        $user = $GLOBALS['user'];
        $accountId = $user->accountId;
=======
        $accountId = $user['accountId'];
>>>>>>> Stashed changes
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        if (!empty($data['theme']) || !empty($data['userName']) || !empty($data['noteFont']) || !empty($data['noteColor'])) {
            $result = $this->accountService->updatePreferenceByAccountId($accountId, $data['userName'], $data['theme'], $data['noteFont'], $data['noteColor']);

            if ($result['status'] === true) {
                http_response_code(200);

<<<<<<< Updated upstream
                setcookie('access_token', $result['token'], [
                    'expires' => time() + 3600, // 1 hour (match your JWT expiry)
                    'path' => '/',
                    'secure' => true, // Set to true if using HTTPS
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
=======
                $_SESSION['isDarkTheme'] = $data['theme'] == 'dark' ? true : false;
                $_SESSION['userName'] = $data['userName'];

                $data = [
                    'theme' => $data['theme'],
                    'userName' => $data['userName'],
                    'noteFont' => $data['noteFont'],
                    'noteColor' => $data['noteColor']
                ];
>>>>>>> Stashed changes

                echo json_encode([
                    'status' => true,
                    'userName' => $data['userName'],
                    'data' => $data,
                    'message' => $result['message']
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => false,
                    'message' => $result['message']
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => "Missing data"
            ]);
        }
    }

    public function addNewSharedEmail_POST() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $noteId = $data['noteId'] ?? null;
        $newEmail = $data['newSharedEmail'] ?? null;
<<<<<<< Updated upstream
        $user = $GLOBALS['user'];
        $email = $user->email;
=======
        $email = $user['email'];
>>>>>>> Stashed changes
        if (empty($noteId) || empty($newEmail) || empty($email)) {
            echo json_encode([
                'status' => false,
                'newEmail' => $newEmail,
                'email' => $email,
                'noteId' => $noteId,
                'message' => "Missing noteId, or newEmail, or Email"
            ]);
        }
        $result = $this->homeUserService->addSharedEmailByNoteIdAndEmailAndNewEmail($noteId, $email, $newEmail);
        if ($result['status'] === true) {
            http_response_code(200);
            echo json_encode([
               'status' => true,
               'data' =>$result['data'],
               'message' => $result['message']
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
               'status' => false,
               'message' => $result['message']
            ]);
        }
    }

    public function deleteSharedEmail_DELETE() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $noteId = $data['noteId'] ?? null;
        $email = $data['sharedEmail'] ?? null;
        if (empty($noteId) || empty($email)) {
            echo json_encode([
                'status' => false,
                'email' => $email,
                'noteId' => $noteId,
                'message' => "Missing noteId, or Email"
            ]);
        }
        $result = $this->homeUserService->removeSharedEmailByNoteIdAndEmail($noteId, $email);
        if ($result['status'] === true) {
            http_response_code(200);
            echo json_encode([
                'status' => true,
                'data' =>$result['data'],
                'message' => $result['message']
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => $result['message']
            ]);
        }
    }

    public function updateShareEmail_PUT() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $noteId = $data['noteId'] ?? null;
        $email = $data['receivedEmail'] ?? null;
        $canEdit = $data['canEdit'] ?? null;
        if (empty($noteId) || empty($email)) {
            echo json_encode([
                'status' => false,
                'email' => $email,
                'noteId' => $noteId,
                'canEdit' => $canEdit,
                'message' => "Missing noteId, Email or canEdit"
            ]);
        }
        $result = $this->homeUserService->updatePermissionByNoteIdAndEmail($noteId, $email, $canEdit);
        if ($result['status'] === true) {
            http_response_code(200);
            echo json_encode([
                'status' => true,
                'data' =>$result['data'],
                'message' => $result['message']
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => $result['message']
            ]);
        }
    }

    public function sharedEmailList() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $noteId = $data['noteId'] ?? null;
<<<<<<< Updated upstream
        $user = $GLOBALS['user'];
        $email = $user->email;
=======
        $email = $user['email'];
>>>>>>> Stashed changes

        if (!empty($noteId) || !empty($email)) {
            $result = $this->homeUserService->getSharedEmailByNoteIdAndEmail($noteId, $email);
            if ($result) {
                echo json_encode([
                    'status' => true,
                    'result' => $result,
                    'message' => 'Get emails shared by this note successfully'
                ]);
            } else {
                echo json_encode([
                   'status' => false,
                   'message' => 'This note does not have any shared email'
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => "Missing noteId or accountId"
            ]);
        }
    }

}
