<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get Inputs
$search_string = filter_input(INPUT_GET, 'search_string');
$filter_col = filter_input(INPUT_GET, 'filter_col') ?? 'sales.guest_name';
$order_by = filter_input(INPUT_GET, 'order_by') ?? 'desc';
$date_range = filter_input(INPUT_GET, 'date_range');

// DB Instance
$db = getDbInstance();

$select = [
    'sales.id',
    'sales.date',
    'sales.category_id',
    'sales.booking_id',
    'sales.guest_name',
    'sales.billing_amount',
    'sales.tax',
    'sales.net_amount',
    'sales.location',
    'sales.payment_mode',
    'sales.transaction_id',
    'sales.payment_status',
    'sales.booking_details',
    'sales.invoice_status',
    'sales.partner',
    'sales.created_at'
];

// Search
if ($search_string) {
    $db->where($filter_col, '%' . $search_string . '%', 'like');
}

// Date Range Filter
if (!empty($date_range)) {
    [$start_date, $end_date] = explode(' - ', $date_range);
    $db->where('sales.date', $start_date, '>=');
    $db->where('sales.date', $end_date, '<=');
}

// Order
$db->orderBy($filter_col, $order_by);
$sales = $db->get('sales', null, $select);
$total_sales = array_sum(array_column($sales, 'net_amount'));
$total_billing_amount_sales = array_sum(array_column($sales, 'billing_amount'));
$total_tax_sales = array_sum(array_column($sales, 'tax'));
include BASE_PATH . '/includes/header.php';
?>
<style>
    .dataTables_wrapper .form-control {
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
    }

    div#salesTable_filter {
        margin-bottom: 11px;
    }
</style>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Search Form -->
            <form method="get">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Search By</label>
                                <select class="form-select" name="filter_col">
                                    <option value="sales.guest_name" <?= $filter_col == 'sales.guest_name' ? 'selected' : '' ?>>Guest Name</option>
                                    <option value="sales.booking_id" <?= $filter_col == 'sales.booking_id' ? 'selected' : '' ?>>Booking ID</option>
                                    <option value="sales.partner" <?= $filter_col == 'sales.partner' ? 'selected' : '' ?>>Partner</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Enter Text</label>
                                <input class="form-control" name="search_string" type="text" placeholder="Search..." value="<?= htmlspecialchars($search_string ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date Range</label>
                                <input type="text" name="date_range" id="date_range" class="form-control" placeholder="Select date range"
                                    value="<?= htmlspecialchars($date_range ?? '') ?>" autocomplete="off">
                            </div>
                            <div class="col-md-12 mt-3 text-end">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <a href="sales.php" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>



            <!-- Overview Cards -->
            <div class="row mb-4">
                <!-- Total Expenses Card -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Sales</h5>
                            <p class="display-6">&#8377; <?= number_format($total_sales, 2) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Billing Amount Card -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Billing Amount</h5>
                            <p class="display-6">&#8377; <?= number_format($total_billing_amount_sales, 2) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Tax Card -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Tax</h5>
                            <p class="display-6">&#8377; <?= number_format($total_tax_sales, 2) ?></p>
                        </div>
                    </div>
                </div>
            </div>


            <?php include BASE_PATH . '/includes/flash_messages.php'; ?>
            <div class="row">
                <div class="col">
                    <h4 class="py-3 mb-4">Sales List</h4>
                </div>
                <div class="col-auto">
                    <a href="add-sale.php" class="btn btn-primary">Add Sale</a>
                </div>
            </div>
            <!-- Sales Table -->
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="salesTable" class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Booking ID</th>
                                <th>Guest Name</th>
                                <th>Total Amount</th>
                                <th>Tax</th>
                                <th>Net Amount</th>
                                <th>Payment Mode</th>
                                <th>Payment Status</th>
                                <th>Invoice Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($sales): ?>
                                <?php $count = 1; ?>
                                <?php foreach ($sales as $sale): ?>
                                    <tr>
                                        <td><?= $count++ ?></td>
                                        <td><?= htmlspecialchars($sale['date']) ?></td>
                                        <td><?= htmlspecialchars($sale['category_id']) ?></td>
                                        <td><?= htmlspecialchars($sale['booking_id']) ?></td>
                                        <td><?= htmlspecialchars($sale['guest_name']) ?></td>
                                        <td><?= htmlspecialchars($sale['billing_amount']) ?></td>
                                        <td><?= htmlspecialchars($sale['tax']) ?></td>
                                        <td><?= htmlspecialchars($sale['net_amount']) ?></td>
                                        <td><?= htmlspecialchars($sale['payment_mode']) ?></td>
                                        <td>
                                            <?php
                                            $status = strtolower($sale['payment_status']);
                                            $badge_class = match ($status) {
                                                'paid' => 'success',
                                                'pending' => 'warning',
                                                'failed' => 'danger',
                                                default => 'secondary',
                                            };
                                            ?>
                                            <span class="badge bg-<?= $badge_class ?>">
                                                <?= ucfirst($sale['payment_status']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($sale['invoice_status']) ?></td>

                                        <td>
                                            <a href="add-sale.php?id=<?= encryptId($sale['id']) ?>" class="dropdown-item">Edit</a>
                                            <a href="delete-sale.php?id=<?= encryptId($sale['id']) ?>" class="dropdown-item" onclick="return confirm('Are you sure you want to delete this sale?')">Delete</a>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="14" class="text-center">No sales records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include required libraries -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#salesTable').DataTable({
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            ordering: true,
            searching: true,
            responsive: true,
            language: {
                searchPlaceholder: "Search records...",
                search: "_INPUT_",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
            },
        });

        $('#date_range').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            autoUpdateInput: false
        });

        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>