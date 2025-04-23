<?php

use \Dotenv\Dotenv;

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

require_once __DIR__ . '/vendor/autoload.php'; // Autoload everything

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__ . '');
$dotenv->load();

require_once __DIR__ . '/vendor/autoload.php';
    include "./config/DatabaseManager.php";
    include "./app/models/index.php";
    include "./app/repository/index.php";
    include "./app/services/index.php";
    include "./route/index.php";
?>