<?php

//use \Dotenv\Dotenv;

//    session_set_cookie_params(86400);   // 1 day
//    session_start();
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

require_once __DIR__ . '/vendor/autoload.php'; // Autoload everything

// Load .env
//$dotenv = Dotenv::createImmutable(__DIR__ . ''); // Adjust path
//$dotenv->load();
//$jwtConfig = include __DIR__ . '/app/core/jwt.php';

//require_once __DIR__ . '/vendor/autoload.php';
    include "./config/DatabaseManager.php";
    include "./app/models/index.php";
//    include "./app/core/JWTHandler.php";
    include "./app/repository/index.php";
    include "./app/services/index.php";
    include "./route/index.php";
?>

<?php
//
//$connect = mysqli_connect(
//    'mysql', # service name
//    'user', # username
//    'userpass', # password
//    'note_manager' # db table
//);
//
//$table_name = "accounts";
//
//$query = "SELECT * FROM $table_name";
//
//$response = mysqli_query($connect, $query);
//
//echo "<strong>$table_name: </strong>";
//while($i = mysqli_fetch_assoc($response))
//{
//    echo "<p>".$i['id']."</p>";
//    echo "<p>".$i['name']."</p>";
//    echo "<p>".$i['password']."</p>";
//    echo "<p>".$i['role']."</p>";
//    echo "<hr>";
//}
//?>