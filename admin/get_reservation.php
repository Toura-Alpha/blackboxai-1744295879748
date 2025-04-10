<?php
require_once 'db_connect.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();
    
    if ($reservation) {
        echo '<div class="space-y-4">';
        echo '<div><span class="font-semibold">Name:</span> '.htmlspecialchars($reservation['name']).'</div>';
        echo '<div><span class="font-semibold">Email:</span> '.htmlspecialchars($reservation['email']).'</div>';
        echo '<div><span class="font-semibold">Phone:</span> '.htmlspecialchars($reservation['phone']).'</div>';
        echo '<div><span class="font-semibold">Date:</span> '.date('F j, Y', strtotime($reservation['date'])).'</div>';
        echo '<div><span class="font-semibold">Time:</span> '.date('g:i A', strtotime($reservation['time'])).'</div>';
        echo '<div><span class="font-semibold">Guests:</span> '.$reservation['guests'].'</div>';
        if (!empty($reservation['special_requests'])) {
            echo '<div><span class="font-semibold">Special Requests:</span> '.htmlspecialchars($reservation['special_requests']).'</div>';
        }
        echo '<div><span class="font-semibold">Status:</span> '.ucfirst($reservation['status']).'</div>';
        echo '<div><span class="font-semibold">Booked On:</span> '.date('M j, Y g:i A', strtotime($reservation['created_at'])).'</div>';
        echo '</div>';
    } else {
        echo '<p class="text-red-500">Reservation not found.</p>';
    }
}
?>
