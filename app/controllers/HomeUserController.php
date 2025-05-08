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
        $user = $GLOBALS['user'];
        $accountId = $user->accountId;
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

            // Pass data to the view
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
    public function getUserLabel() {
        $user = $GLOBALS['user'];
        $accountId = $user->accountId;
        $labelNotes = $this->homeUserService->getLabelByAccountId($accountId);

        return $labelNotes;
    }
    public function homeLabel_POST($labelName) {
        $user = $GLOBALS['user'];
        $accountId = $user->accountId;

        // Basic validation
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
    public function homeArchive() {
        $content = 'home-user-archive';
        $footer = 'home';
        include "./views/layout/index.php";
    }
    public function homeTrash() {
        $user = $GLOBALS['user'];
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

    public function checkVerification() {
        $email = $_SESSION['email'];

        $result = $this->accountService->checkVerification($email);

        if($result['status'] === true) {
            return [
                'status' => true,
                'message' => $result['message']
            ];
        } else {
            return[
                'status' => false,
                'message' => $result['message']
            ];
        }
    }

    public function uploadAvatar() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            $fileTmpPath = $_FILES['avatar']['tmp_name'];
            $accountId = $GLOBALS['user']->accountId ?? null;
            $oldImage = $GLOBALS['user']->profilePicture ?? null;

            if ($oldImage) {
                deleteImageByImageUrl($oldImage);
            }

            $uploadResponse = uploadAvatarToCloudinary($fileTmpPath);

            if ($uploadResponse['status'] === true) {
                $result = $this->accountService->updateProfilePictureByAccountId($accountId, $uploadResponse['url']);
                if ($result['status'] === true) {

                    setcookie('jwt_token', $result['token'], [
                        'expires' => time() + 3600, // 1 hour (match your JWT expiry)
                        'path' => '/',
                        'secure' => true, // Set to true if using HTTPS
                        'httponly' => true,
                        'samesite' => 'Lax'
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

    public function getPreferencesByAccountId() {
        header('Content-Type: application/json');

        $accountId = $_SESSION['accountId'] ?? null;
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
        $user = $GLOBALS['user'];
        $accountId = $user->accountId;
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        if (!empty($data['theme']) || !empty($data['noteFont']) || !empty($data['noteColor'])) {
            $result = $this->accountService->updatePreferenceByAccountId($accountId, $data['theme'], $data['noteFont'], $data['noteColor']);

            if ($result['status'] === true) {
                http_response_code(200);

                setcookie('jwt_token', $result['token'], [
                    'expires' => time() + 3600, // 1 hour (match your JWT expiry)
                    'path' => '/',
                    'secure' => true, // Set to true if using HTTPS
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);

                echo json_encode([
                    'status' => true,
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

}
