<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

// Error reporting (only for development)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize variables for filters with proper sanitization
$status = filter_input(INPUT_GET, 'status');
$start_date = filter_input(INPUT_GET, 'start_date');
$end_date = filter_input(INPUT_GET, 'end_date');
$vendor_name = filter_input(INPUT_GET, 'vendor_name');
$order_number = filter_input(INPUT_GET, 'order_number');
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;

$db = getDbInstance();

// Base query - select from purchase_orders with LEFT JOIN on vendor
$db->join('vendor v', 'o.vendor_id = v.id', 'LEFT');

// Apply filters if they exist
if (!empty($status)) {
    $db->where('o.status', $status);
}

if (!empty($start_date) && !empty($end_date)) {
    $db->where('o.order_date', array(date('Y-m-d', strtotime($start_date)), date('Y-m-d', strtotime($end_date))), 'BETWEEN');
} elseif (!empty($start_date)) {
    $db->where('o.order_date', date('Y-m-d', strtotime($start_date)), '>=');
} elseif (!empty($end_date)) {
    $db->where('o.order_date', date('Y-m-d', strtotime($end_date)), '<=');
}

if (!empty($vendor_name)) {
    $db->where('v.name', '%' . $vendor_name . '%', 'LIKE');
}

if (!empty($order_number)) {
    $db->where('o.order_number', '%' . $order_number . '%', 'LIKE');
}

// Set pagination and ordering
$db->pageLimit = PAGE_LIMIT;
$db->orderBy('o.order_date', 'DESC');

// Select the columns you need (better practice than selecting *)
$columns = array(
    'o.id',
    'o.order_date',
    'o.order_by_name',
    'o.order_number',
    'o.signature',
    'o.vendor_id',
    'o.vendor_contact',
    'o.delivery_date',
    'o.delivered_by_name',
    'o.delivery_signature',
    'o.status',
    'o.bill_attached',
    'o.created_at',
    'o.updated_at',
    'v.name as vendor_name',
    'v.email',
    'v.mobile',
    'v.category_id'
);

// Get paginated results
$orders = $db->arraybuilder()->paginate('purchase_orders o', $page, $columns);

// echo "<pre>";
// print_r($orders);
// die();
$total_pages = $db->totalPages;

include BASE_PATH . '/includes/header.php';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<div class="layout-page">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col">
                <h4 class="py-3 mb-4">Purchase Orders</h4>
            </div>
            <div class="col-auto">
                <a href="add_purchase.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New
                </a>
            </div>
        </div>

        <?php include BASE_PATH . '/includes/flash_messages.php'; ?>

        <!-- Filter Section -->
        <form method="get" action="">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Order Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Statuses</option>
                                <option value="Pending" <?= ($status == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="Completed" <?= ($status == 'Completed') ? 'selected' : '' ?>>Completed</option>
                                <option value="Cancelled" <?= ($status == 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" class="form-control" name="start_date" value="<?= $start_date ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" class="form-control" name="end_date" value="<?= $end_date ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Vendor Name</label>
                            <input type="text" class="form-control" name="vendor_name" value="<?= $vendor_name ?>" placeholder="Search vendor...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Order Number</label>
                            <input type="text" class="form-control" name="order_number" value="<?= $order_number ?>" placeholder="Search order number...">
                        </div>
                        <div class="col-md-12 d-flex justify-content-between align-items-end">
                            <div>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i>Filter</button>
                                <a href="purchase_list.php" class="btn btn-secondary"><i class="fas fa-sync-alt me-2"></i>Reset</a>
                            </div>
                            <?php if (!empty($status) || !empty($start_date) || !empty($end_date) || !empty($vendor_name) || !empty($order_number)): ?>
                                <div class="text-muted">
                                    <small>Filtered results</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="data-table">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="10%">Order Date</th>
                                <th width="10%">Order No</th>
                                <th width="15%">Order By</th>
                                <th width="15%">Vendor</th>
                                <th width="10%">Status</th>
                                <th width="10%">Delivery Date</th>
                                <th width="10%">Items</th>
                                <th width="10%">Bill Attached</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="10" class="text-center">No purchase orders found</td>
                                </tr>
                            <?php else: ?>
                                <?php
                                // First get all order IDs to optimize queries
                                $orderIds = array_column($orders, 'id');

                                // Get item counts for all orders in a single query
                                $db->where('order_id', $orderIds, 'IN');
                                $itemCounts = $db->map('order_id')->ArrayBuilder()->get('purchase_order_items', null, 'order_id, COUNT(*) as item_count');

                                foreach ($orders as $order):
                                    // Get count from our pre-loaded data or default to 0
                                    $item_count = $itemCounts[$order['id']] ?? 0;

                                    // Status badge color
                                    $status_options = [
                                        'Pending' => 'Pending',
                                        'Completed' => 'Completed',
                                        'Cancelled' => 'Cancelled',
                                        'Received' => 'Received'
                                    ];
                                ?>
                                    <tr>
                                        <td><?= $order['id'] ?></td>
                                        <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                                        <td><?= htmlspecialchars($order['order_number']) ?></td>
                                        <td><?= htmlspecialchars($order['order_by_name']) ?></td>
                                        <td><?= htmlspecialchars($order['vendor_name'] ?? 'N/A') ?></td>
                                        <td>
                                            <form method="post" action="update_orders_status.php" class="status-form">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                <select name="status" class="form-select form-select-sm status-select"
                                                    onchange="this.form.submit()" style="width: 120px;">
                                                    <?php foreach ($status_options as $value => $class): ?>
                                                        <option value="<?= $value ?>"
                                                            <?= ($order['status'] == $value) ? 'selected' : '' ?>
                                                            data-class="<?= $class ?>">
                                                            <?= $value ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </form>
                                        </td>
                                        <td><?= $order['delivery_date'] ? date('d M Y', strtotime($order['delivery_date'])) : 'N/A' ?></td>
                                        <td><?= $item_count ?></td>
                                        <td>
                                            <?php if ($order['bill_attached']): ?>
                                                <span class="badge bg-success">Yes</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="add_purchase.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-primary me-2" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="generate_purchase_pdf.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-info me-2" title="Generate PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                                <a href="delete_purchase.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this order?')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p class="text-muted">
                                Showing <?= (($page - 1) * PAGE_LIMIT) + 1 ?> to
                                <?= min($page * PAGE_LIMIT, $db->totalCount) ?> of
                                <?= $db->totalCount ?> entries
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="float-end">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" aria-label="First">
                                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                                </a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" aria-label="Last">
                                                    <span aria-hidden="true">&raquo;&raquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update select appearance based on selected option
        document.querySelectorAll('.status-select').forEach(select => {
            updateSelectStyle(select);

            select.addEventListener('change', function() {
                updateSelectStyle(this);
            });
        });

        function updateSelectStyle(select) {
            const selectedOption = select.options[select.selectedIndex];
            const badgeClass = selectedOption.getAttribute('data-class');
            select.style.backgroundColor = getComputedStyle(document.documentElement)
                .getPropertyValue(`--bs-${badgeClass.replace('bg-', '')}`);
        }
    });
</script>
<?php include BASE_PATH . '/includes/footer.php'; ?>