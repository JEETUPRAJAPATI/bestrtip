<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get Input
$search_string = filter_input(INPUT_GET, 'search_string');
$filter_col = filter_input(INPUT_GET, 'filter_col') ?? 'vendor.name';
$order_by = filter_input(INPUT_GET, 'order_by') ?? 'desc';
$page = filter_input(INPUT_GET, 'page') ?? 1;

$date_range = filter_input(INPUT_GET, 'date_range');
// DB Instance
$db = getDbInstance();

$select = [
    'expense.id',
    'expense.date',
    'vendor.name AS vendor_name',
    'vendor_category.name AS category_name',
    'expense.description',
    'expense.billing_amount',
    'expense.tax',
    'expense.net_amount',
    'expense.location',
    'expense.payment_mode',
    'expense.transaction_id',
    'expense.status',
    'expense.booking_id',
    'expense.group_name',
    'expense.bill_upload',
    'expense.created_at'
];

// Joins
$db->join('vendor', 'expense.vendor_id = vendor.id', 'LEFT');
$db->join('vendor_category', 'vendor.category_id = vendor_category.id', 'LEFT');

// Search
if ($search_string) {
    $db->where($filter_col, '%' . $search_string . '%', 'like');
}
if (!empty($date_range)) {
    [$start_date, $end_date] = explode(' - ', $date_range);
    $db->where('expense.created_at', $start_date, '>=');
    $db->where('expense.created_at', $end_date, '<=');
}
// Order & Pagination
$db->orderBy($filter_col, $order_by);
$records = $db->arraybuilder()->paginate('expense', $page, $select);
$expenses = $db->get('expense');
$total_expenses = array_sum(array_column($expenses, 'net_amount'));
$total_billing_amount = array_sum(array_column($expenses, 'billing_amount'));
$total_tax = array_sum(array_column($expenses, 'tax'));
$total_pages = $db->totalPages;

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
                                <label class="form-label">Date Range</label>
                                <input type="text" name="date_range" id="date_range" class="form-control" placeholder="Select date range"
                                    value="<?= htmlspecialchars($date_range ?? '') ?>" autocomplete="off">
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <a href="expense.php" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Page Title & Add Button -->
            <div class="row">
                <div class="col">
                    <h4 class="py-3 mb-4">Expense List</h4>
                </div>
                <div class="col-auto">
                    <a href="add-expense.php" class="btn btn-primary">Add Expense</a>
                </div>
            </div>

            <!-- Overview Cards -->
            <!-- Overview Cards -->
            <div class="row mb-4">
                <!-- Total Expenses Card -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Expenses</h5>
                            <p class="display-6">&#8377; <?= number_format($total_expenses, 2) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Billing Amount Card -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Billing Amount</h5>
                            <p class="display-6">&#8377; <?= number_format(array_sum(array_column($expenses, 'billing_amount')), 2) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Tax Card -->
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Tax</h5>
                            <p class="display-6">&#8377; <?= number_format(array_sum(array_column($expenses, 'tax')), 2) ?></p>
                        </div>
                    </div>
                </div>
            </div>


            <?php include BASE_PATH . '/includes/flash_messages.php'; ?>

            <!-- Expense Table -->
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="expenseTable" class="table">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Vendor</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Billing Amount</th>
                                <th>Tax</th>
                                <th>Net Amount</th>
                                <th>Status</th>
                                <th>Attachment</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($records): ?>
                                <?php foreach ($records as $item): ?>
                                    <?php
                                    $count = ($page - 1) * PAGE_LIMIT + 1;

                                    ?>
                                    <tr>
                                        <td><?= $count++ ?></td>
                                        <td><?= htmlspecialchars($item['date']) ?></td>
                                        <td><?= htmlspecialchars($item['vendor_name']) ?></td>
                                        <td><?= htmlspecialchars($item['category_name']) ?></td>
                                        <td><?= htmlspecialchars($item['description']) ?></td>
                                        <td><?= htmlspecialchars($item['billing_amount']) ?></td>
                                        <td><?= htmlspecialchars($item['tax']) ?></td>
                                        <td><?= htmlspecialchars($item['net_amount']) ?></td>
                                        <td><?= htmlspecialchars($item['status']) ?></td>
                                        <td>
                                            <?php if (!empty($item['bill_upload'])): ?>
                                                <a href="<?= htmlspecialchars($item['bill_upload']) ?>" target="_blank">View</a>
                                            <?php else: ?>
                                                N/A
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($item['created_at']) ?></td>
                                        <td>
                                            <a href="add-expense.php?id=<?= encryptId($item['id']) ?>" class="dropdown-item">Edit</a>
                                            <a href="delete-expense.php?id=<?= encryptId($item['id']) ?>" class="dropdown-item" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="text-center">No expense records found</td>
                                </tr>
                            <?php endif; ?>

                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="text-end mt-3">
                        <?= paginationLinks($page, $total_pages, 'expense.php') ?>
                    </div>
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
        var table = $('#expenseTable').DataTable({
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

        // Reinitialize the DataTable after the page is loaded with data
        $('#expenseTable').on('draw.dt', function() {
            table.columns.adjust();
        });

        // Initialize Date Range Picker
        $('#date_range').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            autoUpdateInput: false
        });

        // Apply date range filter
        $('#date_range').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        // Reset date range filter
        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>