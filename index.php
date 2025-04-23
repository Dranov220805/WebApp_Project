<?php

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

require_once __DIR__ . '/vendor/autoload.php'; // Autoload everything

require_once __DIR__ . '/vendor/autoload.php';
    include "./config/DatabaseManager.php";
    include "./app/models/index.php";
    include "./app/repository/index.php";
    include "./app/services/index.php";
    include "./route/index.php";
?>