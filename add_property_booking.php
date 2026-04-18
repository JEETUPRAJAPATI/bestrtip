<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$db = getDbInstance();
$properties = $db->get('properties');

function getPropertyRoomAvailability($propertyId, $checkInDate, $checkOutDate, $excludeBookingId = null)
{
    $db = getDbInstance();
    $property = $db->where('id', $propertyId)->getOne('properties', ['no_of_rooms']);

    if (!$property || empty($checkInDate) || empty($checkOutDate)) {
        return [
            'total_rooms' => 0,
            'booked_rooms' => 0,
            'available_rooms' => 0,
        ];
    }

    $db->where('property_id', (int)$propertyId);
    $db->where('status', ['Confirmed', 'Hold'], 'IN');
    $db->where('check_in_date', $checkOutDate, '<=');
    $db->where('check_out_date', $checkInDate, '>=');

    if (!empty($excludeBookingId)) {
        $db->where('id', (int)$excludeBookingId, '!=');
    }

    $occupied = $db->getValue('property_booking', 'COALESCE(SUM(no_of_rooms), 0)');
    $totalRooms = (int)($property['no_of_rooms'] ?? 0);
    $bookedRooms = (int)$occupied;

    return [
        'total_rooms' => $totalRooms,
        'booked_rooms' => $bookedRooms,
        'available_rooms' => max(0, $totalRooms - $bookedRooms),
    ];
}

// Check if this is edit mode
$booking = [];
$id = isset($_GET['crm']) && !empty($_GET['crm']) ? decryptId($_GET['crm']) : "";
$edit = false;
$data = [];
if (!empty($id)) {
    $edit = true;
    $db->where('id', $id);
    $booking = $db->getOne('property_booking');
}

// ── NEW: decode existing services for edit-mode pre-fill ──
$existingServices = [];
if (!empty($booking['extra_services'])) {
    $decoded = json_decode($booking['extra_services'], true);
    if (is_array($decoded)) $existingServices = $decoded;
}
if (empty($existingServices)) $existingServices = [['name' => '', 'price' => '']];
// ─────────────────────────────────────────────────────────



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = array_map(function($val) { return is_string($val) ? trim($val) : $val; }, $_POST);

    $computedRooms = (int)($data['double_room_count'] ?? 0) + (int)($data['single_room_count'] ?? 0);
    $data['no_of_rooms'] = $computedRooms;

    $roomTotal = (float)($data['double_room_total'] ?? 0)
        + (float)($data['extra_bed_total'] ?? 0)
        + (float)($data['child_no_bed_total'] ?? 0)
        + (float)($data['single_total'] ?? 0);
    $serviceTotal = 0;
    foreach (($_POST['service_price'] ?? []) as $servicePrice) {
        $serviceTotal += (float)$servicePrice;
    }
    $discountPercent = (float)($data['discount_percent'] ?? 0);
    $discountAmount = $roomTotal * ($discountPercent / 100);

    $data['total_amount'] = $roomTotal;
    $data['extra_services_total'] = $serviceTotal;
    $data['discount_amount'] = $discountAmount;
    $data['final_total'] = max(0, $roomTotal + $serviceTotal - $discountAmount);
    $data['due_amount'] = max(0, $data['final_total'] - (float)($data['booking_token'] ?? 0));

    $propertyId = (int)($data['property_id'] ?? 0);
    $checkInDate = $data['check_in_date'] ?? '';
    $checkOutDate = $data['check_out_date'] ?? '';
    $availability = getPropertyRoomAvailability($propertyId, $checkInDate, $checkOutDate, $edit ? $id : null);

    if ($propertyId && $checkInDate && $checkOutDate && $computedRooms > $availability['available_rooms']) {
        $_SESSION['failure'] = 'Only ' . $availability['available_rooms'] . ' room(s) available for the selected property and dates.';
        $booking = array_merge($booking, $data);
        $edit = !empty($id);
        $existingServices = [];
        if (!empty($booking['extra_services'])) {
            $decoded = json_decode($booking['extra_services'], true);
            if (is_array($decoded)) $existingServices = $decoded;
        }
        if (empty($existingServices)) $existingServices = [['name' => '', 'price' => '']];
    } else {
        // ── NEW: Encode repeatable extra services as JSON ──
        $serviceNames  = $_POST['service_name']  ?? [];
        $servicePrices = $_POST['service_price'] ?? [];
        $services = [];
        foreach ($serviceNames as $i => $sn) {
            if (!empty(trim($sn))) {
                $services[] = ['name' => trim($sn), 'price' => floatval($servicePrices[$i] ?? 0)];
            }
        }
        $data['extra_services'] = json_encode($services);
        unset($data['service_name'], $data['service_price']);
        // total_pax also computed server-side
        $data['total_pax'] = intval($data['total_pax'] ?? (
            (intval($data['double_room_count'] ?? 0) * 2)
            + intval($data['single_room_count']  ?? 0)
            + intval($data['extra_bed_count']    ?? 0)
            + intval($data['child_no_bed_count'] ?? 0)
        ));
        unset($data['total_pax_display'], $data['dbl_display'], $data['cnb_display'], $data['eb_display'], $data['sgl_display']);
        // ─────────────────────────────────────────────────

        // $lastBooking = $db->orderBy("id", "desc")->getOne("property_booking", "booking_id");
        // $lastIdNumber = 0;

        // if ($lastBooking && !empty($lastBooking['booking_id']) && preg_match('/TAJX(\d+)/', $lastBooking['booking_id'], $matches)) {
        //     $lastIdNumber = (int)$matches[1];
        // }
        // $newBookingId = 'TAJX' . str_pad($lastIdNumber + 1, 4, '0', STR_PAD_LEFT);
        // $data['booking_id'] = $newBookingId;

        if ($edit) {
            $db->where('id', $id);
            $update = $db->update('property_booking', $data);

            if ($update) {
                $_SESSION['success'] = "Booking updated successfully!";
                header("Location: property_booking_list.php");
                exit();
            } else {
                $_SESSION['failure'] = "Update failed: " . $db->getLastError();
            }
        } else {
            $lastBooking = $db->orderBy("id", "desc")->getOne("property_booking", "booking_id");
            $lastIdNumber = 0;
            if ($lastBooking && preg_match('/SSP(\d+)/', $lastBooking['booking_id'], $matches)) {
                $lastIdNumber = (int)$matches[1];
            }
            $newBookingId = 'SSP' . str_pad($lastIdNumber + 1, 6, '0', STR_PAD_LEFT);
            $data['booking_id'] = $newBookingId;

            $insert = $db->insert('property_booking', $data);

            if ($insert) {
                $_SESSION['success'] = "Booking added successfully!";
                header("Location: property_booking_list.php");
                exit();
            } else {
                $_SESSION['failure'] = "Insert failed: " . $db->getLastError();
            }
        }
    }
}
?>

<?php include BASE_PATH . '/includes/header.php'; ?>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3"><?= $edit ? 'Edit' : 'Add' ?> Property Booking</h4>

            <form method="post">
                <!-- Property Info -->
                <div class="card mb-4">
                    <div class="card-header">Property Info</div>
                    <div class="card-body row g-3">
                        <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?? '' ?>">

                        <div class="col-md-6">
                            <label class="form-label">Select Property</label>
                            <select name="property_id" class="form-select" id="property_id" required>
                                <option value="">-- Choose Property --</option>
                                <?php foreach ($properties as $prop): ?>
                                    <option value="<?= $prop['id'] ?>" data-room-count="<?= $prop['no_of_rooms'] ?>"
                                        <?= (isset($booking['property_id']) && $booking['property_id'] == $prop['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prop['hotel_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Guest Name</label>
                            <input type="text" name="guest_name" class="form-control" value="<?= $booking['guest_name'] ?? '' ?>" required />
                        </div>

                        <div class="col-md-3">
                            <label>Check-In Date</label>
                            <input type="date" name="check_in_date" class="form-control" value="<?= $booking['check_in_date'] ?? '' ?>" required />
                        </div>
                        <div class="col-md-3">
                            <label>Check-Out Date</label>
                            <input type="date" name="check_out_date" class="form-control" value="<?= $booking['check_out_date'] ?? '' ?>" required />
                        </div>
                        <div class="col-md-3">
                            <label>No. of Nights</label>
                            <input type="number" name="no_of_nights" class="form-control" value="<?= $booking['no_of_nights'] ?? '' ?>" required />
                        </div>
                        <div class="col-md-3">
                            <label>Meal Plan</label>
                            <select name="meal_plan" class="form-select" required>
                                <option value="">Select</option>
                                <?php foreach (['Breakfast', 'Breakfast & Dinner', 'Breakfast & Lunch', 'Breakfast Lunch & Dinner'] as $plan): ?>
                                    <option value="<?= $plan ?>" <?= (isset($booking['meal_plan']) && $booking['meal_plan'] === $plan) ? 'selected' : '' ?>>
                                        <?= $plan ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>

                    </div>
                </div>

                <!-- Warning message -->
                <div class="col-12 mb-3">
                    <div id="room-warning" class="alert alert-danger py-2 px-3 mb-2" style="display:none;"></div>
                    <div id="room-availability-info" class="alert alert-info py-2 px-3 mb-0" style="display:none;"></div>
                </div>

                <!-- Rooming Section -->
                <div class="card mb-4">
                    <div class="card-header">Rooming &amp; Payment</div>
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-primary fw-bold">Extra Bed Percentage (%)</label>
                            <input type="number" id="extra_bed_percent" class="form-control bg-white border-primary" value="40" step="1" min="0" max="100" />
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Auto-calculates Extra Bed Price from Double Room Price</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-primary fw-bold">Child No Bed Percentage (%)</label>
                            <input type="number" id="child_no_bed_percent" class="form-control bg-white border-primary" value="25" step="1" min="0" max="100" />
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Auto-calculates Child No Bed Price from Double Room Price</small>
                        </div>
                        <div class="col-12"><hr class="my-1 text-muted"></div>
                        <?php
                        $fields = [
                            ['extra_bed_count', 'Extra Bed Count'],
                            ['extra_bed_price', 'Extra Bed Price'],
                            ['extra_bed_total', 'Extra Bed Total', true],

                            ['double_room_count', 'Double Room Count'],
                            ['double_room_price', 'Double Room Price'],
                            ['double_room_total', 'Double Room Total', true],

                            ['child_no_bed_count', 'Child No Bed Count'],
                            ['child_no_bed_price', 'Child No Bed Price'],
                            ['child_no_bed_total', 'Child No Bed Total', true],

                            ['single_room_count', 'Single Room Count'],
                            ['single_price', 'Single Room Price'],
                            ['single_total', 'Single Room Total', true],

                            ['booking_token', 'Booking Token'],
                            ['total_amount', 'Total Amount', true],
                            ['final_total', 'Final Total', true],
                            ['total_pax', 'Total Guests', true],
                            ['due_amount', 'Due Amount'],
                        ];

                        foreach ($fields as $field) {
                            $readonly = isset($field[2]) ? 'readonly' : '';
                            $step = ($field[0] === 'total_pax') ? '1' : '0.01';
                            $val = ($field[0] === 'total_pax') ? (int)($booking[$field[0]] ?? 0) : ($booking[$field[0]] ?? '');
                            echo '<div class="col-md-4">
                                    <label>' . $field[1] . '</label>
                                    <input type="number" name="' . $field[0] . '" class="form-control room-input" min="0" step="' . $step . '"
                                           value="' . $val . '" ' . $readonly . ' />
                                  </div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- ── NEW: No. of Rooms Summary & Total Guests ── -->
                <div class="card mb-4">
                    <div class="card-header">No. of Rooms &amp; Total Guests</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <label>DBL Count <small class="text-muted">(Double)</small></label>
                            <input type="number" name="dbl_display" id="dbl_display" class="form-control bg-light" readonly
                                   value="<?= $booking['double_room_count'] ?? 0 ?>" />
                        </div>
                        <div class="col-md-3">
                            <label>CNB Count <small class="text-muted">(Child No Bed)</small></label>
                            <input type="number" name="cnb_display" id="cnb_display" class="form-control bg-light" readonly
                                   value="<?= $booking['child_no_bed_count'] ?? 0 ?>" />
                        </div>
                        <div class="col-md-3">
                            <label>EB Count <small class="text-muted">(Extra Bed)</small></label>
                            <input type="number" name="eb_display" id="eb_display" class="form-control bg-light" readonly
                                   value="<?= $booking['extra_bed_count'] ?? 0 ?>" />
                        </div>
                        <div class="col-md-3">
                            <label>SGL Count <small class="text-muted">(Single)</small></label>
                            <input type="number" name="sgl_display" id="sgl_display" class="form-control bg-light" readonly
                                   value="<?= $booking['single_room_count'] ?? 0 ?>" />
                        </div>
                        <div class="col-md-3">
                            <label>Total Rooms <span class="badge bg-secondary">Auto</span></label>
                            <input type="number" name="no_of_rooms" id="no_of_rooms" class="form-control bg-light" readonly
                                   value="<?= $booking['no_of_rooms'] ?? 0 ?>" />
                            <small class="text-muted d-block mt-1">Rooms are calculated from room counts above.</small>
                        </div>
                        <div class="col-md-3">
                            <label>Total Guests <span class="badge bg-info">Auto</span></label>
                            <input type="number" name="total_pax_display" id="total_pax" class="form-control bg-light" readonly
                                   step="1" value="<?= (int)($booking['total_pax'] ?? 0) ?>" />
                            <small class="text-muted">DBL×2 + SGL×1 + EB×1 + CNB</small>
                        </div>
                    </div>
                </div>

                <!-- ── NEW: Extra Services ── -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Extra Services</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addServiceRow">+ Add Service</button>
                    </div>
                    <div class="card-body">
                        <div id="services-wrapper">
                            <?php foreach ($existingServices as $si => $svc): ?>
                            <div class="row g-2 mb-2 service-row align-items-center">
                                <div class="col-md-6">
                                    <input type="text" name="service_name[]" class="form-control"
                                           placeholder="Service Name (e.g. Room Cleaning)"
                                           value="<?= htmlspecialchars($svc['name']) ?>" />
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">₹</span>
                                        <input type="number" name="service_price[]" class="form-control svc-price"
                                               placeholder="Price" min="0" step="0.01"
                                               value="<?= $svc['price'] ?>" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <?php if ($si > 0): ?>
                                    <button type="button" class="btn btn-sm btn-danger remove-service">Remove</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label>Extra Services Total <span class="badge bg-secondary">Auto</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" name="extra_services_total" id="extra_services_total"
                                           class="form-control bg-light" readonly
                                           value="<?= $booking['extra_services_total'] ?? 0 ?>" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>Discount (%)</label>
                                <div class="input-group">
                                    <input type="number" name="discount_percent" id="discount_percent"
                                           class="form-control" min="0" max="100" step="0.01"
                                           value="<?= $booking['discount_percent'] ?? 0 ?>" />
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>Discount Amount <span class="badge bg-secondary">Auto</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" name="discount_amount" id="discount_amount"
                                           class="form-control bg-light" readonly
                                           value="<?= $booking['discount_amount'] ?? 0 ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label>Final Total <span class="badge bg-success">Auto</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" id="final_total_display"
                                           class="form-control bg-light fw-bold" readonly
                                           value="<?= $booking['final_total'] ?? 0 ?>" />
                                </div>
                                <small class="text-muted">Total Amount + Extra Services Total − Discount</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ── END NEW ── -->

                <!-- Contact Section -->
                <div class="card mb-4">
                    <div class="card-header">Contact</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <label>Agent Type</label>
                            <select name="agent_type" class="form-select">
                                <option value="">Select</option>
                                <?php foreach (['Direct', 'OTA', 'Agent'] as $type): ?>
                                    <option <?= (isset($booking['agent_type']) && $booking['agent_type'] === $type) ? 'selected' : '' ?>><?= $type ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Agent Name</label>
                            <input type="text" name="agent_name" class="form-control" value="<?= $booking['agent_name'] ?? '' ?>" />
                        </div>
                        <div class="col-md-3">
                            <label>Agent Email</label>
                            <input type="email" name="agent_email" class="form-control" value="<?= $booking['agent_email'] ?? '' ?>" />
                        </div>
                        <div class="col-md-3">
                            <label>Guest Email</label>
                            <input type="email" name="guest_email" class="form-control" value="<?= $booking['guest_email'] ?? '' ?>" />
                        </div>
                        <div class="col-md-3">
                            <label>Guest WhatsApp</label>
                            <input type="text" name="guest_whatsapp" class="form-control" value="<?= $booking['guest_whatsapp'] ?? '' ?>" />
                        </div>
                        <div class="col-md-12 mt-3">
                            <label>Special Remarks</label>
                            <textarea name="special_remarks" class="form-control" rows="3" placeholder="Enter any special requests or notes..."><?= $booking['special_remarks'] ?? '' ?></textarea>
                        </div>
                    </div>
                </div>


                <!-- Status -->
                <div class="card mb-4">
                    <div class="card-header">Booking Status</div>
                    <div class="card-body">
                        <select name="status" class="form-select w-25">
                            <?php foreach (['Enquiry', 'Hold', 'Confirmed', 'Cancel'] as $status): ?>
                                <option <?= (isset($booking['status']) && $booking['status'] === $status) ? 'selected' : '' ?>><?= $status ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><?= $edit ? 'Update' : 'Save' ?> Booking</button>
            </form>
        </div>
    </div>
</div>

<!-- JS -->
<script>
    const currentBookingId = <?= json_encode($id ?: '') ?>;

    // Function to disable past dates in date inputs
    function disablePastDates() {
        const today = new Date().toISOString().split('T')[0];
        document.querySelector('input[name="check_in_date"]').min = today;
        document.querySelector('input[name="check_out_date"]').min = today;
    }

    // Function to calculate nights between dates
    function calculateNights() {
        const checkIn = new Date(document.querySelector('input[name="check_in_date"]').value);
        const checkOut = new Date(document.querySelector('input[name="check_out_date"]').value);

        if (checkIn && checkOut && checkOut > checkIn) {
            const diffTime = checkOut - checkIn;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            document.querySelector('input[name="no_of_nights"]').value = diffDays;
        } else {
            document.querySelector('input[name="no_of_nights"]').value = '';
        }
    }

    async function checkRoomAvailability() {
        const propertySelect = document.getElementById('property_id');
        const checkIn = document.querySelector('input[name="check_in_date"]')?.value;
        const checkOut = document.querySelector('input[name="check_out_date"]')?.value;
        const requested = parseInt(document.querySelector('[name="no_of_rooms"]')?.value || 0, 10) || 0;
        const warning = document.getElementById('room-warning');
        const info = document.getElementById('room-availability-info');
        const submitBtn = document.querySelector('button[type="submit"]');

        if (!propertySelect?.value || !checkIn || !checkOut) {
            warning.style.display = 'none';
            info.style.display = 'block';
            info.classList.remove('alert-danger');
            info.classList.add('alert-info');
            info.textContent = 'Select property, check-in date, and check-out date to see availability.';
            submitBtn.disabled = false;
            return;
        }

        const formData = new FormData();
        formData.append('property_id', propertySelect.value);
        formData.append('check_in_date', checkIn);
        formData.append('check_out_date', checkOut);
        if (currentBookingId) formData.append('booking_id', currentBookingId);

        try {
            const response = await fetch('ajax/property_room_availability.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });
            const data = await response.json();
            const available = parseInt(data.available_rooms || 0, 10);
            const booked = parseInt(data.booked_rooms || 0, 10);
            const total = parseInt(data.total_rooms || 0, 10);

            info.style.display = 'block';
            info.classList.remove('alert-danger');
            info.classList.add('alert-info');
            info.textContent = `Total rooms: ${total} | Booked: ${booked} | Available: ${available} | Requested: ${requested}`;

            if (requested > available) {
                warning.style.display = 'block';
                warning.textContent = `Only ${available} room(s) available for the selected dates.`;
                submitBtn.disabled = true;
            } else {
                warning.style.display = 'none';
                warning.textContent = '';
                submitBtn.disabled = false;
            }
        } catch (error) {
            warning.style.display = 'block';
            warning.textContent = 'Unable to check room availability right now.';
            submitBtn.disabled = false;
        }
    }

    function calculateTotals() {
        const get = name => parseFloat(document.querySelector(`[name="${name}"]`)?.value) || 0;
        const set = (name, value) => {
            const el = document.querySelector(`[name="${name}"]`);
             if (el && document.activeElement !== el) el.value = value.toFixed(2);
        }
        const totals = {
            double: get('double_room_count') * get('double_room_price'),
            child: get('child_no_bed_count') * get('child_no_bed_price'),
            single: get('single_room_count') * get('single_price'),
            extra: get('extra_bed_count') * get('extra_bed_price'),
        };
        set('double_room_total', totals.double);
        set('child_no_bed_total', totals.child);
        set('single_total', totals.single);
        set('extra_bed_total', totals.extra);
        const totalAmount = Object.values(totals).reduce((a, b) => a + b, 0);
        const token = get('booking_token');
        set('total_amount', totalAmount);
        set('due_amount', Math.max(0, totalAmount - token));
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Initialize date restrictions
        disablePastDates();

        // Set up event listeners
        document.querySelector('input[name="check_in_date"]').addEventListener('change', function() {
            // When check-in date changes, update check-out min date
            const checkInDate = this.value;
            document.querySelector('input[name="check_out_date"]').min = checkInDate;

            // If check-out is before new check-in, clear it
            if (document.querySelector('input[name="check_out_date"]').value < checkInDate) {
                document.querySelector('input[name="check_out_date"]').value = '';
            }

            calculateNights();
            checkRoomAvailability();
        });

        document.querySelector('input[name="check_out_date"]').addEventListener('change', function() {
            calculateNights();
            checkRoomAvailability();
        });

        // Existing listeners
        document.querySelectorAll('input, select').forEach(input => {
            if (!['check_in_date', 'check_out_date'].includes(input.name)) {
                input.addEventListener('input', () => {
                    calculateTotals();
                    checkRoomAvailability();
                });
                input.addEventListener('change', () => {
                    calculateTotals();
                    checkRoomAvailability();
                });
            }
        });

        // Initial calculations
        calculateTotals();
        checkRoomAvailability();

        // ── NEW: Auto-calc Extra Bed and Child No Bed Prices ──
        const dblPriceInput = document.querySelector('input[name="double_room_price"]');
        const extraBedPriceInput = document.querySelector('input[name="extra_bed_price"]');
        const childPriceInput = document.querySelector('input[name="child_no_bed_price"]');
        const extraPctInput = document.getElementById('extra_bed_percent');
        const childPctInput = document.getElementById('child_no_bed_percent');

        function updateDerivedPrices() {
            const dblPrice = parseFloat(dblPriceInput.value) || 0;
            const extraPct = parseFloat(extraPctInput.value) || 0;
            const childPct = parseFloat(childPctInput.value) || 0;

            if (dblPrice > 0) {
                // Only overwrite if we have a base price
                extraBedPriceInput.value = (dblPrice * (extraPct / 100)).toFixed(2);
                childPriceInput.value = (dblPrice * (childPct / 100)).toFixed(2);
                calculateTotals();
                if (typeof syncRoomSummary === 'function') {
                    syncRoomSummary();
                }
            }
        }

        if (dblPriceInput) dblPriceInput.addEventListener('input', updateDerivedPrices);
        if (extraPctInput) extraPctInput.addEventListener('input', updateDerivedPrices);
        if (childPctInput) childPctInput.addEventListener('input', updateDerivedPrices);

        // ── NEW: mirror room counts into display fields & calc guests ──
        function syncRoomSummary() {
            const get = name => parseInt(document.querySelector(`[name="${name}"]`)?.value) || 0;
            const setRo = (id, val) => { const el = document.getElementById(id); if(el) el.value = val; };
            const dbl = get('double_room_count');
            const cnb = get('child_no_bed_count');
            const eb  = get('extra_bed_count');
            const sgl = get('single_room_count');
            setRo('dbl_display', dbl);
            setRo('cnb_display', cnb);
            setRo('eb_display',  eb);
            setRo('sgl_display', sgl);
            setRo('no_of_rooms', dbl + sgl);
            // Total Guests as integer
            const paxEl = document.getElementById('total_pax');
            if (paxEl) paxEl.value = Math.round((dbl * 2) + sgl + eb + cnb);
            // also sync the hidden field in Rooming section
            const paxHidden = document.querySelector('[name="total_pax"]');
            if (paxHidden && paxHidden !== paxEl) paxHidden.value = Math.round((dbl * 2) + sgl + eb + cnb);
            // extra services total
            let svcTotal = 0;
            document.querySelectorAll('.svc-price').forEach(el => svcTotal += parseFloat(el.value) || 0);
            const svcEl = document.getElementById('extra_services_total');
            if (svcEl) svcEl.value = svcTotal.toFixed(2);
            // discount
            const discPct = parseFloat(document.querySelector('[name="discount_percent"]')?.value) || 0;
            const totalAmt = parseFloat(document.querySelector('[name="total_amount"]')?.value) || 0;
            const discAmt = totalAmt * (discPct / 100);
            const discEl = document.getElementById('discount_amount');
            if (discEl) discEl.value = discAmt.toFixed(2);
            // final total calculation
            const finalVal = Math.max(0, totalAmt + svcTotal - discAmt);
            
            // Update Final Total Input in Rooming Section
            const finalInput = document.querySelector('input[name="final_total"]');
            if (finalInput) finalInput.value = finalVal.toFixed(2);
            
            // Update Final Total Display in Services Section
            const finalDisplay = document.getElementById('final_total_display');
            if (finalDisplay) finalDisplay.value = finalVal.toFixed(2);

            // due amount
            const dueEl = document.querySelector('[name="due_amount"]');
            const tokenEl = parseFloat(document.querySelector('[name="booking_token"]')?.value) || 0;
            
            // If user is NOT editing Due Amount, update it based on Token
            if (dueEl && document.activeElement !== dueEl) {
                dueEl.value = Math.max(0, finalVal - tokenEl).toFixed(2);
            }
        }
        // Hook room inputs to also sync summary
        document.querySelectorAll('input.room-input').forEach(el => el.addEventListener('input', syncRoomSummary));
        document.querySelector('[name="discount_percent"]')?.addEventListener('input', syncRoomSummary);
        document.getElementById('property_id')?.addEventListener('change', checkRoomAvailability);
        
        // ── NEW: Reverse calculation Due Amount -> Token ──
        const dueInput = document.querySelector('[name="due_amount"]');
        if (dueInput) {
            dueInput.addEventListener('input', function() {
                const finalAmt = parseFloat(document.querySelector('[name="final_total"]')?.value) || 0;
                const dueAmt = parseFloat(this.value) || 0;
                // Token = Final - Due
                const tokenAmt = Math.max(0, finalAmt - dueAmt);
                const tokenInput = document.querySelector('[name="booking_token"]');
                if (tokenInput) tokenInput.value = tokenAmt.toFixed(2);
            });
        }
        
        document.querySelector('form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            checkRoomAvailability().then(() => {
                if (!document.querySelector('button[type="submit"]').disabled) {
                    form.submit();
                }
            });
        });

        syncRoomSummary();

        // ── NEW: add/remove service rows ──
        document.getElementById('addServiceRow').addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'row g-2 mb-2 service-row align-items-center';
            row.innerHTML = `
                <div class="col-md-6">
                    <input type="text" name="service_name[]" class="form-control" placeholder="Service Name" />
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" name="service_price[]" class="form-control svc-price" placeholder="Price" min="0" step="0.01" />
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-danger remove-service">Remove</button>
                </div>`;
            document.getElementById('services-wrapper').appendChild(row);
            row.querySelectorAll('input').forEach(el => el.addEventListener('input', syncRoomSummary));
        });
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-service')) {
                e.target.closest('.service-row').remove();
                syncRoomSummary();
            }
        });
        // ── END NEW ──
    });
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>