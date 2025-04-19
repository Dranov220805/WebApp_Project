<?php

class RegisterMiddleWare {
    private RegisterController $registerController;

    public function __construct() {
        $this->registerController = new RegisterController();
    }

    public function index() {
        $this->registerController->index();
    }

    public function register_POST() {
        header('Content-Type: application/json');

        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        // Check if all fields are filled
        if (!empty($data['email']) && !empty($data['username']) && !empty($data['password'])) {
            // Attempt to create the account
            $registerService = new RegisterService();
            $result = $registerService->register($data['username'], $data['password'], $data['email']);

            if ($result) {
                // Send a success response
                http_response_code(200);
                echo json_encode([
                    'status' => true,
                    'accessToken' => $result['accessToken'],
                    'roleId' => $result['roleId'],
                    'username' => $result['username'],
                    'email' => $result['email'],
                    'message' => 'Your account has been created'
                ]);
            } else {
                // Send an error response if account creation failed
                http_response_code(500);
                echo json_encode([
                    'status' => false,
                    'message' => 'Account creation failed. Please try again later.'
                ]);
            }
        } else {
            // Send an error response if fields are missing
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => 'Please fill in all required fields'
            ]);
        }

        // Ensure no further output is sent
        exit();
    }
}
?>
