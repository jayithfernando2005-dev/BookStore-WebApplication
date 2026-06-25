<?php
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

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title  = $_POST["bookName"];
    $author = $_POST["bookAuthor"];
    $price  = $_POST["bookPrice"];

    $image_url = "";
    if (isset($_FILES["bookImage"]) && $_FILES["bookImage"]["error"] === 0) {
        $targetDir = "../img/uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $fileName = basename($_FILES["bookImage"]["name"]);
        $targetFile = $targetDir . time() . "_" . $fileName;

        if (move_uploaded_file($_FILES["bookImage"]["tmp_name"], $targetFile)) {
            $image_url = $targetFile;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO books (title, author, price, image_path) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $author, $price, $image_url]);

            header('Content-Type: application/json');
        echo json_encode([
            "success" => true,
            "message" => "Book added successfully!"
        ]);
        exit;

}
