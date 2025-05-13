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
                // Access token -> 1 hour
                setcookie('access_token', $result['access_token'], [
                    'expires' => time() + 3600, // 1 hour
                    'path' => '/',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);

                // Refresh token (long-lived) -> 7 days
                setcookie('refresh_token', $result['refresh_token'], [
                    'expires' => time() + (7 * 24 * 60 * 60),
                    'path' => '/',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);

                echo json_encode([
                    'status' => true,
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

    public function changePassword() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);
        $user = $GLOBALS['user'];
        $email = $user->email;
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

    public function logout()
    {
        // Clear the JWT cookie
        setcookie('access_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        setcookie('refresh_token', '', [
            'expires' => time() - (7 * 24 * 60 * 60),
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        // Perform the redirect
        header('Location: /home');
        exit();
    }

    public function checkVerification() {
        $user = $GLOBALS['user'];
        $email = $user->email;

        $result = $this->authService->checkVerification($email);

        if($result['status'] === true) {
            return json_encode([
                'status' => true,
                'message' => $result['message']
            ]);
        } else {
            return json_encode([
                'status' => false,
                'message' => $result['message']
            ]);
        }
    }

}

?>
