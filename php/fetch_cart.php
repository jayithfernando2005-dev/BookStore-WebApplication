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

$session_id = session_id();
$stmt = $conn->prepare("SELECT id, book_id, title, author, price, quantity, image FROM cart WHERE session_id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
    exit;
}
$stmt->bind_param("s", $session_id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Query execution failed: ' . $stmt->error]);
    exit;
}
$result = $stmt->get_result();
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
echo json_encode(['success' => true, 'items' => $items]);

$stmt->close();
$conn->close();
?>