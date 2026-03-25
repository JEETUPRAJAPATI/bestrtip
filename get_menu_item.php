<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$item_id = $_GET['id'] ?? '';

if (empty($item_id) || !is_numeric($item_id)) {
    echo '<div class="text-red-500">Invalid menu item ID</div>';
    exit();
}

$db = getDbInstance();

// Get menu item details
$db->where('id', $item_id);
$item = $db->getOne('restaurant_menu');

if (!$item) {
    echo '<div class="text-red-500">Menu item not found</div>';
    exit();
}

// Get all categories for dropdown
$db->where('restaurant_id', $item['restaurant_id']);
$categories = $db->get('menu_categories');
?>

<form id="editMenuItemForm" class="space-y-4">
    <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">

    <div>
        <label for="edit_item_name" class="block text-sm font-medium text-gray-700">Item Name *</label>
        <input type="text" id="edit_item_name" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div>
        <label for="edit_category_id" class="block text-sm font-medium text-gray-700">Category *</label>
        <select id="edit_category_id" name="category_id" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= $category['id'] == $item['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['category']) ?> - <?= htmlspecialchars($category['subcategory']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="edit_type" class="block text-sm font-medium text-gray-700">Type *</label>
        <select id="edit_type" name="type" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="Veg" <?= $item['type'] == 'Veg' ? 'selected' : '' ?>>Vegetarian</option>
            <option value="Non-Veg" <?= $item['type'] == 'Non-Veg' ? 'selected' : '' ?>>Non-Vegetarian</option>
        </select>
    </div>

    <div>
        <label for="edit_price" class="block text-sm font-medium text-gray-700">Price (₹) *</label>
        <input type="number" step="0.01" min="0" id="edit_price" name="price" value="<?= htmlspecialchars($item['price']) ?>" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div class="flex justify-end space-x-3">
        <button type="button" onclick="window.location.reload()"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-times mr-2"></i> Cancel
        </button>
        <button type="submit"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-save mr-2"></i> Update Item
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#editMenuItemForm').submit(function(e) {
            e.preventDefault();

            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Updating...');

            $.ajax({
                url: 'update_menu_item.php',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.status === 'success') {
                            alert(data.message || 'Menu item updated successfully');
                            window.location.reload();
                        } else {
                            alert(data.message || 'Failed to update menu item');
                        }
                    } catch (e) {
                        alert('Menu item updated successfully');
                        window.location.reload();
                    }
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.statusText);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i> Update Item');
                }
            });
        });
    });
</script>