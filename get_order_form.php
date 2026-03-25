<?php
session_start();
require_once '../config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$order_id = $_GET['order_id'] ?? '';
$pid = decryptId($_GET['pid'] ?? '');
$db = getDbInstance();

// Get order details
$db->where('id', $order_id);
$order = $db->getOne('restaurant_orders');

if (!$order) {
    echo '<div class="text-red-500">Order not found</div>';
    exit();
}

// Get order items
$db->where('order_id', $order_id);
$db->join('restaurant_menu m', 'm.id=restaurant_order_items.menu_item_id', 'LEFT');
$orderItems = $db->get('restaurant_order_items', null, 'restaurant_order_items.*, m.item_name, m.price, m.type');

// Get restaurants for dropdown
$db->where('property_id', $pid);
$restaurants = $db->get('restaurants');

// Get menu items for selected restaurant
$db->where('restaurant_id', $order['restaurant_id']);
$menuItems = $db->get('restaurant_menu');
?>

<form id="editOrderForm" class="space-y-4">
    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="restaurant_id" class="block text-sm font-medium text-gray-700">Restaurant *</label>
            <select id="restaurant_id" name="restaurant_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Select Restaurant</option>
                <?php foreach ($restaurants as $restaurant): ?>
                    <option value="<?= $restaurant['id'] ?>" <?= $order['restaurant_id'] == $restaurant['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($restaurant['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="order_code" class="block text-sm font-medium text-gray-700">Order Code</label>
            <input type="text" id="order_code" value="<?= $order['order_code'] ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" readonly>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="guest_name" class="block text-sm font-medium text-gray-700">Guest Name *</label>
            <input type="text" id="guest_name" name="guest_name" value="<?= htmlspecialchars($order['guest_name']) ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div>
            <label for="table_no" class="block text-sm font-medium text-gray-700">Table No *</label>
            <input type="text" id="table_no" name="table_no" value="<?= htmlspecialchars($order['table_no']) ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
        <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Served" <?= $order['status'] == 'Served' ? 'selected' : '' ?>>Served</option>
            <option value="Cancelled" <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
    </div>

    <div class="mt-6">
        <h4 class="font-bold text-lg mb-2">Order Items</h4>
        <div id="orderItemsContainer">
            <?php foreach ($orderItems as $index => $item): ?>
                <div class="grid grid-cols-12 gap-2 mb-2 item-row">
                    <div class="col-span-5">
                        <select name="items[<?= $index ?>][menu_item_id]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 menu-item-select">
                            <option value="">Select Item</option>
                            <?php foreach ($menuItems as $menuItem): ?>
                                <option value="<?= $menuItem['id'] ?>"
                                    data-price="<?= $menuItem['price'] ?>"
                                    <?= $item['menu_item_id'] == $menuItem['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($menuItem['item_name']) ?> (₹<?= number_format($menuItem['price'], 2) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-span-3">
                        <input type="number" name="items[<?= $index ?>][quantity]" value="<?= $item['quantity'] ?>" min="1" class="quantity-input block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-3 flex items-center">
                        ₹<span class="item-amount"><?= number_format($item['amount'], 2) ?></span>
                    </div>
                    <div class="col-span-1 flex items-center justify-center">
                        <button type="button" class="text-red-600 hover:text-red-900 remove-item">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" id="addItemBtn" class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-plus mr-1"></i> Add Item
        </button>
    </div>

    <div class="mt-4 text-right font-bold text-lg">
        Total: ₹<span id="orderTotal"><?= number_format($order['total_amount'], 2) ?></span>
    </div>

    <div class="flex justify-end mt-6">
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-save mr-2"></i> Update Order
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        let itemIndex = <?= count($orderItems) ?>;

        // Add new item row
        $('#addItemBtn').click(function() {
            const newRow = `
        <div class="grid grid-cols-12 gap-2 mb-2 item-row">
            <div class="col-span-5">
                <select name="items[${itemIndex}][menu_item_id]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 menu-item-select">
                    <option value="">Select Item</option>
                    <?php foreach ($menuItems as $menuItem): ?>
                    <option value="<?= $menuItem['id'] ?>" data-price="<?= $menuItem['price'] ?>">
                        <?= htmlspecialchars($menuItem['item_name']) ?> (₹<?= number_format($menuItem['price'], 2) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-span-3">
                <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" class="quantity-input block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="col-span-3 flex items-center">
                ₹<span class="item-amount">0.00</span>
            </div>
            <div class="col-span-1 flex items-center justify-center">
                <button type="button" class="text-red-600 hover:text-red-900 remove-item">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>`;

            $('#orderItemsContainer').append(newRow);
            itemIndex++;
        });

        // Remove item row
        $('#orderItemsContainer').on('click', '.remove-item', function() {
            $(this).closest('.item-row').remove();
            calculateTotal();
        });

        // Calculate amount when item or quantity changes
        $('#orderItemsContainer').on('change', '.menu-item-select, .quantity-input', function() {
            const row = $(this).closest('.item-row');
            const select = row.find('.menu-item-select');
            const quantity = row.find('.quantity-input').val();
            const price = select.find('option:selected').data('price') || 0;
            const amount = price * quantity;

            row.find('.item-amount').text(amount.toFixed(2));
            calculateTotal();
        });

        // Calculate total amount
        function calculateTotal() {
            let total = 0;
            $('.item-row').each(function() {
                const amount = parseFloat($(this).find('.item-amount').text()) || 0;
                total += amount;
            });

            $('#orderTotal').text(total.toFixed(2));
        }

        // Submit form
        $('#editOrderForm').submit(function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: 'update_order.php',
                method: 'POST',
                data: formData,
                success: function(response) {
                    alert('Order updated successfully');
                    window.location.reload();
                },
                error: function() {
                    alert('Failed to update order');
                }
            });
        });
    });
</script>