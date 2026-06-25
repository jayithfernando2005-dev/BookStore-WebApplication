<?php
ini_set('session.cookie_lifetime', 0); 
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "booknest";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header('Content-Type: application/json');

$_SESSION = [];
session_destroy();

echo json_encode(['success' => true, 'redirect' => '../index.html']);
?>