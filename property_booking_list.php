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

// Load filter dropdown data
$filterDb = getDbInstance();
$properties = $filterDb->orderBy('hotel_name', 'ASC')->get('properties', null, ['id', 'hotel_name']);

// Additional filters
$status = $_GET['status'] ?? '';
$guest_name = $_GET['guest_name'] ?? '';
$property_id = $_GET['property_id'] ?? '';
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$date_range = $_GET['date_range'] ?? '';
$booking_timeline = $_GET['booking_timeline'] ?? '';

// Parsing Range fields
function parseRange($range) {
    if (empty($range)) return ['', ''];
    $range = str_replace(['%20to%20', '+to+'], ' to ', $range);
    if (strpos($range, ' to ') !== false) {
        $parts = explode(' to ', $range);
        $start_ts = strtotime(trim($parts[0]));
        $end_ts = strtotime(trim($parts[1]));
        return [($start_ts ? date('Y-m-d', $start_ts) : ''), ($end_ts ? date('Y-m-d', $end_ts) : '')];
    }
    $ts = strtotime(trim($range));
    $date = ($ts) ? date('Y-m-d', $ts) : '';
    return [$date, $date];
}

list($range_start, $range_end) = parseRange($date_range);


// Property ID is empty by default (All Properties)


function applyPropertyBookingFilters($db, $search_string, $filter_col, $status, $guest_name, $property_id, $check_in, $check_out, $range_start, $range_end, $booking_timeline)
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



    if (!empty($range_start) && !empty($range_end)) {
        $db->where('pb.check_in_date', [$range_start, $range_end], 'BETWEEN');
    }

    if (!empty($check_in)) {
        $db->where('pb.check_in_date', date('Y-m-d', strtotime($check_in)));
    }

    if (!empty($check_out)) {
        $db->where('pb.check_out_date', date('Y-m-d', strtotime($check_out)));
    }

    if (!empty($booking_timeline)) {
        $today = date('Y-m-d');
        if ($booking_timeline === 'recent') {
            // Only apply 'recent' if no custom date ranges are set
            if (empty($start_date) && empty($bd_start)) {
                $recentStart = date('Y-m-d', strtotime('-7 days'));
                $recentEnd = date('Y-m-d', strtotime('+6 days'));
                $db->where('pb.check_in_date', [$recentStart, $recentEnd], 'BETWEEN');
            }
        } elseif ($booking_timeline === 'past') {
            $db->where('pb.check_out_date', $today, '<');
        } elseif ($booking_timeline === 'upcoming') {
            $db->where('pb.check_in_date', $today, '>');
        }
    }
}


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

applyPropertyBookingFilters($db, $search_string, $filter_col, $status, $guest_name, $property_id, $check_in, $check_out, $range_start, $range_end, $booking_timeline);


// Filter summary totals
$summaryDb = getDbInstance();
$summaryDb->join('properties p', 'pb.property_id = p.id', 'LEFT');
applyPropertyBookingFilters($summaryDb, $search_string, $filter_col, $status, $guest_name, $property_id, $check_in, $check_out, $range_start, $range_end, $booking_timeline);
$summaryRows = $summaryDb->arraybuilder()->get('property_booking pb', null, [
    'pb.no_of_rooms',
    'pb.single_room_count',
    'pb.double_room_count',
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
    $rooms = !empty($summaryRow['no_of_rooms']) ? (int)$summaryRow['no_of_rooms'] : ((int)$summaryRow['single_room_count'] + (int)$summaryRow['double_room_count']);
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
    if ($booking_timeline === 'recent') {
        // Recent = latest bookings first
        $db->orderBy('pb.created_at', 'DESC');
        $db->orderBy('pb.id', 'DESC');
    } elseif ($booking_timeline === 'past') {
        // Past = most recently completed first
        $db->orderBy('pb.check_out_date', 'DESC');
        $db->orderBy('pb.id', 'DESC');
    } elseif ($booking_timeline === 'upcoming') {
        // Upcoming = nearest check-in first
        $db->orderBy('pb.check_in_date', 'ASC');
        $db->orderBy('pb.id', 'DESC');
    } else {
        // Default smart order: today first, then recent past dates, then upcoming dates.
        $db->orderBy('SIGN(DATEDIFF(pb.check_in_date,CURDATE()))', 'ASC', ['0', '-1', '1']);
        $db->orderBy('ABS(DATEDIFF(pb.check_in_date,CURDATE()))', 'ASC');
        $db->orderBy('pb.id', 'DESC');
    }
}
$rows = $db->arraybuilder()->paginate('property_booking pb', $page, $select);
$total_pages = $db->totalPages;

include BASE_PATH . '/includes/header.php';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
        width: 100%;
        min-width: 0;
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
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">BOOKING STATUS</label>
                                <select class="form-select" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="Confirmed" <?= ($status == 'Confirmed') ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="Hold" <?= ($status == 'Hold') ? 'selected' : '' ?>>Hold</option>
                                    <option value="Enquiry" <?= ($status == 'Enquiry') ? 'selected' : '' ?>>Enquiry</option>
                                    <option value="Cancel" <?= ($status == 'Cancel') ? 'selected' : '' ?>>Cancel</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">PROPERTY</label>
                                <select class="form-select property-filter-select" name="property_id">
                                    <option value="">All Properties</option>
                                    <?php foreach ($properties as $prop): ?>
                                        <option value="<?= $prop['id'] ?>" <?= ($property_id == $prop['id']) ? 'selected' : '' ?>>
                                            <?= xss_clean($prop['hotel_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">CHECK-IN</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="text" class="form-control flatpickr-date" name="check_in" placeholder="Select Date" value="<?= htmlspecialchars($check_in) ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">CHECK-OUT</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="text" class="form-control flatpickr-date" name="check_out" placeholder="Select Date" value="<?= htmlspecialchars($check_out) ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">DATE RANGE</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                                    <input type="text" class="form-control flatpickr-range" name="date_range" placeholder="Select Range" value="<?= htmlspecialchars($date_range) ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">GUEST NAME</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" name="guest_name" placeholder="Search guest..." value="<?= htmlspecialchars($_GET['guest_name'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">BOOKING TIMELINE</label>
                                <select class="form-select" name="booking_timeline" id="bookingTimeline">
                                    <option value="">All Bookings</option>
                                    <option value="recent" <?= ($booking_timeline === 'recent') ? 'selected' : '' ?>>Recent Bookings</option>
                                    <option value="past" <?= ($booking_timeline === 'past') ? 'selected' : '' ?>>Past Bookings</option>
                                    <option value="upcoming" <?= ($booking_timeline === 'upcoming') ? 'selected' : '' ?>>Upcoming Bookings</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                                <a href="property_booking_list.php" class="btn btn-outline-secondary w-100">Reset</a>
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
                                    <th>Due</th>
                                    <th>Status</th>
                                    <th class="actions-column">Action</th>
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
                                            ₹<?= number_format($row['due_amount'] ?? 0, 2) ?>
                                            <?php if (!empty($row['receipt_amount']) && $row['receipt_amount'] > 0): ?>
                                                <br><small class="text-success">Paid: ₹<?= number_format($row['receipt_amount'], 2) ?></small>
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
                                                <a class="btn btn-sm btn-info" href="send_booking_email.php?crm=<?= encryptId($row['id']) ?>" title="Send Email">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                                <a class="btn btn-sm btn-primary" href="view_property_invoice.php?crm=<?= encryptId($row['id']) ?>" target="_blank" title="View Invoice">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                                <a class="btn btn-sm btn-secondary" href="send_payment_reminder.php?crm=<?= encryptId($row['id']) ?>" title="Reminder">
                                                    <i class="fas fa-bell"></i>
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
<script>
    const bookingTimeline = document.getElementById('bookingTimeline');
    if (bookingTimeline && bookingTimeline.form) {
        bookingTimeline.addEventListener('change', function() {
            bookingTimeline.form.submit();
        });
    }

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

                if (data.message) {
                    alert(data.message);
                }
                window.location.reload();
            } catch (error) {
                alert('Unable to update booking status');
            }
        });
    });
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d-m-Y",
            allowInput: true
        });

        flatpickr(".flatpickr-range", {
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d-M-Y",
            allowInput: true
        });
    });
</script>
<?php include BASE_PATH . '/includes/footer.php'; ?>