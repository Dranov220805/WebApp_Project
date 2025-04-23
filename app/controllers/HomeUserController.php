<?php
include_once "./app/controllers/Base/BaseController.php";
class HomeUserController extends BaseController{
    private HomeUserService $homeUserService;
    private NoteService $noteService;

    public function __construct(){
        parent::__construct();
        $this->homeUserService = new HomeUserService();
        $this->noteService = new NoteService();
    }
    public function index()
    {
        $accountId = $_SESSION['accountId'] ?? null;
        $intPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = isset($_GET['limit']) ? $_GET['limit'] : 10;

        // Basic validation
        if (!$accountId) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Missing accountId']);
            return;
        }

        $offset = ($intPage - 1) * $perPage;

        // Fetch pinned notes
        $pinnedNotes = $this->noteService->getPinnedNotesByAccountId($accountId);

        // Fetch other notes
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
    public function homeArchive() {
        $content = 'home-user-archive';
        $footer = 'home';
        include "./views/layout/index.php";
    }
    public function homeTrash() {
        $content = 'home-user-trash';
        $footer = 'home';
        include "./views/layout/index.php";
    }
    public function userPreference() {
        $content = 'home-user-preference';
        $footer = 'home';
        include "./views/layout/index.php";
    }
}

?>