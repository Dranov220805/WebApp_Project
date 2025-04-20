<?php

class RegisterController {
    private RegisterService $registerService;
    public function __construct() {
        $this->registerService = new RegisterService();
    }

    public function index() {
        $content = 'register';
        $footer = 'home';
        include "views/log/register.php";
    }

    public function register_POST() {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        // Check if all fields are filled
        if (!empty($data['email']) && !empty($data['username']) && !empty($data['password'])) {
            // Attempt to create the account
            $registerService = new RegisterService();
            $result = $registerService->register($data['username'], $data['password'], $data['email']);

            if ($result['status'] === true) {
                http_response_code(201);
                echo json_encode([
                    'status' => true,
                    'roleId' => $result['roleId'],
                    'username' => $result['username'],
                    'email' => $result['email'],
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
            // Send an error response if fields are missing
            http_response_code(400);
            echo json_encode([
                'status' => false,
                'message' => 'Please fill in all required fields'
            ]);
        }
    }
}