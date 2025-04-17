<?php

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

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
