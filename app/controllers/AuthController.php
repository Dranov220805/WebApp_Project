<?php

class AuthController {
    private AuthService $authService;

    public function __construct() {
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
                echo json_encode([
                    'status' => true,
                    'roleId' => $result['roleId'],
                    'userName' => $result['userName'],
                    'email' => $result['email'],
                    'last_activity' => $result['last_activity'],
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

    public function checkVerification() {
        $email = $_SESSION['email'];

        $result = $this->authService->checkVerification($email);

        if($result['status'] === true) {
            return [
                'status' => true,
                'message' => $result['message']
            ];
        } else {
            return [
                'status' => false,
                'message' => $result['message']
            ];
        }

    }

    public function accountActivate($activation_token) {
        $result = $this->authService->accountActivate($activation_token);
        if ($result) {
            $login_link = "http://localhost:8080/log/login";
            $body = <<<EOD
<!DOCTYPE html>
<html>
<head>
    <title>Email Confirmation</title>
</head>
<body style='margin-top:20px;'>
    <table class='body-wrap' style='font-family: "Helvetica Neue",Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;' bgcolor='#f6f6f6'>
        <tbody>
            <tr>
                <td valign='top'></td>
                <td class='container' width='600' valign='top'>
                    <div class='content' style='padding: 20px;'>
                        <table class='main' width='100%' cellpadding='0' cellspacing='0' style='border-radius: 3px; background-color: #fff; margin: 0; border: 1px solid #e9e9e9;' bgcolor='#fff'>
                            <tbody>
                                <tr>
                                    <td class='' style='font-size: 16px; vertical-align: top; color: #fff; font-weight: 500; text-align: center; background-color: #38414a; padding: 20px;' align='center' bgcolor='#71b6f9' valign='top'>
                                        <a href='#' style='font-size:32px;color:#fff;text-decoration: none;'>Hi there!</a> <br>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='content-wrap' style='padding: 20px;' valign='top'>
                                        <table width='100%' cellpadding='0' cellspacing='0'>
                                            <tbody>
                                                <tr>
                                                    <td class='content-block' style='padding: 0 0 20px;' valign='top'>
                                                       Your account has been activated, use the below button to log in:
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class='content-block' style='text-align: center;' valign='top'>
                                                        <a href="$login_link" class='btn-success' style='font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; border-radius: 5px; background-color: #2dd100; padding: 8px 16px; display: inline-block;'>Go lo login page</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
                <td valign='top'></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
EOD;

            return $body;
        } else {
            echo json_encode([
                'status' => false,
                'message' => $result
            ]);
        }
    }

    public function logout() {
        $_SESSION = []; // Clear session variables

        session_destroy(); // Fully destroy the session

        // Perform the redirect
        header('Location: /home'); // Make sure no output is sent before this
        exit();
    }

    public function refreshToken() {
        $this->authService->refreshSession();
    }

    public function register() {
    }
}

?>
