<?php
session_start();
require_once 'db_connect.php';
require_once 'auth_check.php';

// Validate input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'Invalid menu item ID';
    header("Location: menu.php");
    exit();
}

$id = (int)$_GET['id'];

try {
    // First verify item exists
    $stmt = $conn->prepare("SELECT id FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        throw new Exception('Menu item not found');
    }

    // Delete the item
    $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Menu item #$id deleted successfully";
    } else {
        throw new Exception('Database error');
    }

} catch (Exception $e) {
    $_SESSION['error'] = "Failed to delete menu item: " . $e->getMessage();
}

header("Location: menu.php");
exit();
?>
