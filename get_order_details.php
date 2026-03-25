<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$order_id = $_GET['order_id'] ?? '';
$db = getDbInstance();

// Get order details
$db->where('id', $order_id);
$order = $db->getOne('restaurant_orders');

if (!$order) {
    echo '<div class="text-red-500">Order not found</div>';
    exit();
}

// Get restaurant details
$db->where('id', $order['restaurant_id']);
$restaurant = $db->getOne('restaurants');

// Get order items
$db->where('order_id', $order_id);
$db->join('restaurant_menu m', 'm.id=restaurant_order_items.menu_item_id', 'LEFT');
$orderItems = $db->get('restaurant_order_items', null, 'restaurant_order_items.*, m.item_name, m.type, m.price');
?>

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Order Information -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h4 class="font-bold text-lg mb-3 border-b pb-2">Order Information</h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Order Code:</span>
                    <span><?= $order['order_code'] ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Date:</span>
                    <span><?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Status:</span>
                    <span class="px-2 py-1 rounded-full text-xs <?=
                                                                $order['status'] == 'Pending' ? 'bg-yellow-100 text-yellow-800' : ($order['status'] == 'Served' ? 'bg-green-100 text-green-800' : ($order['status'] == 'Paid' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'))
                                                                ?>">
                        <?= $order['status'] ?>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Payment Mode:</span>
                    <span><?= ucfirst($order['payment_mode'] ?? 'cash') ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Tax Option:</span>
                    <span><?= $order['tax_option'] == 'include' ? 'Included (10%)' : 'Excluded' ?></span>
                </div>
            </div>
        </div>

        <!-- Guest Information -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h4 class="font-bold text-lg mb-3 border-b pb-2">Guest Information</h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Name:</span>
                    <span><?= htmlspecialchars($order['guest_name']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Room No:</span>
                    <span><?= $order['room_no'] ? $order['room_no'] : '-' ?></span>
                </div>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h4 class="font-bold text-lg mb-3 border-b pb-2">Billing Information</h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Subtotal:</span>
                    <span>₹<?= number_format($order['total_amount'], 2) ?></span>
                </div>
                <?php if ($order['tax_option'] == 'include'): ?>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Tax (10%):</span>
                        <span>₹<?= number_format($order['tax_amount'], 2) ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex justify-between font-bold border-t pt-2 mt-2">
                    <span class="text-gray-700">Grand Total:</span>
                    <span>₹<?= number_format($order['grand_total'], 2) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Billing Address -->
    <?php if (!empty($order['billing_address'])): ?>
        <div class="bg-white p-4 rounded-lg shadow">
            <h4 class="font-bold text-lg mb-2 border-b pb-2">Billing Address</h4>
            <p class="text-sm"><?= nl2br(htmlspecialchars($order['billing_address'])) ?></p>
        </div>
    <?php endif; ?>

    <!-- Order Items -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h4 class="font-bold text-lg mb-3 border-b pb-2">Order Items</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($orderItems as $item): ?>
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                <?= htmlspecialchars($item['item_name']) ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 rounded-full text-xs <?=
                                                                            $item['type'] == 'Veg' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                                                            ?>">
                                    <?= $item['type'] ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm"><?= $item['quantity'] ?></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">₹<?= number_format($item['rate'], 2) ?></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">₹<?= number_format($item['amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Update Status -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h4 class="font-bold text-lg mb-3 border-b pb-2">Update Status</h4>
        <form id="updateStatusForm">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            <div class="flex flex-col sm:flex-row gap-3">
                <select name="status" class="flex-grow rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Served" <?= $order['status'] == 'Served' ? 'selected' : '' ?>>Served</option>
                    <option value="Paid" <?= $order['status'] == 'Paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="Cancelled" <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="bx bx-check-circle mr-2"></i> Update Status
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#updateStatusForm').submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: 'update_order_status.php',
                method: 'POST',
                data: $(this).serialize(),
                beforeSend: function() {
                    // Show loading indicator
                    $('#updateStatusForm button').html('<i class="bx bx-loader bx-spin mr-2"></i> Updating...');
                },
                success: function(response) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Order status updated successfully',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function() {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update status'
                    });
                    $('#updateStatusForm button').html('<i class="bx bx-check-circle mr-2"></i> Update Status');
                }
            });
        });
    });
</script>