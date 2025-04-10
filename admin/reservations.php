<?php
session_start();
require_once 'db_connect.php';
require_once 'auth_check.php';

// Handle status updates
if (isset($_POST['update_status'])) {
    $reservation_id = (int)$_POST['reservation_id'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $reservation_id);
    $stmt->execute();
}

// Get all reservations
$reservations = $conn->query("SELECT * FROM reservations ORDER BY date DESC, time DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Management | Savory Bites</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'sidebar.php'; ?>
    
    <div class="flex-1 overflow-auto">
        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Reservation Management</h1>
                <div class="flex space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="Search reservations..." 
                               class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guests</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($row = $reservations->fetch_assoc()): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium"><?= htmlspecialchars($row['name']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div><?= htmlspecialchars($row['email']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($row['phone']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div><?= date('M j, Y', strtotime($row['date'])) ?></div>
                                    <div class="text-sm text-gray-500"><?= date('g:i A', strtotime($row['time'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= $row['guests'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form method="POST" class="flex items-center space-x-2">
                                        <input type="hidden" name="reservation_id" value="<?= $row['id'] ?>">
                                        <select name="status" onchange="this.form.submit()"
                                            class="border rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-red-500
                                            <?= $row['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                               ($row['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                            <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="confirmed" <?= $row['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                            <option value="cancelled" <?= $row['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="viewDetails(<?= $row['id'] ?>)" 
                                            class="text-red-600 hover:text-red-900 mr-3">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button onclick="confirmDelete(<?= $row['id'] ?>)" 
                                            class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation Details Modal -->
    <div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center border-b p-4">
                <h3 class="text-lg font-semibold">Reservation Details</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4" id="reservationDetails">
                <!-- Details will be loaded here via AJAX -->
            </div>
            <div class="border-t p-4 flex justify-end">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Close</button>
            </div>
        </div>
    </div>

    <script>
    function viewDetails(id) {
        fetch('get_reservation.php?id=' + id)
            .then(response => response.text())
            .then(data => {
                document.getElementById('reservationDetails').innerHTML = data;
                document.getElementById('detailsModal').classList.remove('hidden');
            });
    }

    function closeModal() {
        document.getElementById('detailsModal').classList.add('hidden');
    }

    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this reservation?')) {
            window.location.href = 'delete_reservation.php?id=' + id;
        }
    }
    </script>
</body>
</html>
