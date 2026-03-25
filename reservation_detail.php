<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = getDbInstance();

// Get filters
$search_string = filter_input(INPUT_GET, 'search_string');
$filter_col     = filter_input(INPUT_GET, 'filter_col') ?: 'guest_name';
$order_by       = filter_input(INPUT_GET, 'order_by') ?: 'desc';
$page           = filter_input(INPUT_GET, 'page') ?: 1;

// Build query to fetch all property bookings
$db->join('properties p', 'pb.property_id = p.id', 'LEFT');
$select = [
    'pb.booking_id',
    'pb.guest_name',
    'pb.double_room_count',
    'pb.extra_bed_count',
    'pb.child_no_bed_count',
    'pb.single_room_count',
    'pb.check_in_date',
    'pb.check_out_date',
    'pb.meal_plan',
    'p.hotel_name',
    'pb.status',
    'pb.total_amount',
    'pb.booking_token',
    'pb.due_amount',
    'pb.agent_type',
    'pb.created_at'
];

// Apply search filter if provided
if ($search_string) {
    $db->where($filter_col, '%' . $search_string . '%', 'like');
}

$db->orderBy($filter_col, $order_by);
$db->pageLimit = PAGE_LIMIT;
$rows = $db->arraybuilder()->paginate('property_booking pb', $page, $select);
$total_pages = $db->totalPages;

include BASE_PATH . '/includes/header.php';
?>
<!-- Include Bootstrap JS and Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <h2 class="mb-3">All Property Bookings</h2>

            <form method="get" class="row g-2 mb-4">
                <div class="col-md-3">
                    <select name="filter_col" class="form-select">
                        <?php
                        $opts = [
                            'guest_name' => 'Guest Name',
                            'hotel_name' => 'Hotel Name',
                            'status'     => 'Booking Status',
                            'meal_plan'  => 'Meal Plan',
                        ];
                        foreach ($opts as $col => $label) {
                            $sel = ($filter_col === $col) ? 'selected' : '';
                            echo "<option value=\"$col\" $sel>$label</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search_string" class="form-control" placeholder="Search..."
                        value="<?= $search_string ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Search</button>
                </div>
                <div class="col-md-3 text-end">
                    <a href="reservation_detail.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive mb-4">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Booking ID</th>
                            <th>Guest Name</th>
                            <th>Room Counts (D/E/C/S)</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Meal Plan</th>
                            <th>Hotel</th>
                            <th>Status</th>
                            <th>Booking Amount</th>
                            <th>Advance</th>
                            <th>Due Amount</th>
                            <th>Agent</th>
                            <th>Booking Date</th>
                            <th>Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = ($page - 1) * PAGE_LIMIT + 1; ?>
                        <?php foreach ($rows as $r): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= xss_clean($r['booking_id']) ?></td>
                                <td><?= xss_clean($r['guest_name']) ?></td>
                                <td><?= (int)$r['double_room_count'] ?>/<?= (int)$r['extra_bed_count'] ?>/<?= (int)$r['child_no_bed_count'] ?>/<?= (int)$r['single_room_count'] ?></td>
                                <td><?= date('d/m/Y', strtotime($r['check_in_date'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($r['check_out_date'])) ?></td>
                                <td><?= xss_clean($r['meal_plan']) ?></td>
                                <td><?= xss_clean($r['hotel_name']) ?></td>
                                <td>
                                    <span class="badge <?= $r['status'] == 'Confirmed' ? 'bg-success' : ($r['status'] == 'Hold' ? 'bg-warning text-dark' : 'bg-secondary') ?>">
                                        <?= xss_clean($r['status']) ?>
                                    </span>
                                </td>
                                <td>₹<?= number_format($r['total_amount'], 2) ?></td>
                                <td>₹<?= number_format($r['booking_token'], 2) ?></td>
                                <td>₹<?= number_format($r['due_amount'], 2) ?></td>
                                <td><?= xss_clean($r['agent_type']) ?></td>
                                <td><?= date('d/m/Y', strtotime($r['created_at'])) ?></td>
                                <td>
                                    <?= ($r['due_amount'] <= 0) ? '<span class="badge bg-success">Paid</span>' : '<span class="badge bg-danger">Pending</span>' ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-grid gap-1 d-md-flex justify-content-md-center">
                                        <button
                                            class="btn btn-info btn-sm view-booking"
                                            data-bs-toggle="modal"
                                            data-bs-target="#bookingDetailModal"
                                            data-booking='<?= json_encode($r) ?>'>
                                            <i class="bi bi-eye"></i> View
                                        </button>

                                        <button
                                            class="btn btn-sm btn-outline-primary editStatusBtn"
                                            data-id="<?= $r['booking_id'] ?>"
                                            data-status="<?= $r['status'] ?>"
                                            data-payment="<?= ($r['due_amount'] <= 0) ? 'Paid' : 'Pending' ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editStatusModal">
                                            <i class="bi bi-pencil-square"></i> Edit Status
                                        </button>
                                    </div>
                                </td>


                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                <?php
                $params = $_GET;
                echo paginationLinks($page, $total_pages, 'reservation_detail.php', $params);
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-labelledby="bookingDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bookingDetailModalLabel">Booking Details</h5>
                <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6"><strong>Guest Name:</strong>
                        <div id="modalGuestName"></div>
                    </div>
                    <div class="col-md-6"><strong>Booking ID:</strong>
                        <div id="modalBookingId"></div>
                    </div>
                    <div class="col-md-6"><strong>Room Counts (D/E/C/S):</strong>
                        <div id="modalRoomCounts"></div>
                    </div>
                    <div class="col-md-6"><strong>Hotel:</strong>
                        <div id="modalHotelName"></div>
                    </div>
                    <div class="col-md-6"><strong>Meal Plan:</strong>
                        <div id="modalMealPlan"></div>
                    </div>
                    <div class="col-md-6"><strong>Status:</strong>
                        <div id="modalStatus"></div>
                    </div>
                    <div class="col-md-6"><strong>Check-In:</strong>
                        <div id="modalCheckIn"></div>
                    </div>
                    <div class="col-md-6"><strong>Check-Out:</strong>
                        <div id="modalCheckOut"></div>
                    </div>
                    <div class="col-md-6"><strong>Booking Amount:</strong>
                        <div id="modalTotalAmount"></div>
                    </div>
                    <div class="col-md-6"><strong>Advance:</strong>
                        <div id="modalAdvance"></div>
                    </div>
                    <div class="col-md-6"><strong>Due:</strong>
                        <div id="modalDueAmount"></div>
                    </div>
                    <div class="col-md-6"><strong>Agent:</strong>
                        <div id="modalAgent"></div>
                    </div>
                    <div class="col-md-6"><strong>Booking Date:</strong>
                        <div id="modalBookingDate"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Status Modal -->
<div class="modal fade" id="editStatusModal" tabindex="-1" aria-labelledby="editStatusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="update_reservation_status.php">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Booking & Payment Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="booking_id" id="modal_booking_id">
                    <div class="mb-3">
                        <label for="booking_status" class="form-label">Booking Status</label>
                        <select class="form-select" name="booking_status" id="modal_booking_status">
                            <option value="Confirmed">Confirmed</option>
                            <option value="Hold">Hold</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="payment_status" class="form-label">Payment Status</label>
                        <select class="form-select" name="payment_status" id="modal_payment_status">
                            <option value="Paid">Paid</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Script to fill modal -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".view-booking").forEach(button => {
            button.addEventListener("click", function() {
                const data = JSON.parse(this.dataset.booking);

                document.getElementById("modalGuestName").innerText = data.guest_name || "-";
                document.getElementById("modalBookingId").innerText = data.booking_id || "-";
                document.getElementById("modalRoomCounts").innerText = `${data.double_room_count || 0}/${data.extra_bed_count || 0}/${data.child_no_bed_count || 0}/${data.single_room_count || 0}`;
                document.getElementById("modalHotelName").innerText = data.hotel_name || "-";
                document.getElementById("modalMealPlan").innerText = data.meal_plan || "-";
                document.getElementById("modalStatus").innerText = data.status || "-";
                document.getElementById("modalCheckIn").innerText = formatDate(data.check_in_date);
                document.getElementById("modalCheckOut").innerText = formatDate(data.check_out_date);
                document.getElementById("modalTotalAmount").innerText = "₹" + parseFloat(data.total_amount || 0).toFixed(2);
                document.getElementById("modalAdvance").innerText = "₹" + parseFloat(data.booking_token || 0).toFixed(2);
                document.getElementById("modalDueAmount").innerText = "₹" + parseFloat(data.due_amount || 0).toFixed(2);
                document.getElementById("modalAgent").innerText = data.agent_type || "-";
                document.getElementById("modalBookingDate").innerText = formatDate(data.created_at);
            });
        });

        function formatDate(dateStr) {
            if (!dateStr) return "-";
            const d = new Date(dateStr);
            return `${d.getDate().toString().padStart(2, '0')}/${
            (d.getMonth() + 1).toString().padStart(2, '0')}/${d.getFullYear()}`;
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.editStatusBtn');
        buttons.forEach(btn => {
            btn.addEventListener('click', function() {
                const bookingId = this.getAttribute('data-id');
                const status = this.getAttribute('data-status');
                const payment = this.getAttribute('data-payment');

                document.getElementById('modal_booking_id').value = bookingId;
                document.getElementById('modal_booking_status').value = status;
                document.getElementById('modal_payment_status').value = payment;
            });
        });
    });
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>