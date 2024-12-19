<?php
$host = "localhost:3306";
$user = "yaw.budu";
$password = "Agyayaw@2024"; 
$database = "webtech_fall2024_yaw_budu";;

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>