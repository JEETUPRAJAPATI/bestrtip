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
if (!$filter_col) $filter_col = '';
if (!$order_by) $order_by = 'desc';

// Additional filters
$status = $_GET['status'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
$guest_name = $_GET['guest_name'] ?? '';
$booking_year = $_GET['booking_year'] ?? '';
$property_id = $_GET['property_id'] ?? '';
$booking_month = $_GET['booking_month'] ?? '';

function applyPropertyBookingFilters($db, $search_string, $filter_col, $status, $guest_name, $property_id, $booking_year, $booking_month, $start_date, $end_date)
{
    if ($search_string && !empty($filter_col)) {
        $db->where($filter_col, '%' . $search_string . '%', 'LIKE');
    }

    if (!empty($status)) {
        $db->where('pb.status', $status);
    }

    if (!empty($guest_name)) {
        $db->where('pb.guest_name', '%' . $guest_name . '%', 'LIKE');
    }

    if (!empty($property_id) && ctype_digit((string)$property_id)) {
        $db->where('pb.property_id', (int)$property_id);
    }

    if (!empty($booking_year) && ctype_digit((string)$booking_year)) {
        $yearStart = $booking_year . '-01-01';
        $yearEnd = $booking_year . '-12-31';
        $db->where('pb.check_in_date', [$yearStart, $yearEnd], 'BETWEEN');
    }

    if (!empty($booking_month) && ctype_digit((string)$booking_month)) {
        $db->where('MONTH(pb.check_in_date)', (int)$booking_month, '=');
    }

    if (!empty($start_date) && !empty($end_date)) {
        $db->where('pb.check_in_date', [$start_date, $end_date], 'BETWEEN');
    } elseif (!empty($start_date)) {
        $db->where('pb.check_in_date', $start_date, '>=');
    } elseif (!empty($end_date)) {
        $db->where('pb.check_in_date', $end_date, '<=');
    }
}

// Load filter dropdown data
$filterDb = getDbInstance();
$properties = $filterDb->orderBy('hotel_name', 'ASC')->get('properties', null, ['id', 'hotel_name']);
$yearRows = $filterDb->rawQuery("SELECT DISTINCT YEAR(check_in_date) AS booking_year FROM property_booking WHERE check_in_date IS NOT NULL ORDER BY booking_year DESC");

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
    'pb.booking_token',
    'pb.due_amount',
    'pb.agent_name',
    'pb.created_at'
];

$db->join('properties p', 'pb.property_id = p.id', 'LEFT');
$db->pageLimit = PAGE_LIMIT;

applyPropertyBookingFilters($db, $search_string, $filter_col, $status, $guest_name, $property_id, $booking_year, $booking_month, $start_date, $end_date);


// Filter summary totals
$summaryDb = getDbInstance();
applyPropertyBookingFilters($summaryDb, $search_string, $filter_col, $status, $guest_name, $property_id, $booking_year, $booking_month, $start_date, $end_date);
$summaryRows = $summaryDb->arraybuilder()->get('property_booking pb', null, [
    'pb.no_of_rooms',
    'pb.status',
    'pb.total_amount',
    'pb.final_total',
    'pb.due_amount',
    'pb.booking_token'
]);

$total_rooms_summary = 0;
$confirmed_rooms_summary = 0;
$pending_rooms_summary = 0;
$cancel_rooms_summary = 0;
$confirmed_amount_summary = 0.0;
$pending_amount_summary = 0.0;
$cancel_amount_summary = 0.0;
$total_amount_summary = 0.0;
$total_pending_summary = 0.0;
$total_received_summary = 0.0;

foreach ($summaryRows as $summaryRow) {
    $rooms = (int)($summaryRow['no_of_rooms'] ?? 0);
    $statusValue = $summaryRow['status'] ?? '';
    $rowAmount = (float)((float)($summaryRow['final_total'] ?? 0) > 0 ? $summaryRow['final_total'] : $summaryRow['total_amount']);
    $rowPending = (float)($summaryRow['due_amount'] ?? 0);
    $rowReceived = (float)($summaryRow['booking_token'] ?? 0);

    $total_rooms_summary += $rooms;
    $total_amount_summary += $rowAmount;
    $total_pending_summary += $rowPending;
    $total_received_summary += $rowReceived;

    if ($statusValue === 'Confirmed') {
        $confirmed_rooms_summary += $rooms;
        $confirmed_amount_summary += $rowAmount;
    } elseif (in_array($statusValue, ['Hold', 'Enquiry'], true)) {
        $pending_rooms_summary += $rooms;
        $pending_amount_summary += $rowAmount;
    } elseif ($statusValue === 'Cancel') {
        $cancel_rooms_summary += $rooms;
        $cancel_amount_summary += $rowAmount;
    }
}

if (!empty($filter_col)) {
    $db->orderBy($filter_col, $order_by);
} else {
    // Default smart order: today first, then recent past dates, then upcoming dates.
    $db->orderBy('SIGN(DATEDIFF(pb.check_in_date,CURDATE()))', 'ASC', ['0', '-1', '1']);
    $db->orderBy('ABS(DATEDIFF(pb.check_in_date,CURDATE()))', 'ASC');
    $db->orderBy('pb.id', 'DESC');
}
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

    .table-responsive .table {
        min-width: 1250px;
    }

    .hotel-column {
        min-width: 180px;
    }

    .date-column {
        min-width: 115px;
        white-space: nowrap;
    }

    .property-filter-select {
        min-width: 260px;
    }

    .actions-column {
        position: sticky;
        right: 0;
        min-width: 130px;
        z-index: 3;
        background: #fff;
    }

    thead .actions-column {
        background: #22364d;
        z-index: 5;
    }

    tbody tr:nth-child(odd) .actions-column {
        background: #f8f9fa;
    }

    tbody tr:nth-child(even) .actions-column {
        background: #fff;
    }

    .status-dropdown .dropdown-toggle::after {
        margin-left: 0.4rem;
    }

    .status-dropdown .dropdown-menu {
        min-width: 130px;
    }

    .status-select {
        min-width: 128px;
        font-size: 12px;
        padding-top: 4px;
        padding-bottom: 4px;
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
                            <div class="col-md-2">
                                <label class="form-label">Year</label>
                                <select class="form-select" name="booking_year">
                                    <option value="">All Years</option>
                                    <?php foreach ($yearRows as $yearRow): ?>
                                        <?php if (!empty($yearRow['booking_year'])): ?>
                                            <option value="<?= (int)$yearRow['booking_year'] ?>" <?= ($booking_year == $yearRow['booking_year']) ? 'selected' : '' ?>>
                                                <?= (int)$yearRow['booking_year'] ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Month</label>
                                <select class="form-select" name="booking_month">
                                    <option value="">All Months</option>
                                    <?php foreach ([1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'] as $monthNo => $monthName): ?>
                                        <option value="<?= $monthNo ?>" <?= ($booking_month == $monthNo) ? 'selected' : '' ?>><?= $monthName ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Property</label>
                                <select class="form-select property-filter-select" name="property_id">
                                    <option value="">All Properties</option>
                                    <?php foreach ($properties as $property): ?>
                                        <option value="<?= (int)$property['id'] ?>" <?= ($property_id == $property['id']) ? 'selected' : '' ?>>
                                            <?= xss_clean($property['hotel_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-12 d-flex justify-content-between align-items-end">
                                <div>
                                    <button type="submit" class="btn btn-primary">🔍 Filter</button>
                                    <a href="property_booking_list.php" class="btn btn-secondary">Reset</a>
                                </div>
                                <div class="d-flex gap-2">
                                    
                                    <a href="export_booking_excel.php?<?= http_build_query($_GET) ?>" class="btn btn-success">📥 Export to Excel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row g-3 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="mb-1 text-muted">Total Rooms</p>
                            <h5 class="mb-0 fw-bold"><?= number_format($total_rooms_summary) ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="mb-1 text-muted">Confirmed Rooms</p>
                            <h5 class="mb-0 fw-bold text-success"><?= number_format($confirmed_rooms_summary) ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="mb-1 text-muted">Pending Rooms</p>
                            <h5 class="mb-0 fw-bold text-warning"><?= number_format($pending_rooms_summary) ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="mb-1 text-muted">Cancel Rooms</p>
                            <h5 class="mb-0 fw-bold text-danger"><?= number_format($cancel_rooms_summary) ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="mb-1 text-muted">Confirmed Amount</p>
                            <h5 class="mb-0 fw-bold text-success">₹<?= number_format($confirmed_amount_summary, 2) ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="mb-1 text-muted">Pending Amount</p>
                            <h5 class="mb-0 fw-bold text-warning">₹<?= number_format($pending_amount_summary, 2) ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <p class="mb-1 text-muted">Cancel Amount</p>
                            <h5 class="mb-0 fw-bold text-danger">₹<?= number_format($cancel_amount_summary, 2) ?></h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <h4 class="py-3 mb-4">Booking Management</h4>
                </div>
                <div class="col-auto">
                   <a href="send_today_booking_manager.php" class="btn btn-warning">📧 Today Booking Send</a>
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
                                    <th class="hotel-column">Hotel</th>
                                    <th>Booking ID</th>
                                    <th>Guest</th>
                                    <th class="date-column">Check-In</th>
                                    <th class="date-column">Check-Out</th>
                                    <th>Nights</th>
                                    <th class="rooms-column">Rooms</th>
                                    <th class="agent-column">Agent</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="actions-column">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $k = ($page != 1) ? (($page - 1) * PAGE_LIMIT) + 1 : 1; ?>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td><?= $k ?></td>
                                        <td class="hotel-column"><?= xss_clean($row['hotel_name']) ?></td>
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
                                        <td class="date-column"><?= !empty($row['check_in_date']) ? date('d-m-Y', strtotime($row['check_in_date'])) : '-' ?></td>
                                        <td class="date-column"><?= !empty($row['check_out_date']) ? date('d-m-Y', strtotime($row['check_out_date'])) : '-' ?></td>
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
                                            <select class="form-select form-select-sm status-select"
                                                data-booking-id="<?= (int)$row['id'] ?>">
                                                <?php foreach (['Confirmed', 'Hold', 'Enquiry', 'Cancel'] as $statusOption): ?>
                                                    <option value="<?= $statusOption ?>" <?= ($row['status'] === $statusOption) ? 'selected' : '' ?>>
                                                        <?= $statusOption ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="actions-column">
                                            <div class="d-flex gap-1">
                                                <a class="btn btn-sm btn-warning" href="add_property_booking.php?crm=<?= encryptId($row['id']) ?>" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if (($row['status'] ?? '') === 'Confirmed'): ?>
                                                    <a class="btn btn-sm btn-info" href="send_booking_invoice.php?crm=<?= encryptId($row['id']) ?>" title="Send Invoice">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!empty($row['due_amount']) && (float)$row['due_amount'] > 0): ?>
                                                    <a class="btn btn-sm btn-secondary" href="send_booking_payment_reminder.php?crm=<?= encryptId($row['id']) ?>" title="Pending Payment Notify">
                                                        <i class="fas fa-bell"></i>
                                                    </a>
                                                <?php endif; ?>
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
<script>
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', async function () {
            const bookingId = this.dataset.bookingId;
            const status = this.value;

            try {
                const response = await fetch('update_property_booking_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                    },
                    body: `booking_id=${encodeURIComponent(bookingId)}&status=${encodeURIComponent(status)}`
                });

                const data = await response.json();
                if (!data.success) {
                    alert(data.message || 'Failed to update status');
                    return;
                }

                window.location.reload();
            } catch (error) {
                alert('Unable to update booking status');
            }
        });
    });
</script>
<?php include BASE_PATH . '/includes/footer.php'; ?>