<?php
//
//
//    include "./config/Database.php";
//    include "./app/models/index.php";
//    include "./app/repository/index.php";
//    include "./app/services/index.php";
//    include "./route/index.php";
//?>

<?php

$connect = mysqli_connect(
    'db', # service name
    'php_docker', # username
    'password', # password
    'note_manager' # db table
);

$table_name = "accounts";

$query = "SELECT * FROM $table_name";

$response = mysqli_query($connect, $query);

echo "<strong>$table_name: </strong>";
while($i = mysqli_fetch_assoc($response))
{
    echo "<p>".$i['id']."</p>";
    echo "<p>".$i['name']."</p>";
    echo "<p>".$i['password']."</p>";
    echo "<p>".$i['email']."</p>";
    echo "<p>".$i['role']."</p>";
    echo "<hr>";
}