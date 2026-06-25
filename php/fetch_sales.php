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
$sql = "SELECT id, session_id, book_id, title, author, price, quantity, total, sale_date FROM sales";
$result = $conn->query($sql);
$sales = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sales[] = $row;
    }
    echo json_encode(['success' => true, 'sales' => $sales]);
} else {
    echo json_encode(['success' => false, 'message' => 'No sales found']);
}
$conn->close();
?>
