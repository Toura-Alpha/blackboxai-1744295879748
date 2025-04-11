<?php
require_once 'auth_check.php';
require_once 'db_connect.php';

// Get stats for dashboard
$menu_items = $conn->query("SELECT COUNT(*) as count FROM menu_items")->fetch_assoc();
$reservations = $conn->query("SELECT COUNT(*) as count FROM reservations")->fetch_assoc();
$today_reservations = $conn->query("SELECT COUNT(*) as count FROM reservations WHERE date = CURDATE()")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Savory Bites</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'sidebar.php'; ?>
    
    <div class="flex-1 overflow-auto ml-64">
        <div class="p-8">
            <h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Menu Items Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700">Menu Items</h3>
                            <p class="text-3xl font-bold text-red-600"><?= $menu_items['count'] ?></p>
                        </div>
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <i class="fas fa-utensils text-xl"></i>
                        </div>
                    </div>
                    <a href="menu.php" class="mt-4 inline-block text-sm text-red-600 hover:text-red-800">
                        View all menu items <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <!-- Total Reservations Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700">Total Reservations</h3>
                            <p class="text-3xl font-bold text-blue-600"><?= $reservations['count'] ?></p>
                        </div>
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-calendar-alt text-xl"></i>
                        </div>
                    </div>
                    <a href="reservations.php" class="mt-4 inline-block text-sm text-blue-600 hover:text-blue-800">
                        View all reservations <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <!-- Today's Reservations Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700">Today's Reservations</h3>
                            <p class="text-3xl font-bold text-green-600"><?= $today_reservations['count'] ?></p>
                        </div>
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                    </div>
                    <a href="reservations.php?filter=today" class="mt-4 inline-block text-sm text-green-600 hover:text-green-800">
                        View today's reservations <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Recent Reservations -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold">Recent Reservations</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php
                            $recent_reservations = $conn->query("SELECT * FROM reservations ORDER BY date DESC, time DESC LIMIT 5");
                            while ($row = $recent_reservations->fetch_assoc()): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium"><?= htmlspecialchars($row['name']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($row['email']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div><?= date('M j, Y', strtotime($row['date'])) ?></div>
                                    <div class="text-sm text-gray-500"><?= date('g:i A', strtotime($row['time'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-full text-xs 
                                        <?= $row['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                           ($row['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
