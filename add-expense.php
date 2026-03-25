<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = getDbInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $data_to_store = [
        'date' => $_POST['date'] ?? '',
        'category_id' => $_POST['category_id'] ?? '',
        'vendor_id' => $_POST['vendor_id'] ?? '',
        'description' => $_POST['description'] ?? '',
        'billing_amount' => $_POST['billing_amount'] ?? '',
        'tax' => $_POST['tax'] ?? '',
        'net_amount' => $_POST['net_amount'] ?? '',
        'location' => $_POST['location'] ?? '',
        'payment_mode' => $_POST['payment_mode'] ?? '',
        'transaction_id' => $_POST['transaction_id'] ?? '',
        'status' => $_POST['status'] ?? '',
        'booking_id' => $_POST['booking_id'] ?? '',
        'group_name' => $_POST['group_name'] ?? '',
    ];

    // Handle bill_upload
    $upload_dir = 'uploads/expenses/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (isset($_FILES['bill_upload']) && $_FILES['bill_upload']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['bill_upload']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['bill_upload']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            $data_to_store['bill_upload'] = $file_path;

            // Remove old file if updating
            if (!empty($id)) {
                $db->where('id', $id);
                $old_file = $db->getValue('expense', 'bill_upload');
                if ($old_file && file_exists($old_file)) {
                    unlink($old_file);
                }
            }
        }
    }

    // Insert or Update
    if (!empty($id)) {
        $db->where('id', $id);
        $updated = $db->update('expense', $data_to_store);
        if ($updated) {
            $_SESSION['success'] = 'Expense updated successfully.';
        } else {
            $_SESSION['error'] = 'Update failed: ' . $db->getLastError();
        }
    } else {
        $insert = $db->insert('expense', $data_to_store);
        if ($insert) {
            $_SESSION['success'] = 'Expense added successfully.';
        } else {
            $_SESSION['error'] = 'Insert failed: ' . $db->getLastError();
        }
    }

    header('Location: expense.php');
    exit();
}

$id = isset($_GET['id']) ? decryptId($_GET['id']) : '';
$edit = false;
$data = [];

if (!empty($id)) {
    $edit = true;
    $db->where('id', $id);
    $data = $db->getOne('expense');
}

$db->where('status', 'active');
$categories = $db->get('vendor_category', null, 'id, name');

include BASE_PATH . '/includes/header.php';

?>
<style>
    .select2-selection__choice {
        background-color: #007bff !important;
        color: #fff !important;
        padding: 6px 12px !important;
        border-radius: 15px !important;
        display: flex !important;
        align-items: center !important;
        font-size: 14px !important;
        margin: 4px !important;
    }
</style>
<div class="layout-page">


    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Expense/</span> <?= $edit ? 'Edit' : "Add" ?> Expense</h4>
            <h4 class="py-3 mb-4">Add Expense</h4>
            <!-- Basic Layout -->
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

                                <div class="row mb-3">
                                    <div class="col">
                                        <label>Date</label>
                                        <input type="date" name="date" class="form-control" value="<?= $data['date'] ?? '' ?>" required>
                                    </div>

                                    <div class="col">
                                        <label class="form-label">Vendor Category</label>
                                        <select name="category_id" id="category_id" class="form-select" required>
                                            <option value="">-- Select Category --</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['id'] ?>" <?= ($data['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col">
                                        <label class="form-label">Vendor Name</label>
                                        <select name="vendor_id" id="vendor_id" class="form-select" data-selected="<?= $data['vendor_id'] ?? '' ?>">
                                            <option value="">-- Select Vendor --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label>Description</label>
                                    <textarea name="description" class="form-control" required><?= $data['description'] ?? '' ?></textarea>
                                </div>


                                <div class="row mb-3">
                                    <div class="col">
                                        <label>Billing Amount</label>
                                        <input type="number" step="0.01" name="billing_amount" id="billing_amount" class="form-control" value="<?= $data['billing_amount'] ?? '' ?>" required>
                                    </div>
                                    <div class="col">
                                        <label>Tax</label>
                                        <input type="number" step="0.01" name="tax" id="tax" class="form-control" value="<?= $data['tax'] ?? '' ?>" required>
                                    </div>
                                    <div class="col">
                                        <label>Net Amount</label>
                                        <input type="number" step="0.01" name="net_amount" id="net_amount" class="form-control" value="<?= $data['net_amount'] ?? '' ?>" readonly required>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        <label>Location</label>
                                        <input type="text" name="location" class="form-control" value="<?= $data['location'] ?? '' ?>" required>
                                    </div>
                                    <div class="col">
                                        <label>Payment Mode</label>
                                        <select name="payment_mode" class="form-select" required>
                                            <option value="">-- Select Payment Mode --</option>
                                            <option value="Online" <?= ($data['payment_mode'] ?? '') === 'Online' ? 'selected' : '' ?>>Online</option>
                                            <option value="GPay" <?= ($data['payment_mode'] ?? '') === 'GPay' ? 'selected' : '' ?>>GPay</option>
                                            <option value="Bank Transfer" <?= ($data['payment_mode'] ?? '') === 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                            <option value="Cheque" <?= ($data['payment_mode'] ?? '') === 'Cheque' ? 'selected' : '' ?>>Cheque</option>
                                            <option value="Cash" <?= ($data['payment_mode'] ?? '') === 'Cash' ? 'selected' : '' ?>>Cash</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label>Transaction ID</label>
                                        <input type="text" name="transaction_id" class="form-control" value="<?= $data['transaction_id'] ?? '' ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        <label>Status</label>
                                        <select name="status" class="form-select" required>
                                            <option value="">-- Select Status --</option>
                                            <option value="Paid" <?= ($data['status'] ?? '') === 'Paid' ? 'selected' : '' ?>>Paid</option>
                                            <option value="Pending" <?= ($data['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="verified" <?= ($data['status'] ?? '') === 'verified' ? 'selected' : '' ?>>Verified</option>
                                        </select>
                                    </div>

                                    <div class="col">
                                        <label>Booking ID</label>
                                        <input type="text" name="booking_id" class="form-control" value="<?= $data['booking_id'] ?? '' ?>" required>
                                    </div>

                                    <div class="col">
                                        <label>Group Name</label>
                                        <input type="text" name="group_name" class="form-control" value="<?= $data['group_name'] ?? '' ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label>Upload Bill</label>
                                    <input type="file" name="bill_upload" class="form-control" <?= empty($data['bill_upload']) ? 'required' : '' ?>>
                                    <?php if (!empty($data['bill_upload'])): ?>
                                        <p>Current: <a href="<?= $data['bill_upload'] ?>" target="_blank">View</a></p>
                                    <?php endif; ?>
                                </div>

                                <button type="submit" class="btn btn-primary">Save</button>
                                <a href="expense.php" class="btn btn-secondary">Cancel</a>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
</div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $("#agents").select2({
            placeholder: "Choose agents...",
            allowClear: true
        });
        const selectedCategoryId = $('#category_id').val();
        const selectedVendorId = $('#vendor_id').data('selected');

        function loadVendors(categoryId, selectedVendorId = null) {
            const $vendorSelect = $('#vendor_id');
            $vendorSelect.html('<option value="">-- Loading Vendors... --</option>');

            if (categoryId) {
                $.ajax({
                    url: 'ajax/vendor-list.php',
                    method: 'GET',
                    data: {
                        category_id: categoryId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $vendorSelect.html('<option value="">-- Select Vendor --</option>');
                            $.each(response.vendors, function(index, vendor) {
                                const selected = vendor.id == selectedVendorId ? 'selected' : '';
                                $vendorSelect.append(`<option value="${vendor.id}" ${selected}>${vendor.name}</option>`);
                            });
                        } else {
                            $vendorSelect.html('<option value="">-- No Vendors Found --</option>');
                        }
                    },
                    error: function() {
                        $vendorSelect.html('<option value="">-- Error Loading Vendors --</option>');
                    }
                });
            } else {
                $vendorSelect.html('<option value="">-- Select Vendor --</option>');
            }
        }

        // On category change
        $('#category_id').on('change', function() {
            const categoryId = $(this).val();
            loadVendors(categoryId);
        });

        // Trigger on page load (edit mode)
        if (selectedCategoryId) {
            loadVendors(selectedCategoryId, selectedVendorId);
        }

        // Trigger on page load (edit mode)
        if (selectedCategoryId) {
            loadVendors(selectedCategoryId, selectedVendorId);
        }

        function calculateNetAmount() {
            let billing = parseFloat(document.getElementById('billing_amount').value) || 0;
            let taxPercent = parseFloat(document.getElementById('tax').value) || 0;

            let taxAmount = (billing * taxPercent) / 100;
            let net = billing + taxAmount;

            document.getElementById('net_amount').value = net.toFixed(2);
        }

        document.getElementById('billing_amount').addEventListener('input', calculateNetAmount);
        document.getElementById('tax').addEventListener('input', calculateNetAmount);
    });
</script>
<?php include BASE_PATH . '/includes/footer.php'; ?>