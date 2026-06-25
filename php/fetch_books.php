<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$host = 'localhost';
$db   = 'booknest';
$user = 'root'; 
$pass = '';     
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
$conn = new mysqli($host, $user, $pass, $db);


if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed: ' . $conn->connect_error]);
    exit();
}
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

$sql = "SELECT id, title, author, price, image_path FROM books";
$result = $conn->query($sql);

$books = []; 

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    echo json_encode(['success' => true, 'books' => $books]);
} else {
    echo json_encode(['success' => false, 'message' => 'Query failed']);
}

$conn->close();
exit();
