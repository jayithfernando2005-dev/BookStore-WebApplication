<?php
session_start();
$host = 'localhost';
$user = 'root'; 
$password = ''; 
$dbname = 'booknest'; 
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $session_id = session_id();

    if ($id) {
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
        $stmt->bind_param("is", $id, $session_id);
        $success = $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => $success, 'message' => $success ? 'Item removed' : 'Failed to remove item']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>