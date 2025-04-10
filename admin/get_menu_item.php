<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Missing item ID');
    }

    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Menu item not found');
    }

    $item = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'data' => $item
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
