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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $profile_image = $_FILES['profile_image'] ?? null;

    if (empty($name) || empty($email) || empty($password) || !$profile_image) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }

    $upload_dir = '../BookPages/img/books/';
    $file_ext = pathinfo($profile_image['name'], PATHINFO_EXTENSION);
    $file_name = uniqid() . '.' . $file_ext;
    $file_path = $upload_dir . $file_name;

    if (!move_uploaded_file($profile_image['tmp_name'], $file_path)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (name, email, password, profile_image, session_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $session_id = session_id();
    $stmt->bind_param('sssss', $name, $email, $hashed_password, $file_name, $session_id);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        echo json_encode(['success' => true, 'redirect' => '../index.html']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to register user']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
$conn->close();
?>