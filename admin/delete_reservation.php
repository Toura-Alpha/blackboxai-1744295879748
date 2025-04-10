<?php
session_start();
require_once 'db_connect.php';
require_once 'auth_check.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Reservation #$id deleted successfully";
    } else {
        $_SESSION['error_message'] = "Failed to delete reservation #$id";
    }
    
    header("Location: reservations.php");
    exit();
}

header("Location: reservations.php");
exit();
?>
