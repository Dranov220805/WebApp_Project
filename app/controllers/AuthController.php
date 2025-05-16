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
<<<<<<< Updated upstream
=======
<<<<<<< Updated upstream
<<<<<<< Updated upstream
>>>>>>> Stashed changes
                // Access token -> 1 hour
                setcookie('access_token', $result['access_token'], [
                    'expires' => time() + 3600, // 1 hour
                    'path' => '/',
//                    'domain' => 'pernote.id.vn',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'None'
                ]);

                // Refresh token (long-lived) -> 7 days
                setcookie('refresh_token', $result['refresh_token'], [
                    'expires' => time() + (7 * 24 * 60 * 60),
                    'path' => '/',
//                    'domain' => 'pernote.id.vn',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'None'
                ]);
<<<<<<< Updated upstream
=======
=======

=======

>>>>>>> Stashed changes
                $_SESSION['accountId'] = $result['accountId'];
                $_SESSION['userName'] = $result['userName'];
                $_SESSION['email'] = $result['email'];
                $_SESSION['profilePicture'] = $result['profilePicture'];
                $_SESSION['refreshToken'] = $result['refreshToken'];
                $_SESSION['expiredTime'] = $result['expiredTime'];
                $_SESSION['roleId'] = $result['roleId'];
                $_SESSION['isDarkTheme'] = $result['isDarkTheme'];
                $_SESSION['isVerified'] = $result['isVerified'];

//                setcookie('access_token', $result['access_token'], [
//                    'expires' => time() + 3600, // 1 hour
//                    'path' => '/',
//                    'secure' => true,
//                    'httponly' => true,
//                    'samesite' => 'None'
//                ]);
//
//                setcookie('refresh_token', $result['refresh_token'], [
//                    'expires' => time() + (7 * 24 * 60 * 60),
//                    'path' => '/',
//                    'secure' => true,
//                    'httponly' => true,
//                    'samesite' => 'None'
//                ]);
<<<<<<< Updated upstream
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
>>>>>>> Stashed changes

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

    public function changePassword($user) {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);
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

<<<<<<< Updated upstream
=======
<<<<<<< Updated upstream
<<<<<<< Updated upstream
>>>>>>> Stashed changes
    public function logout()
    {
        // Clear the JWT cookie
        setcookie('access_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
//            'domain' => 'pernote.id.vn',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None'
        ]);

        setcookie('refresh_token', '', [
            'expires' => time() - (7 * 24 * 60 * 60),
            'path' => '/',
//            'domain' => 'pernote.id.vn',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None'
        ]);
<<<<<<< Updated upstream
=======
=======
    public function logout() {

        $_SESSION = [];

        session_destroy();
>>>>>>> Stashed changes
=======
    public function logout() {

        $_SESSION = [];

        session_destroy();
>>>>>>> Stashed changes
>>>>>>> Stashed changes

        // Perform the redirect
        header('Location: /home');
        exit();
    }

    public function checkVerification($user) {
        $email = $user->email;

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
