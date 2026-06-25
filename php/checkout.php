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
    $session_id = session_id();

    $stmt = $conn->prepare("SELECT book_id, title, author, price, quantity, image FROM cart WHERE session_id = ?");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $success = true;
    while ($row = $result->fetch_assoc()) {
        $total = $row['price'] * $row['quantity'];
        $insert_stmt = $conn->prepare("INSERT INTO sales (session_id, book_id, title, author, price, quantity, total) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("sissdid", $session_id, $row['book_id'], $row['title'], $row['author'], $row['price'], $row['quantity'], $total);
        if (!$insert_stmt->execute()) {
            $success = false;
        }
        $insert_stmt->close();
    }
    $stmt->close();

    if ($success) {
        $delete_stmt = $conn->prepare("DELETE FROM cart WHERE session_id = ?");
        $delete_stmt->bind_param("s", $session_id);
        $success = $delete_stmt->execute();
        $delete_stmt->close();
    }

    echo json_encode(['success' => $success, 'message' => $success ? 'Checkout successful' : 'Checkout failed']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>