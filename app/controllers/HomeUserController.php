<?php
include_once "./app/controllers/Base/BaseController.php";
include_once "./app/core/uploadImage/cloudinary_upload.php";
class HomeUserController extends BaseController{
    private AuthMiddleware $authMiddleware;
    private HomeUserService $homeUserService;
    private AccountService $accountService;
    private NoteService $noteService;

    public function __construct(){
        parent::__construct();
        $this->authMiddleware = new AuthMiddleware();
        $this->homeUserService = new HomeUserService();
        $this->noteService = new NoteService();
        $this->accountService = new AccountService();
    }
    public function index($user)
    {
        $accountId = $user->accountId;
        $intPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = isset($_GET['limit']) ? $_GET['limit'] : 10;

        if (!isset($user) || empty($accountId)) {
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
    public function getUserInfo() {
        return $this->authMiddleware->checkSession();
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
    public function homeLabel($user) {
        $this->Views('home-user-label', [
            'user' => $user
        ]);
    }
    public function getUserLabel($user) {
        $accountId = $user->accountId;
        $labelNotes = $this->homeUserService->getLabelByAccountId($accountId);

        return $labelNotes;
    }
    public function homeLabel_POST($user, $labelName) {
        $accountId = $user->accountId;

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
    public function homeShare($user) {
        $email = $user->email;

        $result = $this->noteService->getNotesSharedByEmail($email);

        $this->Views('home-user-share', [
            'status' => true,
            'data' => $result,
            'message' => 'Get shared view for this account successfully'
        ]);
    }
    public function homeTrash($user) {
        $accountId = $user->accountId;

        // Basic validation
        if (!$accountId) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Missing accountId']);
            return;
        }

        // Fetch trash notes
        $trashNotes = $this->noteService->getTrashedNotesByAccountId($accountId);

        // Pass data to the view
        $this->Views('home-user-trash', [
            'status' => true,
            'trashNotes' => $trashNotes,
            'message' => 'Get trashed notes for this account successfully'
        ]);

    }

    public function uploadAvatar($user) {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            $fileTmpPath = $_FILES['avatar']['tmp_name'];
            $accountId = $user->accountId ?? null;
            $email = $user->email ?? null;
            $oldImage = $user->profilePicture ?? null;

            if ($oldImage) {
                deleteImageByImageUrl($oldImage);
            }

            $uploadResponse = uploadAvatarToCloudinary($fileTmpPath);

            if ($uploadResponse['status'] === true) {
                $result = $this->accountService->updateProfilePictureByAccountIdAndEmail($accountId, $email, $uploadResponse['url']);
                if ($result['status'] === true) {

                    setcookie('access_token', $result['token'], [
                        'expires' => time() + 3600, // 1 hour (match your JWT expiry)
                        'path' => '/',
//                        'domain' => 'pernote.id.vn',
                        'secure' => true, // Set to true if using HTTPS
                        'httponly' => true,
                        'samesite' => 'None'
                    ]);

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

    public function getPreferencesByAccountId($user) {
        header('Content-Type: application/json');

        $accountId = $user->accountId ?? null;
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

    public function updatePreference($user) {
        header('Content-Type: application/json');
        $accountId = $user->accountId;
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        if (!empty($data['theme']) || !empty($data['userName']) || !empty($data['noteFont']) || !empty($data['noteColor'])) {
            $result = $this->accountService->updatePreferenceByAccountId($accountId, $data['userName'], $data['theme'], $data['noteFont'], $data['noteColor']);

            if ($result['status'] === true) {
                http_response_code(200);

                setcookie('access_token', $result['token'], [
                    'expires' => time() + 3600, // 1 hour (match your JWT expiry)
                    'path' => '/',
//                    'domain' => 'pernote.id.vn',
                    'secure' => true, // Set to true if using HTTPS
                    'httponly' => true,
                    'samesite' => 'None'
                ]);

                echo json_encode([
                    'status' => true,
                    'userName' => $data['userName'],
                    'data' => $result['token'],
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

    public function addNewSharedEmail_POST($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $noteId = $data['noteId'] ?? null;
        $newEmail = $data['newSharedEmail'] ?? null;
        $email = $user->email;
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

    public function sharedEmailList($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $noteId = $data['noteId'] ?? null;
        $email = $user->email;

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
