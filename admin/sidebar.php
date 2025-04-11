<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg">
    <div class="flex items-center justify-center h-16 px-4 bg-red-600">
        <h1 class="text-white font-bold text-xl">Savory Bites Admin</h1>
    </div>
    <nav class="mt-6">
        <div class="px-4">
            <div class="mb-4">
                <a href="dashboard.php" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-red-50 <?= $current_page === 'dashboard.php' ? 'bg-red-50 text-red-600' : '' ?>">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="mb-4">
                <a href="menu.php" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-red-50 <?= $current_page === 'menu.php' ? 'bg-red-50 text-red-600' : '' ?>">
                    <i class="fas fa-utensils mr-3"></i>
                    <span>Menu Items</span>
                </a>
            </div>
            <div class="mb-4">
                <a href="reservations.php" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-red-50 <?= $current_page === 'reservations.php' ? 'bg-red-50 text-red-600' : '' ?>">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    <span>Reservations</span>
                </a>
            </div>
        </div>
    </nav>
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t">
        <a href="logout.php" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-red-50">
            <i class="fas fa-sign-out-alt mr-3"></i>
            <span>Logout</span>
        </a>
    </div>
</div>
