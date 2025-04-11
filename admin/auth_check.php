<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
    header('Location: login.php');
    exit();
}

// Add security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Database connection
require_once 'db_connect.php';

// Verify admin still exists in database
$stmt = $conn->prepare("SELECT id FROM admin_users WHERE id = ? AND username = ?");
$stmt->bind_param("is", $_SESSION['admin_id'], $_SESSION['admin_username']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    // Admin no longer exists - destroy session
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
$stmt->close();
?>
