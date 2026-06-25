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

$sql = "SELECT id, name, email FROM users";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode(['success' => true, 'users' => $users]);
} else {
    echo json_encode(['success' => true, 'users' => []]);
}

$conn->close();
?>