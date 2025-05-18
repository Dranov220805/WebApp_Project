<?php
include_once "./app/controllers/Base/BaseController.php";
class AuthController extends BaseController {
    private AuthService $authService;

    public function __construct() {
        parent::__construct();
        $this->authService = new AuthService();
    }

    public function index() {
        include "./views/log/login.php";
    }

    public function login_POST() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        if (!empty($data['email']) && !empty($data['password'])) {
            $authService = new AuthService();
            $result = $authService->login($data['email'], $data['password']);

            if ($result['status'] === true) {

                $_SESSION['accountId'] = $result['accountId'];
                $_SESSION['userName'] = $result['userName'];
                $_SESSION['email'] = $result['email'];
                $_SESSION['profilePicture'] = $result['profilePicture'];
                $_SESSION['refreshToken'] = $result['refreshToken'];
                $_SESSION['expiredTime'] = $result['expiredTime'];
                $_SESSION['roleId'] = $result['roleId'];
                $_SESSION['isDarkTheme'] = $result['isDarkTheme'];
                $_SESSION['fontSize'] = $result['fontSize'];
                $_SESSION['noteColor'] = $result['noteColor'];
                $_SESSION['isVerified'] = $result['isVerified'];

                echo json_encode([
                    'status' => true,
                    'data' => $result,
                    'message' => $result['message']
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    'status' => false,
                    'message' => $result['message']
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => 'Missing login credentials'
            ]);
        }
    }

    public function sendVerificationLink_POST() {
        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];
            $activation_token = $this->authService->sendVerificationLink($_SESSION['email']);
            if (sendActivationEmail($email, $activation_token)) {
                http_response_code(200);
                echo json_encode([
                    'status' => true,
                    'message' => 'Send email verification successfully'
                ]);
            }
        }
    }

    public function accountActivate($activation_token) {
        $result = $this->authService->accountActivate($activation_token);
        if ($result) {
            echo json_encode([
                'status' => true,
                'message' => 'Account activated'
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'message' => 'Account activation failed'
            ]);
        }
    }

    public function forgotPassword() {
        include "./views/log/forgot-password.php";
    }

    public function resetPassword() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        if (empty($data['email'])) {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => 'Missing email'
            ]);
        }

        $result = $this->authService->resetPasswordByEmail($data['email']);

        if ($result['status'] === true) {
            echo json_encode([
                'status' => true,
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

    public function changePassword($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);
        $email = $user['email'];
        $currPassword = $data['currentPassword'];
        $password = $data['newPassword'];

        if (empty($email) || empty($data['currentPassword']) || empty($data['newPassword'])) {
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'email' => $email,
                'currentPassword' => $data['currentPassword'],
                'newPassword' => $data['newPassword'],
                'message' => 'Change password failed'
            ]);
        } else {

            $checkCredentials = $this->authService->login($email, $currPassword);

            if($checkCredentials['status'] === true) {
                $result = $this->authService->updatePasswordByEmail($email, $password);

                if ($result['status'] === true) {
                    echo json_encode([
                        'status' => true,
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
                    'message' => 'Current password is wrong'
                ]);
            }
        }
    }

    public function logout() {

        $_SESSION = [];

        session_destroy();

        // Perform the redirect
        header('Location: /home');
        exit();
    }

    public function checkVerification($user) {
        $email = $user['email'];

        $result = $this->authService->checkVerification($email);

        if($result['status'] === true) {
            echo json_encode([
                'status' => true,
                'message' => $result['message']
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'message' => $result['message']
            ]);
        }
    }

}

?>
