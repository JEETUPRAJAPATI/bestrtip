<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$date_range = $_GET['date_range'] ?? '';
$search_string = $_GET['search_string'] ?? '';
$filter_col = 'sales.date'; // Filtering by date
$order_by = 'desc';

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

if ($search_string) {
    $db->where($filter_col, "%$search_string%", 'LIKE');
}

if (!empty($date_range)) {
    [$start_date, $end_date] = explode(' - ', $date_range);
    $db->where('sales.date', $start_date, '>=');
    $db->where('sales.date', $end_date, '<=');
}

$db->orderBy($filter_col, $order_by);
$sales = $db->get('sales', null, $select);

$total_sales = array_sum(array_column($sales, 'net_amount'));

// Fetch expenses
$expenses = $db->get('expense');
$total_expenses = array_sum(array_column($expenses, 'net_amount'));

include BASE_PATH . '/includes/header.php';
?>
<style>
    .dt-buttons {
        float: right;
        margin-bottom: 10px;

    }

    .dt-button {
        color: #fff;
        background-color: #696cff;
        border-color: #696cff;
        box-shadow: 0 0.125rem 0.25rem 0 rgba(105, 108, 255, 0.4);
    }

    #salesExpenseChart {
        height: 470px !important;
    }

    .char-data {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
<!-- Filters & Overview -->
<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <form method="get">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Date Range</label>
                                <input type="text" name="date_range" id="date_range" class="form-control" placeholder="Select date range"
                                    value="<?= htmlspecialchars($date_range) ?>" autocomplete="off">
                            </div>
                            <div class="col-md-2 mt-4">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="overview.php" class="btn btn-secondary ml-2">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Overview Cards -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Sales</h5>
                            <p class="display-6">&#8377; <?= number_format($total_sales, 2) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Expenses</h5>
                            <p class="display-6">&#8377; <?= number_format($total_expenses, 2) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Card -->
            <div class="card mb-4">
                <div class="card-body char-data">
                    <canvas id="salesExpenseChart"></canvas>

                </div>
            </div>

            <!-- Sales Table -->
            <div class="card mb-4">
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
                                <th>Status</th>
                                <th>Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($sales): ?>
                                <?php $count = 1;
                                foreach ($sales as $sale): ?>
                                    <tr>
                                        <td><?= $count++ ?></td>
                                        <td><?= $sale['date'] ?></td>
                                        <td><?= $sale['category_id'] ?></td>
                                        <td><?= $sale['booking_id'] ?></td>
                                        <td><?= $sale['guest_name'] ?></td>
                                        <td><?= $sale['billing_amount'] ?></td>
                                        <td><?= $sale['tax'] ?></td>
                                        <td><?= $sale['net_amount'] ?></td>
                                        <td><?= $sale['payment_mode'] ?></td>
                                        <td><?= $sale['payment_status'] ?></td>
                                        <td><?= $sale['invoice_status'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center">No records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Expense Table -->
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="expenseTable" class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($expenses): ?>
                                <?php $count = 1;
                                foreach ($expenses as $expense): ?>
                                    <tr>
                                        <td><?= $count++ ?></td>
                                        <td><?= $expense['date'] ?></td>
                                        <td><?= $expense['net_amount'] ?></td>
                                        <td><?= $expense['description'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No records found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Scripts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vfs_fonts/2.0.0/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>

<script>
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

    // Chart.js
    const ctx = document.getElementById('salesExpenseChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Sales', 'Expenses'],
            datasets: [{
                label: 'Amount (in Rs)',
                data: [<?= $total_sales ?>, <?= $total_expenses ?>],
                backgroundColor: ['#4CAF50', '#F44336']
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Initialize DataTables with export options
    $(document).ready(function() {
        $('#salesTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['csv', 'excel'],
        });
        $('#expenseTable').DataTable({
            dom: 'Bfrtip',
            buttons: ['csv', 'excel'],
        });

    });
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>