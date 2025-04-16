<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
//use \Dotenv\Dotenv;
//
//// Load .env file
//try {
//    $dotenv = Dotenv::createImmutable('.');
//    $dotenv->load();
//} catch (\Exception $e) {
//    // Handle error (log it or display a message)
//    echo 'Error loading .env file: ' . $e->getMessage();
//}

class JWTHandler {
    private $secret;
    private $issuer;
    private $audience;
    private $accessTokenExp;

    public function __construct() {
        $config = include 'jwt.php';
        $this->secret = $config['secret'];
        $this->issuer = $config['issuer'];
        $this->audience = $config['audience'];
        $this->accessTokenExp = $config['accessTokenExp'];
//        $this->secret = "123456asdlksaduoasdpipmi987923849039ncqw8n0askdaosmp9";
//        $this->issuer = "http://localhost";
//        $this->audience = "http://localhost";
//        $this->accessTokenExp = 600;
    }

    public function generateAccessToken($payload) {
        $issuedAt = time();
        $token = [
            'iss' => $this->issuer,
            'aud' => $this->audience,
            'iat' => $issuedAt,
            'exp' => $issuedAt + $this->accessTokenExp,
            'data' => $payload
        ];
        return JWT::encode($token, $this->secret, 'HS256');
    }

    public function decodeToken($token) {
        return JWT::decode($token, new Key($this->secret, 'HS256'));
    }
}
