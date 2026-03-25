<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$db = getDbInstance();

// Get restaurants and menu items for dropdowns
$restaurants = $db->get('restaurants');
$menuItems = $db->get('restaurant_menu');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_to_store = array_filter($_POST);

    // Generate order code
    $lastOrder = $db->orderBy("id", "DESC")->getOne("restaurant_orders", "order_code");
    if ($lastOrder && isset($lastOrder['order_code'])) {
        $lastNum = (int) filter_var($lastOrder['order_code'], FILTER_SANITIZE_NUMBER_INT);
        $newCode = 'ORD-' . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newCode = 'ORD-0001';
    }

    $orderData = [
        'order_code' => $newCode,
        'restaurant_id' => $data_to_store['restaurant_id'],
        'guest_name' => $data_to_store['guest_name'],
        'room_no' => $data_to_store['room_no'] ?? null,
        'billing_address' => $data_to_store['billing_address'] ?? null,
        'tax_option' => $data_to_store['tax_option'] ?? 'exclude',
        'payment_mode' => $data_to_store['payment_mode'] ?? 'cash',
        'status' => 'Pending',
        'total_amount' => 0,
        'tax_amount' => 0,
        'grand_total' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Calculate total from items
    $total = 0;
    $orderItems = [];

    foreach ($data_to_store['items'] as $item) {
        if ($item['menu_item_id'] && $item['quantity'] > 0) {
            $db->where('id', $item['menu_item_id']);
            $menuItem = $db->getOne('restaurant_menu');

            if ($menuItem) {
                $amount = $menuItem['price'] * $item['quantity'];
                $total += $amount;

                $orderItems[] = [
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'rate' => $menuItem['price'],
                    'amount' => $amount
                ];
            }
        }
    }

    // Calculate tax if included
    $taxAmount = 0;
    if ($orderData['tax_option'] === 'include' && $total > 0) {
        // Assuming 10% tax rate - you can make this configurable
        $taxRate = 0.10;
        $taxAmount = $total * $taxRate;
    }

    $orderData['total_amount'] = $total;
    $orderData['tax_amount'] = $taxAmount;
    $orderData['grand_total'] = $total + $taxAmount;

    // Insert order
    $last_id = $db->insert('restaurant_orders', $orderData);

    if ($last_id) {
        // Insert order items
        foreach ($orderItems as $item) {
            $item['order_id'] = $last_id;
            $db->insert('restaurant_order_items', $item);
        }

        $_SESSION['success'] = "Order added successfully!";
        $db->where('id', $data_to_store['restaurant_id']);
        $restaurant = $db->getOne('restaurants', ['property_id']);

        if ($restaurant && isset($restaurant['property_id'])) {
            // Encrypt the property_id for the redirect
            $encrypted_property_id = encryptId($restaurant['property_id']);
            header('Location: restaurant_orders.php?pid=' . $encrypted_property_id);
        } else {
            // Fallback if restaurant not found
            header('Location: restaurant_orders.php');
        }
        exit();
    } else {
        echo 'Insert failed: ' . $db->getLastError();
        exit();
    }
}

include BASE_PATH . '/includes/header.php';
?>
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<!-- Layout container -->
<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4">Add New Order</h4>
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="" method="post" id="order_form">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Restaurant</label>
                                        <select class="form-select" name="restaurant_id" required>
                                            <option value="">Select Restaurant</option>
                                            <?php foreach ($restaurants as $restaurant): ?>
                                                <option value="<?= $restaurant['id'] ?>"><?= htmlspecialchars($restaurant['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Guest Name</label>
                                        <input type="text" class="form-control" name="guest_name" required />
                                    </div>
                                </div>

                                <div class="row mb-3">

                                    <div class="col-md-4">
                                        <label class="form-label">Room No (Optional)</label>
                                        <input type="text" class="form-control" name="room_no" />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Payment Mode</label>
                                        <select class="form-select" name="payment_mode" required>
                                            <option value="cash">Cash</option>
                                            <option value="upi">UPI</option>
                                            <option value="online">Online</option>
                                            <option value="card">Card</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Tax Option</label>
                                        <select class="form-select" name="tax_option" required>
                                            <option value="exclude">Tax Excluded</option>
                                            <option value="include">Tax Included</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Billing Address (Optional)</label>
                                        <textarea class="form-control" name="billing_address" rows="2"></textarea>
                                    </div>

                                </div>

                                <div class="mb-4">
                                    <h5 class="mb-3">Order Items</h5>
                                    <div id="order_items_container">
                                        <div class="row mb-3 item-row">
                                            <div class="col-md-5">
                                                <select name="items[0][menu_item_id]" class="form-select menu-item-select" required>
                                                    <option value="">Select Menu Item</option>
                                                    <?php foreach ($menuItems as $item): ?>
                                                        <option value="<?= $item['id'] ?>" data-price="<?= $item['price'] ?>">
                                                            <?= htmlspecialchars($item['item_name']) ?> (₹<?= number_format($item['price'], 2) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="number" name="items[0][quantity]" min="1" value="1" class="form-control quantity-input" required>
                                            </div>
                                            <div class="col-md-3 d-flex align-items-center">
                                                <span class="item-amount">₹0.00</span>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-item" disabled>
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" id="add_item_btn" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="bx bx-plus me-1"></i> Add Item
                                    </button>
                                </div>

                                <div class="mb-3 text-end">
                                    <div class="row justify-content-end">
                                        <div class="col-md-4">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>Subtotal</th>
                                                    <td>₹<span id="order_subtotal">0.00</span></td>
                                                </tr>
                                                <tr id="tax_row" style="display: none;">
                                                    <th>Tax (10%)</th>
                                                    <td>₹<span id="order_tax">0.00</span></td>
                                                </tr>
                                                <tr>
                                                    <th>Grand Total</th>
                                                    <td>₹<span id="order_grand_total">0.00</span></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Save Order</button>
                                <a href="order_list.php" class="btn btn-outline-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemIndex = 1;

        // Add new item row
        document.getElementById('add_item_btn').addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.className = 'row mb-3 item-row';
            newRow.innerHTML = `
                <div class="col-md-5">
                    <select name="items[${itemIndex}][menu_item_id]" class="form-select menu-item-select" required>
                        <option value="">Select Menu Item</option>
                        <?php foreach ($menuItems as $item): ?>
                            <option value="<?= $item['id'] ?>" data-price="<?= $item['price'] ?>">
                                <?= htmlspecialchars($item['item_name']) ?> (₹<?= number_format($item['price'], 2) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="items[${itemIndex}][quantity]" min="1" value="1" class="form-control quantity-input" required>
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <span class="item-amount">₹0.00</span>
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
            `;

            document.getElementById('order_items_container').appendChild(newRow);
            itemIndex++;
            updateRemoveButtons();
        });

        // Remove item row
        document.getElementById('order_items_container').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
                const row = e.target.closest('.item-row');
                if (document.querySelectorAll('.item-row').length > 1) {
                    row.remove();
                    calculateTotals();
                }
            }
        });

        // Calculate amount when item or quantity changes
        document.getElementById('order_items_container').addEventListener('change', function(e) {
            if (e.target.classList.contains('menu-item-select') || e.target.classList.contains('quantity-input')) {
                const row = e.target.closest('.item-row');
                const select = row.querySelector('.menu-item-select');
                const quantityInput = row.querySelector('.quantity-input');
                const amountSpan = row.querySelector('.item-amount');

                const selectedOption = select.options[select.selectedIndex];
                const price = selectedOption ? parseFloat(selectedOption.dataset.price) : 0;
                const quantity = parseInt(quantityInput.value) || 0;
                const amount = price * quantity;

                amountSpan.textContent = '₹' + amount.toFixed(2);
                calculateTotals();
            }
        });

        // Tax option change
        document.querySelector('select[name="tax_option"]').addEventListener('change', function() {
            calculateTotals();
        });

        // Enable/disable remove buttons based on row count
        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.item-row');
            const removeButtons = document.querySelectorAll('.remove-item');

            removeButtons.forEach(button => {
                button.disabled = rows.length <= 1;
            });
        }

        // Calculate total amounts
        function calculateTotals() {
            let subtotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const amountText = row.querySelector('.item-amount').textContent;
                const amount = parseFloat(amountText.replace('₹', '')) || 0;
                subtotal += amount;
            });

            const taxOption = document.querySelector('select[name="tax_option"]').value;
            let taxAmount = 0;
            let grandTotal = subtotal;

            if (taxOption === 'include' && subtotal > 0) {
                const taxRate = 0.10; // 10% tax
                taxAmount = subtotal * taxRate;
                grandTotal = subtotal + taxAmount;

                document.getElementById('tax_row').style.display = '';
                document.getElementById('order_tax').textContent = taxAmount.toFixed(2);
            } else {
                document.getElementById('tax_row').style.display = 'none';
            }

            document.getElementById('order_subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('order_grand_total').textContent = grandTotal.toFixed(2);
        }

        // Initialize
        updateRemoveButtons();
        calculateTotals();
    });
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>