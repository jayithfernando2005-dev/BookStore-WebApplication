<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: signin.html");
    exit();
}
$user = $_SESSION['user'];
?>
