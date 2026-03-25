<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Get filter values
$search_string = filter_input(INPUT_GET, 'search_string');
$filter_col = filter_input(INPUT_GET, 'filter_col');
$order_by = filter_input(INPUT_GET, 'order_by');
$page = filter_input(INPUT_GET, 'page');
if (!$page) $page = 1;
if (!$filter_col) $filter_col = 'pb.id';
if (!$order_by) $order_by = 'desc';

// Additional filters
$status = $_GET['status'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$guest_name = $_GET['guest_name'] ?? '';

$db = getDbInstance();
$select = [
    'pb.id',
    'p.hotel_name',
    'pb.booking_id',
    'pb.guest_name',
    'pb.check_in_date',
    'pb.check_out_date',
    'pb.no_of_nights',
    'pb.single_room_count',
    'pb.double_room_count',
    'pb.no_of_rooms',
    'pb.total_pax',
    'pb.agent_type',
    'pb.agent_email',
    'pb.guest_email',
    'pb.meal_plan',
    'pb.status',
    'pb.total_amount',
    'pb.final_total',
    'pb.agent_name',
    'pb.created_at'
];

$db->join('properties p', 'pb.property_id = p.id', 'LEFT');
$db->pageLimit = PAGE_LIMIT;

if ($search_string) {
    $db->where($filter_col, '%' . $search_string . '%', 'LIKE');
}

if (!empty($status)) {
    $db->where('pb.status', $status);
}

if (!empty($guest_name)) {
    $db->where('pb.guest_name', '%' . $guest_name . '%', 'LIKE');
}

if (!empty($start_date) && !empty($end_date)) {
    $db->where('pb.check_in_date', [$start_date, $end_date], 'BETWEEN');
} elseif (!empty($start_date)) {
    $db->where('pb.check_in_date', $start_date, '>=');
} elseif (!empty($end_date)) {
    $db->where('pb.check_in_date', $end_date, '<=');
}

$db->orderBy($filter_col, $order_by);
$rows = $db->arraybuilder()->paginate('property_booking pb', $page, $select);
$total_pages = $db->totalPages;

include BASE_PATH . '/includes/header.php';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    @media (max-width: 768px) {

        .agent-column,
        .rooms-column {
            display: none;
        }
    }

    .badge-confirmed {
        background-color: #28a745;
    }

    .badge-hold {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-enquiry {
        background-color: #6c757d;
    }
</style>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <form method="get" action="">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Booking Status</label>
                                <select class="form-select" name="status">
                                    <option value="">All</option>
                                    <option value="Confirmed" <?= ($status == 'Confirmed') ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="Hold" <?= ($status == 'Hold') ? 'selected' : '' ?>>Hold</option>
                                    <option value="Enquiry" <?= ($status == 'Enquiry') ? 'selected' : '' ?>>Enquiry</option>
                                    <option value="Cancel" <?= ($status == 'Cancel') ? 'selected' : '' ?>>Cancel</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" value="<?= $start_date ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date" value="<?= $end_date ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Guest Name</label>
                                <input type="text" class="form-control" name="guest_name" value="<?= $guest_name ?>">
                            </div>
                            <div class="col-md-12 d-flex justify-content-between align-items-end">
                                <div>
                                    <button type="submit" class="btn btn-primary">🔍 Filter</button>
                                    <a href="property_booking_list.php" class="btn btn-secondary">Reset</a>
                                </div>
                                <a href="export_booking_excel.php?<?= http_build_query($_GET) ?>" class="btn btn-success">📥 Export to Excel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col">
                    <h4 class="py-3 mb-4">Booking Management</h4>
                </div>
                <div class="col-auto">
                    <a href="add_property_booking.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Booking
                    </a>
                </div>
            </div>
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success'];
                    unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['failure'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['failure'];
                    unset($_SESSION['failure']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Hotel</th>
                                    <th>Booking ID</th>
                                    <th>Guest</th>
                                    <th>Check-In</th>
                                    <th>Check-Out</th>
                                    <th>Nights</th>
                                    <th class="rooms-column">Rooms</th>
                                    <th class="agent-column">Agent</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $k = ($page != 1) ? (($page - 1) * PAGE_LIMIT) + 1 : 1; ?>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td><?= $k ?></td>
                                        <td><?= xss_clean($row['hotel_name']) ?></td>
                                        <td><?= xss_clean($row['booking_id']) ?></td>
                                        <td>
                                            <?= xss_clean($row['guest_name']) ?>
                                            <?php if (!empty($row['guest_email'])): ?>
                                                <br><small class="text-muted"><?= xss_clean($row['guest_email']) ?></small>
                                            <?php endif; ?>
                                            <?php if (!empty($row['total_pax'])): ?>
                                                <br><span class="badge bg-secondary">Pax: <?= $row['total_pax'] ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d M Y', strtotime($row['check_in_date'])) ?></td>
                                        <td><?= date('d M Y', strtotime($row['check_out_date'])) ?></td>
                                        <td><?= xss_clean($row['no_of_nights']) ?></td>
                                        <td class="rooms-column">
                                            <?= !empty($row['no_of_rooms']) ? xss_clean($row['no_of_rooms']) : ($row['single_room_count'] + $row['double_room_count']) ?>
                                            <small class="text-muted d-block">
                                                (S: <?= $row['single_room_count'] ?>, D: <?= $row['double_room_count'] ?>)
                                            </small>
                                        </td>
                                        <td class="agent-column">
                                            <?= xss_clean($row['agent_type'] ?? 'Direct') ?>
                                            <?php if (!empty($row['agent_name'])): ?>
                                                <br><small class="text-muted"><?= xss_clean($row['agent_name']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['final_total']) && $row['final_total'] > 0): ?>
                                                ₹<?= number_format($row['final_total'], 2) ?>
                                                <br><small class="text-muted text-decoration-line-through">₹<?= number_format($row['total_amount'], 2) ?></small>
                                            <?php else: ?>
                                                ₹<?= number_format($row['total_amount'], 2) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= strtolower($row['status']) ?>">
                                                <?= xss_clean($row['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a class="btn btn-sm btn-warning" href="add_property_booking.php?crm=<?= encryptId($row['id']) ?>" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a class="btn btn-sm btn-info" href="send_booking_email.php?crm=<?= encryptId($row['id']) ?>" title="Email">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                                <a class="btn btn-sm btn-danger" href="delete_booking.php?crm=<?= encryptId($row['id']) ?>"
                                                    onclick="return confirm('Are you sure you want to delete this booking?')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php $k++;
                                endforeach; ?>
                            </tbody>
                        </table>
                    </div>

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
                                <?= paginationLinks($page, $total_pages, 'property_booking_list.php'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include BASE_PATH . '/includes/footer.php'; ?>