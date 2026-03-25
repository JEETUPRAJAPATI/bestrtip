<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$db = getDbInstance();

$isEdit = false;
$order = [];
$order_id = '';
$items = [];

if (isset($_GET['order_id'])) {
    $isEdit = true;
    $order_id = filter_var($_GET['order_id'], FILTER_VALIDATE_INT);

    $db->where('id', $order_id);
    $order = $db->getOne('purchase_orders');

    if ($order) {
        $db->where('order_id', $order_id);
        $items = $db->get('purchase_order_items');
    } else {
        $_SESSION['failure'] = 'Purchase order not found';
        header('Location: purchase_list.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'order_date' => $_POST['order_date'],
        'order_by_name' => $_POST['order_by_name'],
        'order_number' => $_POST['order_number'],
        'signature' => $_POST['signature'] ?? null,
        'delivery_date' => $_POST['delivery_date'] ?? null,
        'delivered_by_name' => $_POST['delivered_by_name'] ?? null,
        'delivery_signature' => $_POST['delivery_signature'] ?? null,
        'bill_attached' => isset($_POST['bill_attached']) ? 1 : 0,
        'updated_at' => date('Y-m-d H:i:s')
    ];

    // Handle vendor selection/creation
    if (!empty($_POST['vendor_id'])) {
        if (strpos($_POST['vendor_id'], 'new_') === 0) {
            // New vendor - create record
            $vendor_name = substr($_POST['vendor_id'], 4);
            $vendor_data = [
                'name' => $vendor_name,
                'mobile' => $_POST['vendor_contact'] ?? '',
                'email' => '',
                'category_id' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $vendor_id = $db->insert('vendor', $vendor_data);
            $data['vendor_id'] = $vendor_id;
            $data['vendor_contact'] = $_POST['vendor_contact'] ?? '';
        } else {
            // Existing vendor
            $data['vendor_id'] = $_POST['vendor_id'];
            $data['vendor_contact'] = $_POST['vendor_contact'] ?? '';
        }
    }

    if ($isEdit) {
        $db->where('id', $order_id);
        $result = $db->update('purchase_orders', $data);

        if ($result) {
            // Update or add items
            $item_ids = [];
            foreach ($_POST['item'] as $item_data) {
                $item_data = [
                    'order_id' => $order_id,
                    'item_name' => $item_data['name'],
                    'quantity' => $item_data['quantity'],
                    'unit' => $item_data['unit'],
                    'date_needed' => $item_data['date_needed'] ?? null,
                    'status' => $item_data['status'] ?? 'pending'
                ];

                if (!empty($item_data['id'])) {
                    $db->where('id', $item_data['id']);
                    $db->where('order_id', $order_id);
                    $db->update('purchase_order_items', $item_data);
                    $item_ids[] = $item_data['id'];
                } else {
                    $item_id = $db->insert('purchase_order_items', $item_data);
                    $item_ids[] = $item_id;
                }
            }

            // Delete items not in the list
            if (!empty($item_ids)) {
                $db->where('order_id', $order_id);
                $db->where('id', $item_ids, 'NOT IN');
                $db->delete('purchase_order_items');
            }

            $_SESSION['success'] = 'Purchase order updated successfully!';
        } else {
            $_SESSION['failure'] = 'Failed to update purchase order: ' . $db->getLastError();
        }
    } else {
        $data['created_at'] = date('Y-m-d H:i:s');
        $order_id = $db->insert('purchase_orders', $data);

        if ($order_id) {
            // Add items
            foreach ($_POST['item'] as $item_data) {
                $item_data = [
                    'order_id' => $order_id,
                    'item_name' => $item_data['name'],
                    'quantity' => $item_data['quantity'],
                    'unit' => $item_data['unit'],
                    'date_needed' => $item_data['date_needed'] ?? null,
                    'status' => $item_data['status'] ?? 'pending'
                ];
                $db->insert('purchase_order_items', $item_data);
            }

            $_SESSION['success'] = 'Purchase order added successfully!';
        } else {
            $_SESSION['failure'] = 'Failed to add purchase order: ' . $db->getLastError();
        }
    }

    header('Location: purchase_list.php');
    exit();
}

// Get all vendors for dropdown
$vendors = $db->get('vendor');

include BASE_PATH . '/includes/header.php';
?>

<div class="layout-page">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col">
                <h4 class="py-3 mb-4"><?= $isEdit ? 'Edit' : 'Add New' ?> Purchase Order</h4>
            </div>
            <div class="col-auto">
                <a href="purchase_list.php" class="btn btn-secondary">Back to List</a>
            </div>
        </div>

        <?php include BASE_PATH . '/includes/flash_messages.php'; ?>

        <div class="card mb-4">
            <div class="card-body">
                <form action="" method="post" id="purchase-order-form">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="order_date" class="form-label">Date</label>
                            <input type="date" name="order_date" id="order_date" class="form-control"
                                value="<?= htmlspecialchars($order['order_date'] ?? date('Y-m-d')) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="order_by_name" class="form-label">Order By Name</label>
                            <input type="text" name="order_by_name" id="order_by_name" class="form-control"
                                value="<?= htmlspecialchars($order['order_by_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="order_number" class="form-label">Order No</label>
                            <input type="text" name="order_number" id="order_number" class="form-control"
                                value="<?= htmlspecialchars($order['order_number'] ?? 'PO-' . date('Ymd-His')) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="signature" class="form-label">Signature</label>
                            <input type="text" name="signature" id="signature" class="form-control"
                                value="<?= htmlspecialchars($order['signature'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="vendor_id" class="form-label">Vendor</label>
                            <select name="vendor_id" id="vendor_id" class="form-select select2-vendor"
                                data-placeholder="Select or add vendor">
                                <option value=""></option>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?= $vendor['id'] ?>"
                                        data-contact="<?= htmlspecialchars($vendor['mobile']) ?>"
                                        <?= (isset($order['vendor_id']) && $order['vendor_id'] == $vendor['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($vendor['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="vendor_contact" class="form-label">Vendor Contact</label>
                            <input type="text" name="vendor_contact" id="vendor_contact" class="form-control"
                                value="<?= htmlspecialchars($order['vendor_contact'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="delivery_date" class="form-label">Delivery Date</label>
                            <input type="date" name="delivery_date" id="delivery_date" class="form-control"
                                value="<?= htmlspecialchars($order['delivery_date'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th width="5%">Sr#</th>
                                    <th width="30%">Item</th>
                                    <th width="15%">Qty</th>
                                    <th width="10%">Unit</th>
                                    <th width="15%">Date Needed</th>
                                    <th width="15%">Status</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($items)): ?>
                                    <?php foreach ($items as $index => $item): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <input type="hidden" name="item[<?= $index ?>][id]" value="<?= $item['id'] ?>">
                                                <input type="text" name="item[<?= $index ?>][name]" class="form-control"
                                                    value="<?= htmlspecialchars($item['item_name']) ?>" required>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <button type="button" class="btn btn-outline-secondary quantity-minus">-</button>
                                                    <input type="number" name="item[<?= $index ?>][quantity]" class="form-control quantity-input"
                                                        step="0.01" min="0" value="<?= htmlspecialchars($item['quantity']) ?>" required>
                                                    <button type="button" class="btn btn-outline-secondary quantity-plus">+</button>
                                                </div>
                                            </td>
                                            <td>
                                                <select name="item[<?= $index ?>][unit]" class="form-select">
                                                    <option value="kg" <?= $item['unit'] == 'kg' ? 'selected' : '' ?>>kg</option>
                                                    <option value="ltr" <?= $item['unit'] == 'ltr' ? 'selected' : '' ?>>ltr</option>
                                                    <option value="pcs" <?= $item['unit'] == 'pcs' ? 'selected' : '' ?>>pcs</option>
                                                    <option value="box" <?= $item['unit'] == 'box' ? 'selected' : '' ?>>box</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="date" name="item[<?= $index ?>][date_needed]" class="form-control"
                                                    value="<?= htmlspecialchars($item['date_needed'] ?? '') ?>">
                                            </td>
                                            <td>
                                                <select name="item[<?= $index ?>][status]" class="form-select">
                                                    <option value="pending" <?= $item['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="completed" <?= $item['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                                    <option value="cancelled" <?= $item['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <input type="hidden" name="item[0][id]" value="">
                                            <input type="text" name="item[0][name]" class="form-control" required>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-secondary quantity-minus">-</button>
                                                <input type="number" name="item[0][quantity]" class="form-control quantity-input"
                                                    step="0.01" min="0" value="1" required>
                                                <button type="button" class="btn btn-outline-secondary quantity-plus">+</button>
                                            </div>
                                        </td>
                                        <td>
                                            <select name="item[0][unit]" class="form-select">
                                                <option value="kg">kg</option>
                                                <option value="ltr">ltr</option>
                                                <option value="pcs">pcs</option>
                                                <option value="box">box</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="date" name="item[0][date_needed]" class="form-control">
                                        </td>
                                        <td>
                                            <select name="item[0][status]" class="form-select">
                                                <option value="pending">Pending</option>
                                                <option value="completed">Completed</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <button type="button" id="add-row" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Add Item
                        </button>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="delivered_by_name" class="form-label">Delivered By Name</label>
                            <input type="text" name="delivered_by_name" id="delivered_by_name" class="form-control"
                                value="<?= htmlspecialchars($order['delivered_by_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="delivery_signature" class="form-label">Delivery Signature</label>
                            <input type="text" name="delivery_signature" id="delivery_signature" class="form-control"
                                value="<?= htmlspecialchars($order['delivery_signature'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-4 pt-2">
                                <input class="form-check-input" type="checkbox" name="bill_attached" id="bill_attached"
                                    value="1" <?= isset($order['bill_attached']) && $order['bill_attached'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="bill_attached">
                                    Bill Attached
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> <?= $isEdit ? 'Update' : 'Save' ?> Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include Select2 CSS and JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 for vendor dropdown
        $('.select2-vendor').select2({
            theme: 'bootstrap-5',
            allowClear: true,
            placeholder: $(this).data('placeholder'),
            tags: true,
            createTag: function(params) {
                // Don't duplicate existing vendors
                if (params.term.match(/\(new\)$/)) {
                    return null;
                }
                return {
                    id: 'new_' + params.term,
                    text: params.term + ' (new)',
                    newOption: true
                };
            }
        });

        $('#vendor_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const contact = selectedOption.data('contact') || '';

            // Always make sure the field is editable
            $('#vendor_contact').prop('readonly', false);

            if (!selectedOption.val()) {
                // No vendor selected (empty option)
                $('#vendor_contact').val('');
            } else if (selectedOption.attr('id') && selectedOption.attr('id').startsWith('new_')) {
                // New vendor - clear the field and focus
                $('#vendor_contact').val('').focus();
            } else {
                // Existing vendor - auto-fill contact if available
                if (contact) {
                    $('#vendor_contact').val(contact);
                } else {
                    // If no contact data exists, clear and allow editing
                    $('#vendor_contact').val('').focus();
                }
            }
        });
        // Add new row to items table
        $('#add-row').click(function() {
            const rowCount = $('#items-table tbody tr').length;
            const newRow = `
            <tr>
                <td>${rowCount + 1}</td>
                <td>
                    <input type="hidden" name="item[${rowCount}][id]" value="">
                    <input type="text" name="item[${rowCount}][name]" class="form-control" required>
                </td>
                <td>
                    <div class="input-group">
                        <button type="button" class="btn btn-outline-secondary quantity-minus">-</button>
                        <input type="number" name="item[${rowCount}][quantity]" class="form-control quantity-input"
                               step="0.01" min="0" value="1" required>
                        <button type="button" class="btn btn-outline-secondary quantity-plus">+</button>
                    </div>
                </td>
                <td>
                    <select name="item[${rowCount}][unit]" class="form-select">
                        <option value="kg">kg</option>
                        <option value="ltr">ltr</option>
                        <option value="pcs">pcs</option>
                        <option value="box">box</option>
                    </select>
                </td>
                <td>
                    <input type="date" name="item[${rowCount}][date_needed]" class="form-control">
                </td>
                <td>
                    <select name="item[${rowCount}][status]" class="form-select">
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
            $('#items-table tbody').append(newRow);
        });

        // Remove row
        $(document).on('click', '.remove-row', function() {
            if ($('#items-table tbody tr').length > 1) {
                $(this).closest('tr').remove();
                updateRowNumbers();
            } else {
                alert('You must have at least one item');
            }
        });

        // Quantity minus
        $(document).on('click', '.quantity-minus', function() {
            const input = $(this).siblings('.quantity-input');
            let value = parseFloat(input.val()) || 0;
            if (value > 0) {
                value -= 1;
                input.val(value.toFixed(2));
            }
        });

        // Quantity plus
        $(document).on('click', '.quantity-plus', function() {
            const input = $(this).siblings('.quantity-input');
            let value = parseFloat(input.val()) || 0;
            value += 1;
            input.val(value.toFixed(2));
        });

        // Update row numbers
        function updateRowNumbers() {
            $('#items-table tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
                // Update all input names to maintain array order
                $(this).find('input, select').each(function() {
                    const name = $(this).attr('name');
                    if (name && name.includes('item[')) {
                        $(this).attr('name', name.replace(/item\[\d+\]/, `item[${index}]`));
                    }
                });
            });
        }
    });
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>