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
        'date' => $_POST['date'] ?? null,
        'category_id' => $_POST['category_id'] ?? null,
        'booking_id' => $_POST['booking_code'] ?? null,
        'guest_name' => $_POST['guest_name'] ?? null,
        'billing_amount' => $_POST['billing_amount'] ?? null,
        'tax' => $_POST['tax'] ?? null,
        'net_amount' => $_POST['net_amount'] ?? null,
        'location' => $_POST['location'] ?? null,
        'payment_mode' => $_POST['payment_mode'] ?? null,
        'transaction_id' => $_POST['transaction_id'] ?? null,
        'payment_status' => $_POST['payment_status'] ?? null,
        'booking_details' => $_POST['booking_details'] ?? null,
        'invoice_status' => $_POST['invoice_status'] ?? null,
        'partner' => $_POST['partner'] ?? null,
        'service' => $_POST['service'] ?? null,
    ];

    if (!empty($id)) {
        $db->where('id', $id);
        $update = $db->update('sales', $data_to_store);
        $_SESSION['success'] = $update ? 'Sale updated successfully.' : 'Update failed: ' . $db->getLastError();
    } else {
        $insert = $db->insert('sales', $data_to_store);
        $_SESSION['success'] = $insert ? 'Sale added successfully.' : 'Insert failed: ' . $db->getLastError();
    }

    header('Location: sales.php');
    exit();
}


$id = isset($_GET['id']) ? decryptId($_GET['id']) : '';
$edit = false;
$data = [];

if (!empty($id)) {
    $edit = true;
    $db->where('id', $id);
    $data = $db->getOne('sales');
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
            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales/</span> <?= $edit ? 'Edit' : "Add" ?> Sales</h4>
            <h4 class="py-3 mb-4">Add Sales</h4>
            <!-- Basic Layout -->
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="post">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

                                <div class="row mb-3">
                                    <div class="col">
                                        <label>Date</label>
                                        <input type="date" name="date" id="date" class="form-control" value="<?= $data['date'] ?? '' ?>">
                                    </div>
                                    <div class="col">
                                        <label>Category</label>
                                        <input type="text" name="category_id" class="form-control" value="<?= $data['category_id'] ?? '' ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        <label>Booking Code</label>
                                        <input type="text" name="booking_code" id="booking_code" class="form-control" value="<?= $data['booking_id'] ?? '' ?>">
                                    </div>
                                    <div class="col">
                                        <label>Guest Name</label>
                                        <input type="text" name="guest_name" id="guest_name" class="form-control" value="<?= $data['guest_name'] ?? '' ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        <label>Billing Amount</label>
                                        <input type="number" step="0.01" name="billing_amount" id="billing_amount" class="form-control" value="<?= $data['billing_amount'] ?? '' ?>">
                                    </div>
                                    <div class="col">
                                        <label>Tax</label>
                                        <input type="number" step="0.01" name="tax" id="tax" class="form-control" value="<?= $data['tax'] ?? '' ?>">
                                    </div>
                                    <div class="col">
                                        <label>Net Amount</label>
                                        <input type="number" step="0.01" name="net_amount" id="net_amount" class="form-control" value="<?= $data['net_amount'] ?? '' ?>" required>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        <label>Location</label>
                                        <input type="text" name="location" class="form-control" value="<?= $data['location'] ?? '' ?>">
                                    </div>
                                    <div class="col">
                                        <label>Payment Mode</label>
                                        <select name="payment_mode" class="form-select">
                                            <option value="">-- Select Mode --</option>
                                            <option value="Cash" <?= ($data['payment_mode'] ?? '') === 'Cash' ? 'selected' : '' ?>>Cash</option>
                                            <option value="Online" <?= ($data['payment_mode'] ?? '') === 'Online' ? 'selected' : '' ?>>Online</option>
                                            <option value="Bank Transfer" <?= ($data['payment_mode'] ?? '') === 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                        </select>
                                    </div>

                                    <div class="col">
                                        <label>Transaction ID</label>
                                        <input type="text" name="transaction_id" class="form-control" value="<?= $data['transaction_id'] ?? '' ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        <label>Payment Status</label>
                                        <select name="payment_status" class="form-select">
                                            <option value="">-- Select Status --</option>
                                            <option value="Paid" <?= ($data['payment_status'] ?? '') === 'Paid' ? 'selected' : '' ?>>Paid</option>
                                            <option value="Pending" <?= ($data['payment_status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Partial Received" <?= ($data['payment_status'] ?? '') === 'Partial Received' ? 'selected' : '' ?>>Partial Received</option>
                                        </select>
                                    </div>

                                    <div class="col">
                                        <label>Invoice Status</label>
                                        <select name="invoice_status" class="form-select">
                                            <option value="">-- Select Status --</option>
                                            <option value="Generate" <?= ($data['invoice_status'] ?? '') === 'Generate' ? 'selected' : '' ?>>Generate</option>
                                            <option value="Not" <?= ($data['invoice_status'] ?? '') === 'Not' ? 'selected' : '' ?>>Not</option>
                                        </select>
                                    </div>

                                    <div class="col">
                                        <label>Partner</label>
                                        <select name="partner" class="form-select">
                                            <option value="">-- Select Partner --</option>
                                            <option value="Agent" <?= ($data['partner'] ?? '') === 'Agent' ? 'selected' : '' ?>>Agent</option>
                                            <option value="Direct" <?= ($data['partner'] ?? '') === 'Direct' ? 'selected' : '' ?>>Direct</option>
                                            <option value="Hotel" <?= ($data['partner'] ?? '') === 'Hotel' ? 'selected' : '' ?>>Hotel</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label>Service</label>
                                        <select name="service" class="form-select">
                                            <option value="">-- Select Service --</option>
                                            <option value="Room" <?= ($data['service'] ?? '') === 'Room' ? 'selected' : '' ?>>Room</option>
                                            <option value="F&B" <?= ($data['service'] ?? '') === 'F&B' ? 'selected' : '' ?>>F&amp;B</option>
                                            <option value="Other Service" <?= ($data['service'] ?? '') === 'Other Service' ? 'selected' : '' ?>>Other Service</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="mb-3">
                                    <label>Booking Details</label>
                                    <textarea name="booking_details" class="form-control"><?= $data['booking_details'] ?? '' ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-success"><?= $edit ? 'Update' : 'Submit' ?></button>
                                <a href="sales.php" class="btn btn-secondary">Cancel</a>
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

        function calculateNetAmount() {
            let billing = parseFloat(document.getElementById('billing_amount').value) || 0;
            let taxPercent = parseFloat(document.getElementById('tax').value) || 0;

            let taxAmount = (billing * taxPercent) / 100;
            let net = billing + taxAmount;

            document.getElementById('net_amount').value = net.toFixed(2);
        }

        document.getElementById('billing_amount').addEventListener('input', calculateNetAmount);
        document.getElementById('tax').addEventListener('input', calculateNetAmount);


        $('#booking_code').on('blur', function() {
            const bookingCode = $(this).val().trim();

            if (bookingCode !== '') {
                $.ajax({
                    url: 'ajax/get_booking_details.php',
                    type: 'GET',
                    data: {
                        booking_code: bookingCode
                    },
                    dataType: 'json',
                    success: function(result) {
                        if (result.status === 'success') {
                            const data = result.details;
                            $('#guest_name').val(data.name || '');
                            $('#date').val(data.due_date || '');
                            $('input[name="category_id"]').val(data.category || '');
                            $('input[name="location"]').val(data.hotel_details ? parseHotelLocation(data.hotel_details) : '');
                            $('textarea[name="booking_details"]').val(buildBookingDetails(data));
                            $('#billing_amount').val(data.total_amount || '');
                            $('#tax').val(calculateTax(data.total_amount, data.without_gst));
                            $('#net_amount').val(data.total_amount || '');
                        } else {
                            // Just clear autofill fields, no alert
                            $('#guest_name').val('');
                            $('#date').val('');
                            $('input[name="category_id"]').val('');
                            $('input[name="location"]').val('');
                            $('textarea[name="booking_details"]').val('');
                            $('#billing_amount').val('');
                            $('#tax').val('');
                            $('#net_amount').val('');
                            // Don't alert user – allow manual entry
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        // Optional: could silently fail or notify softly
                        alert('Something went wrong while fetching booking details');
                    }
                });
            }
        });

    });
</script>
<?php include BASE_PATH . '/includes/footer.php'; ?>