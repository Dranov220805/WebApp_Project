<?php

use \Dotenv\Dotenv;

//    session_set_cookie_params(86400);   // 1 day
//    session_start();
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

require_once __DIR__ . '/vendor/autoload.php'; // Autoload everything

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__ . ''); // Adjust path
$dotenv->load();
//$jwtConfig = include __DIR__ . '/app/core/jwt.php';

require_once __DIR__ . '/vendor/autoload.php';
    include "./config/DatabaseManager.php";
    include "./app/models/index.php";
//    include "./app/core/JWTHandler.php";
    include "./app/repository/index.php";
    include "./app/services/index.php";
    include "./route/index.php";
?>