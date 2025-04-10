<?php
session_start();
require_once 'db_connect.php';
require_once 'auth_check.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item'])) {
        // Handle new menu item addition
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $category = trim($_POST['category']);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        $stmt = $conn->prepare("INSERT INTO menu_items (name, description, price, category, is_featured) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $name, $description, $price, $category, $is_featured);
        $stmt->execute();
        
        $_SESSION['message'] = "Menu item added successfully";
        header("Location: menu.php");
        exit();
    }
    elseif (isset($_POST['update_item'])) {
        // Handle menu item update
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $category = trim($_POST['category']);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        $stmt = $conn->prepare("UPDATE menu_items SET name=?, description=?, price=?, category=?, is_featured=? WHERE id=?");
        $stmt->bind_param("ssdsii", $name, $description, $price, $category, $is_featured, $id);
        $stmt->execute();
        
        $_SESSION['message'] = "Menu item updated successfully";
        header("Location: menu.php");
        exit();
    }
}

// Get all menu items
$menu_items = $conn->query("SELECT * FROM menu_items ORDER BY category, name");
$categories = $conn->query("SELECT DISTINCT category FROM menu_items ORDER BY category");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management | Savory Bites</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'sidebar.php'; ?>
    
    <div class="flex-1 overflow-auto">
        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Menu Management</h1>
                <button onclick="openAddModal()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                    <i class="fas fa-plus mr-2"></i> Add New Item
                </button>
            </div>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= $_SESSION['message'] ?>
                    <?php unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($item = $menu_items->fetch_assoc()): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php if (!empty($item['image_path'])): ?>
                                        <div class="flex-shrink-0 h-10 w-10 mr-3">
                                            <img class="h-10 w-10 rounded-full object-cover" 
                                                 src="<?= htmlspecialchars($item['image_path']) ?>" 
                                                 alt="<?= htmlspecialchars($item['name']) ?>">
                                        </div>
                                        <?php endif; ?>
                                        <div class="font-medium"><?= htmlspecialchars($item['name']) ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($item['description']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    $<?= number_format($item['price'], 2) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?= htmlspecialchars($item['category']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($item['is_featured']): ?>
                                        <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Featured</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="openEditModal(<?= $item['id'] ?>)" 
                                            class="text-red-600 hover:text-red-900 mr-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button onclick="confirmDelete(<?= $item['id'] ?>)" 
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

    <!-- Add Item Modal -->
    <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center border-b p-4">
                <h3 class="text-lg font-semibold">Add New Menu Item</h3>
                <button onclick="closeAddModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" enctype="multipart/form-data" class="p-4">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Price</label>
                    <input type="number" name="price" step="0.01" min="0" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Category</label>
                    <input type="text" name="category" list="categories" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-500">
                    <datalist id="categories">
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($cat['category']) ?>">
                        <?php endwhile; ?>
                    </datalist>
                </div>
                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="is_featured" id="is_featured"
                           class="mr-2 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="is_featured" class="text-gray-700">Featured Item</label>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Item Image</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="border-t p-4 flex justify-end">
                    <button type="button" onclick="closeAddModal()" 
                            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 mr-2">Cancel</button>
                    <button type="submit" name="add_item"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Add Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center border-b p-4">
                <h3 class="text-lg font-semibold">Edit Menu Item</h3>
                <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" class="p-4" id="editForm">
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" id="edit_name" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="edit_description" rows="3"
                              class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Price</label>
                    <input type="number" name="price" id="edit_price" step="0.01" min="0" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Category</label>
                    <input type="text" name="category" id="edit_category" list="categories" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="is_featured" id="edit_is_featured"
                           class="mr-2 h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                    <label for="edit_is_featured" class="text-gray-700">Featured Item</label>
                </div>
                <div class="border-t p-4 flex justify-end">
                    <button type="button" onclick="closeEditModal()" 
                            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 mr-2">Cancel</button>
                    <button type="submit" name="update_item"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Update Item</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Modal functions
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    function openEditModal(id) {
        fetch('get_menu_item.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_name').value = data.name;
                document.getElementById('edit_description').value = data.description;
                document.getElementById('edit_price').value = data.price;
                document.getElementById('edit_category').value = data.category;
                document.getElementById('edit_is_featured').checked = data.is_featured == 1;
                document.getElementById('editModal').classList.remove('hidden');
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this menu item?')) {
            window.location.href = 'delete_menu_item.php?id=' + id;
        }
    }
    </script>
</body>
</html>
