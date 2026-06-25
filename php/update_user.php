
<?php
include 'db_connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $profile_image = $_FILES['profile_image'] ?? null;

    if (empty($name) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Name and email are required']);
        exit;
    }

    $file_name = null;
    if ($profile_image && $profile_image['size'] > 0) {
        $upload_dir = '../BookPages/img/books/';
        $file_ext = pathinfo($profile_image['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_ext;
        $file_path = $upload_dir . $file_name;

        if (!move_uploaded_file($profile_image['tmp_name'], $file_path)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
            exit;
        }
    }

    $sql = "UPDATE users SET name = ?, email = ?, bio = ?" . ($file_name ? ", profile_image = ?" : "") . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($file_name) {
        $stmt->bind_param('ssssi', $name, $email, $bio, $file_name, $user_id);
    } else {
        $stmt->bind_param('sssi', $name, $email, $bio, $user_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request or not logged in']);
}
$conn->close();
?>