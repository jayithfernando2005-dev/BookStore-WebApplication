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
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $author = isset($_POST['author']) ? trim($_POST['author']) : '';
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0.0;
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';
    $session_id = session_id();

    if ($book_id && $title && $author && $price && $image) {
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND book_id = ?");
        $stmt->bind_param("si", $session_id, $book_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + 1;
            $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $update_stmt->bind_param("ii", $new_quantity, $row['id']);
            $success = $update_stmt->execute();
            $update_stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO cart (session_id, book_id, title, author, price, image, quantity) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("sissds", $session_id, $book_id, $title, $author, $price, $image);
            $success = $stmt->execute();
        }
        $stmt->close();

        echo json_encode(['success' => $success, 'message' => $success ? 'Item added to cart' : 'Failed to add item']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>