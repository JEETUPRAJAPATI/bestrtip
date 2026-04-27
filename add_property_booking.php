<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
require_once __DIR__ . '/helpers/property_booking_invoice_mailer.php';
require_once __DIR__ . '/helpers/property_status_mailer.php';
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
    // Hotel-style overlap: checkout date is non-occupied (exclusive boundary)
    $db->where('check_in_date', $checkOutDate, '<');
    $db->where('check_out_date', $checkInDate, '>');

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

$existingStatus = $booking['status'] ?? '';

$tokenPercentColumnExists = false;
$receiptAmountColumnExists = false;
$holdStartedAtColumnExists = false;
$mysqli = $db->mysqli();
$schemaCheck = $mysqli->query("SHOW COLUMNS FROM `property_booking` LIKE 'booking_token_percent'");
if ($schemaCheck instanceof mysqli_result && $schemaCheck->num_rows > 0) {
    $tokenPercentColumnExists = true;
    $schemaCheck->free();
} else {
    // Best-effort schema update for local deployments.
    $mysqli->query("ALTER TABLE `property_booking` ADD COLUMN `booking_token_percent` DECIMAL(6,2) NOT NULL DEFAULT 0.00 AFTER `booking_token`");
    $schemaCheckAfter = $mysqli->query("SHOW COLUMNS FROM `property_booking` LIKE 'booking_token_percent'");
    if ($schemaCheckAfter instanceof mysqli_result) {
        $tokenPercentColumnExists = $schemaCheckAfter->num_rows > 0;
        $schemaCheckAfter->free();
    }
}

$receiptSchemaCheck = $mysqli->query("SHOW COLUMNS FROM `property_booking` LIKE 'receipt_amount'");
if ($receiptSchemaCheck instanceof mysqli_result && $receiptSchemaCheck->num_rows > 0) {
    $receiptAmountColumnExists = true;
    $receiptSchemaCheck->free();
} else {
    // Best-effort schema update for local deployments.
    $mysqli->query("ALTER TABLE `property_booking` ADD COLUMN `receipt_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `booking_token`");
    $receiptSchemaCheckAfter = $mysqli->query("SHOW COLUMNS FROM `property_booking` LIKE 'receipt_amount'");
    if ($receiptSchemaCheckAfter instanceof mysqli_result) {
        $receiptAmountColumnExists = $receiptSchemaCheckAfter->num_rows > 0;
        $receiptSchemaCheckAfter->free();
    }
}

$holdSchemaCheck = $mysqli->query("SHOW COLUMNS FROM `property_booking` LIKE 'hold_started_at'");
if ($holdSchemaCheck instanceof mysqli_result && $holdSchemaCheck->num_rows > 0) {
    $holdStartedAtColumnExists = true;
    $holdSchemaCheck->free();
} else {
    // Best-effort schema update for local deployments.
    $mysqli->query("ALTER TABLE `property_booking` ADD COLUMN `hold_started_at` DATETIME NULL DEFAULT NULL AFTER `status`");
    $holdSchemaCheckAfter = $mysqli->query("SHOW COLUMNS FROM `property_booking` LIKE 'hold_started_at'");
    if ($holdSchemaCheckAfter instanceof mysqli_result) {
        $holdStartedAtColumnExists = $holdSchemaCheckAfter->num_rows > 0;
        $holdSchemaCheckAfter->free();
    }
}

// Backfill: older Hold bookings may have NULL hold_started_at
if ($holdStartedAtColumnExists) {
    $mysqli->query("UPDATE `property_booking` SET `hold_started_at` = NOW() WHERE `status` = 'Hold' AND `hold_started_at` IS NULL");
}

if (!isset($booking['booking_token_percent'])) {
    $baseForPercent = (float)($booking['final_total'] ?? $booking['total_amount'] ?? 0);
    $tokenForPercent = (float)($booking['booking_token'] ?? 0);
    $booking['booking_token_percent'] = $baseForPercent > 0
        ? round(($tokenForPercent / $baseForPercent) * 100, 2)
        : 0;
}

if (!isset($booking['receipt_amount'])) {
    $booking['receipt_amount'] = 0;
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
    $receiptAmount = max(0, (float)($data['receipt_amount'] ?? 0));
    $data['receipt_amount'] = $receiptAmount;
    $data['final_total'] = max(0, $roomTotal + $serviceTotal - $discountAmount);
    $tokenPercent = (float)($data['booking_token_percent'] ?? 0);
    $tokenPercent = max(0, min(100, $tokenPercent));
    if ($data['final_total'] > 0) {
        $data['booking_token'] = round(($data['final_total'] * $tokenPercent) / 100, 2);
    } else {
        $data['booking_token'] = 0;
    }
    $data['booking_token_percent'] = $tokenPercent;
    $data['due_amount'] = max(0, $data['final_total'] - $receiptAmount);

    // Hold timing: set start when moving into Hold; clear when leaving Hold.
    if ($holdStartedAtColumnExists) {
        $newStatus = $data['status'] ?? '';
        $postedHoldStartedAt = trim((string)($data['hold_started_at'] ?? ''));

        $normalizeHoldStartedAt = function (string $value): string {
            $value = trim($value);
            if ($value === '') {
                return '';
            }
            // Accept HTML datetime-local (YYYY-MM-DDTHH:MM) or normal datetime strings.
            $ts = strtotime(str_replace('T', ' ', $value));
            if ($ts === false) {
                return '';
            }
            return date('Y-m-d H:i:s', $ts);
        };

        if ($newStatus === 'Hold') {
            $normalized = $normalizeHoldStartedAt($postedHoldStartedAt);
            if ($normalized !== '') {
                $data['hold_started_at'] = $normalized;
            } else {
                // If user didn't provide a value, always set "now" to avoid NULL holds.
                $data['hold_started_at'] = date('Y-m-d H:i:s');
            }
        } else {
            $data['hold_started_at'] = null;
        }
    } else {
        unset($data['hold_started_at']);
    }

    if (!$tokenPercentColumnExists) {
        unset($data['booking_token_percent']);
    }

    if (!$receiptAmountColumnExists) {
        unset($data['receipt_amount']);
    }

    $propertyId = (int)($data['property_id'] ?? 0);
    $checkInDate = $data['check_in_date'] ?? '';
    $checkOutDate = $data['check_out_date'] ?? '';

    // Enforce nights from selected dates so 08 -> 09 is always 1 night.
    if (!empty($checkInDate) && !empty($checkOutDate)) {
        $inTs = strtotime($checkInDate);
        $outTs = strtotime($checkOutDate);
        if ($inTs !== false && $outTs !== false && $outTs > $inTs) {
            $data['no_of_nights'] = (int)(($outTs - $inTs) / 86400);
        }
    }

    $availability = getPropertyRoomAvailability($propertyId, $checkInDate, $checkOutDate, $edit ? $id : null);

    if (
        ($propertyId && $checkInDate && $checkOutDate && $computedRooms > $availability['available_rooms'])
        || (($data['status'] ?? '') === 'Confirmed' && $receiptAmount <= 0)
    ) {
        if (($data['status'] ?? '') === 'Confirmed' && $receiptAmount <= 0) {
            $_SESSION['failure'] = 'Receipt Amount is required before confirming booking.';
        } else {
            $_SESSION['failure'] = 'Only ' . $availability['available_rooms'] . ' room(s) available for the selected property and dates.';
        }
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
                $message = "Booking updated successfully!";
                $statusEmailResult = sendPropertyStatusEmail((int)$id);
                $message .= !empty($statusEmailResult['success']) 
                    ? " Status email sent." 
                    : " Status email failed: " . ($statusEmailResult['message'] ?? 'Unknown error');

                if (($data['status'] ?? '') === 'Confirmed') {
                    $emailResult = sendPropertyBookingInvoiceEmail((int)$id);
                    $message .= !empty($emailResult['success'])
                        ? " Invoice email sent."
                        : " Invoice email failed: " . ($emailResult['message'] ?? 'Unknown error');
                }
                $_SESSION['success'] = $message;
                // header("Location: property_booking_list.php");
                // exit();
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
                $message = "Booking added successfully!";
                $statusEmailResult = sendPropertyStatusEmail((int)$insert);
                $message .= !empty($statusEmailResult['success']) 
                    ? " Status email sent." 
                    : " Status email failed: " . ($statusEmailResult['message'] ?? 'Unknown error');

                if (($data['status'] ?? '') === 'Confirmed') {
                    $emailResult = sendPropertyBookingInvoiceEmail((int)$insert);
                    $message .= !empty($emailResult['success'])
                        ? " Invoice email sent."
                        : " Invoice email failed: " . ($emailResult['message'] ?? 'Unknown error');
                }
                $_SESSION['success'] = $message;
                // header("Location: property_booking_list.php");
                // exit();
            } else {
                $_SESSION['failure'] = "Insert failed: " . $db->getLastError();
            }
        }
    }
}

// Recompute hold duration for current render state (after any POST merge)
$holdDurationDisplay = '';
if (($booking['status'] ?? '') === 'Hold' && !empty($booking['hold_started_at'])) {
    $holdStartTs = strtotime($booking['hold_started_at']);
    if ($holdStartTs !== false) {
        $nowTs = time();
        $isFuture = $holdStartTs > $nowTs;
        $diff = abs($nowTs - $holdStartTs);
        $days = (int)floor($diff / 86400);
        $hours = (int)floor(($diff % 86400) / 3600);
        $mins = (int)floor(($diff % 3600) / 60);
        $secs = (int)($diff % 60);
        if ($days > 0) $holdDurationDisplay = $days . 'd ' . $hours . 'h ' . $mins . 'm ' . $secs . 's';
        elseif ($hours > 0) $holdDurationDisplay = $hours . 'h ' . $mins . 'm ' . $secs . 's';
        elseif ($mins > 0) $holdDurationDisplay = $mins . 'm ' . $secs . 's';
        else $holdDurationDisplay = $secs . 's';

        if ($isFuture) {
            $holdDurationDisplay = 'Starts in ' . $holdDurationDisplay;
        }
    }
}
?>

<?php include BASE_PATH . '/includes/header.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3"><?= $edit ? 'Edit' : 'Add' ?> Property Booking</h4>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['failure'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['failure']; unset($_SESSION['failure']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

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
                            <input type="text" name="check_in_date" class="form-control flatpickr-booking-date" value="<?= $booking['check_in_date'] ?? '' ?>" required />
                        </div>
                        <div class="col-md-3">
                            <label>Check-Out Date</label>
                            <input type="text" name="check_out_date" class="form-control flatpickr-booking-date" value="<?= $booking['check_out_date'] ?? '' ?>" required />
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
                    <div id="room-booking-history" class="card border-0 mt-2" style="display:none;">
                        <div class="card-body py-2 px-3 bg-light rounded">
                            <div class="small fw-semibold mb-2">Booked Room History</div>
                            <div id="room-booking-history-list" style="display:none;"></div>
                        </div>
                    </div>
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

                            ['booking_token_percent', 'Booking Token (%)'],
                            ['booking_token', 'Booking Token'],
                            ['receipt_amount', 'Receipt Amount'],
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
                        <select name="status" class="form-select w-25" id="booking_status">
                            <?php foreach (['Enquiry', 'Hold', 'Confirmed', 'Cancel'] as $status): ?>
                                <option <?= (isset($booking['status']) && $booking['status'] === $status) ? 'selected' : '' ?>><?= $status ?></option>
                            <?php endforeach; ?>
                        </select>

                        <?php
                        $holdStartedAtLocal = '';
                        if (!empty($booking['hold_started_at'])) {
                            $ts = strtotime($booking['hold_started_at']);
                            if ($ts !== false) {
                                $holdStartedAtLocal = date('Y-m-d\\TH:i', $ts);
                            }
                        }
                        ?>

                        <div class="row g-3 mt-3" id="hold-timing-fields" style="<?= (($booking['status'] ?? '') === 'Hold') ? '' : 'display: none;' ?>">
                            <div class="col-md-3">
                                <label class="form-label">Hold Since</label>
                                <input type="datetime-local" name="hold_started_at" class="form-control" value="<?= htmlspecialchars($holdStartedAtLocal) ?>" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Hold Duration</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($holdDurationDisplay) ?>" readonly />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4"><?= $edit ? 'Update' : 'Save' ?> Booking</button>
                    <?php if ($edit): ?>
                        <a href="send_booking_email.php?crm=<?= encryptId($id) ?>" class="btn btn-info px-4">
                            <i class="fas fa-envelope me-1"></i> Send Email
                        </a>
                        <a href="view_property_invoice.php?crm=<?= encryptId($id) ?>" target="_blank" class="btn btn-primary px-4">
                            <i class="fas fa-file-invoice me-1"></i> Send Invoice
                        </a>
                    <?php endif; ?>
                    <a href="property_booking_list.php" class="btn btn-outline-secondary px-4">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS -->
<script>
    const currentBookingId = <?= json_encode($id ?: '') ?>;
    let bookingHistoryOpen = false;

    let holdDurationTimer = null;

    function updateHoldDuration() {
        const holdSinceEl = document.querySelector('#hold-timing-fields input[name="hold_started_at"]');
        const durationEl = document.getElementById('hold_duration_display');
        if (!holdSinceEl || !durationEl || !holdSinceEl.value) {
            if (durationEl) durationEl.value = '';
            return;
        }
        const start = new Date(holdSinceEl.value);
        if (Number.isNaN(start.getTime())) {
            durationEl.value = '';
            return;
        }
        const nowMs = Date.now();
        const isFuture = start.getTime() > nowMs;
        const diffMs = Math.abs(nowMs - start.getTime());
        const totalSeconds = Math.floor(diffMs / 1000);
        const days = Math.floor(totalSeconds / 86400);
        const hours = Math.floor((totalSeconds % 86400) / 3600);
        const mins = Math.floor((totalSeconds % 3600) / 60);
        const secs = totalSeconds % 60;

        let txt;
        if (days > 0) txt = `${days}d ${hours}h ${mins}m ${secs}s`;
        else if (hours > 0) txt = `${hours}h ${mins}m ${secs}s`;
        else if (mins > 0) txt = `${mins}m ${secs}s`;
        else txt = `${secs}s`;

        durationEl.value = isFuture ? `Starts in ${txt}` : txt;
    }

    function toggleHoldTimingFields() {
        const statusEl = document.getElementById('booking_status');
        const wrap = document.getElementById('hold-timing-fields');
        const holdSinceEl = document.querySelector('#hold-timing-fields input[name="hold_started_at"]');
        if (!statusEl || !wrap) return;
        const isHold = statusEl.value === 'Hold';
        wrap.style.display = isHold ? '' : 'none';

        if (holdDurationTimer) {
            clearInterval(holdDurationTimer);
            holdDurationTimer = null;
        }

        if (isHold && holdSinceEl && !holdSinceEl.value) {
            const now = new Date();
            const pad = (n) => String(n).padStart(2, '0');
            const y = now.getFullYear();
            const m = pad(now.getMonth() + 1);
            const d = pad(now.getDate());
            const hh = pad(now.getHours());
            const mm = pad(now.getMinutes());
            holdSinceEl.value = `${y}-${m}-${d}T${hh}:${mm}`;
        }

        if (isHold) {
            updateHoldDuration();
            holdDurationTimer = setInterval(updateHoldDuration, 1000);
        }
    }


    document.addEventListener('DOMContentLoaded', () => {
        toggleHoldTimingFields();
        document.getElementById('booking_status')?.addEventListener('change', toggleHoldTimingFields);
        document.querySelector('#hold-timing-fields input[name="hold_started_at"]')?.addEventListener('change', updateHoldDuration);
    });

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
        const checkOutInput = document.querySelector('input[name="check_out_date"]');
        const checkOut = checkOutInput?.value || '';
        const nights = parseInt(document.querySelector('input[name="no_of_nights"]')?.value || 0, 10) || 0;
        const requested = parseInt(document.querySelector('[name="no_of_rooms"]')?.value || 0, 10) || 0;
        const warning = document.getElementById('room-warning');
        const info = document.getElementById('room-availability-info');
        const historyWrap = document.getElementById('room-booking-history');
        const historyList = document.getElementById('room-booking-history-list');
        const submitBtn = document.querySelector('button[type="submit"]');

        const deriveCheckoutDate = (checkInDate, noOfNights) => {
            if (!checkInDate || noOfNights <= 0) {
                return '';
            }
            const base = new Date(checkInDate + 'T00:00:00');
            if (Number.isNaN(base.getTime())) {
                return '';
            }
            base.setDate(base.getDate() + noOfNights);
            const y = base.getFullYear();
            const m = String(base.getMonth() + 1).padStart(2, '0');
            const d = String(base.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        };

        const effectiveCheckOut = checkOut || deriveCheckoutDate(checkIn, nights);

        if (!checkOut && effectiveCheckOut && checkOutInput) {
            checkOutInput.value = effectiveCheckOut;
        }

        const escapeHtml = (value) => String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        if (!propertySelect?.value || !checkIn || !effectiveCheckOut) {
            warning.style.display = 'none';
            info.style.display = 'block';
            info.classList.remove('alert-danger');
            info.classList.add('alert-info');
            info.textContent = 'Select property and check-in date, then set nights or check-out date to see availability.';
            historyWrap.style.display = 'none';
            historyList.style.display = 'none';
            historyList.innerHTML = '';
            submitBtn.disabled = false;
            return;
        }

        const formData = new FormData();
        formData.append('property_id', propertySelect.value);
        formData.append('check_in_date', checkIn);
        formData.append('check_out_date', effectiveCheckOut);
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
            const bookingHistory = Array.isArray(data.booking_history) ? data.booking_history : [];

            info.style.display = 'block';
            info.classList.remove('alert-danger');
            info.classList.add('alert-info');
            if (booked > 0 && bookingHistory.length > 0) {
                info.innerHTML = `Total rooms: ${total} | <button type="button" id="bookedHistoryToggle" class="btn btn-link p-0 align-baseline">Booked: ${booked}</button> | Available: ${available} | Requested: ${requested}`;
                historyWrap.style.display = 'block';
                bookingHistoryOpen = true;
                historyList.style.display = 'block';
                historyList.innerHTML = `
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Guest</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Rooms</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${bookingHistory.map(item => `
                                    <tr>
                                        <td>${escapeHtml(item.booking_id || '-')}</td>
                                        <td>${escapeHtml(item.guest_name || '-')}</td>
                                        <td>${escapeHtml(item.check_in_date || '-')}</td>
                                        <td>${escapeHtml(item.check_out_date || '-')}</td>
                                        <td>${escapeHtml(item.no_of_rooms || 0)}</td>
                                        <td>${escapeHtml(item.status || '-')}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>`;

            } else {
                info.textContent = `Total rooms: ${total} | Booked: ${booked} | Available: ${available} | Requested: ${requested}`;
                historyWrap.style.display = 'none';
                historyList.style.display = 'none';
                historyList.innerHTML = '';
                bookingHistoryOpen = false;
            }

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
            historyWrap.style.display = 'none';
            historyList.style.display = 'none';
            historyList.innerHTML = '';
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
        const serviceTotal = get('extra_services_total');
        const discountAmount = get('discount_amount');
        const receiptAmount = get('receipt_amount');
        const token = get('booking_token');
        const finalAmount = Math.max(0, totalAmount + serviceTotal - discountAmount);
        set('total_amount', totalAmount);
        set('final_total', finalAmount);
        set('due_amount', Math.max(0, finalAmount - receiptAmount));
    }

    let checkInPicker, checkOutPicker;

    document.addEventListener('DOMContentLoaded', () => {
        // Flatpickr initialization
        const isEdit = <?= json_encode($edit) ?>;
        const checkInVal = document.querySelector('[name="check_in_date"]').value;
        
        checkInPicker = flatpickr('[name="check_in_date"]', {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d-m-Y",
            allowInput: true,
            minDate: (isEdit && checkInVal) ? null : "today",
            onChange: function(selectedDates, dateStr) {
                if (checkOutPicker) {
                    checkOutPicker.set("minDate", dateStr);
                }
                calculateNights();
                checkRoomAvailability();
            }
        });

        checkOutPicker = flatpickr('[name="check_out_date"]', {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d-m-Y",
            allowInput: true,
            minDate: (isEdit && checkInVal) ? null : "today",
            onChange: function() {
                calculateNights();
                checkRoomAvailability();
            }
        });

        // Set up event listeners
        document.querySelector('input[name="no_of_nights"]').addEventListener('input', function() {
            const checkInDate = document.querySelector('input[name="check_in_date"]')?.value;
            const nights = parseInt(this.value || 0, 10) || 0;
            const checkOutEl = document.querySelector('input[name="check_out_date"]');
            if (checkOutEl && checkInDate && nights > 0) {
                const d = new Date(checkInDate + 'T00:00:00');
                if (!Number.isNaN(d.getTime())) {
                    d.setDate(d.getDate() + nights);
                    const y = d.getFullYear();
                    const m = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');
                    checkOutEl.value = `${y}-${m}-${day}`;
                    if (checkOutPicker) checkOutPicker.setDate(`${y}-${m}-${day}`);
                }
            }
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
            const receiptAmt = parseFloat(document.querySelector('[name="receipt_amount"]')?.value) || 0;
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
            const tokenInputEl = document.querySelector('[name="booking_token"]');
            const tokenPctInputEl = document.querySelector('[name="booking_token_percent"]');
            const tokenEl = parseFloat(tokenInputEl?.value) || 0;
            
            if (dueEl && document.activeElement !== dueEl) {
                dueEl.value = Math.max(0, finalVal - receiptAmt).toFixed(2);
            }

            if (tokenPctInputEl && document.activeElement !== tokenPctInputEl) {
                const pct = finalVal > 0 ? (tokenEl / finalVal) * 100 : 0;
                tokenPctInputEl.value = Math.max(0, Math.min(100, pct)).toFixed(2);
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
                const tokenPctInput = document.querySelector('[name="booking_token_percent"]');
                if (tokenPctInput) {
                    const pct = finalAmt > 0 ? (tokenAmt / finalAmt) * 100 : 0;
                    tokenPctInput.value = Math.max(0, Math.min(100, pct)).toFixed(2);
                }
            });
        }

        const tokenPercentInput = document.querySelector('[name="booking_token_percent"]');
        if (tokenPercentInput) {
            tokenPercentInput.addEventListener('input', function() {
                const finalAmt = parseFloat(document.querySelector('[name="final_total"]')?.value) || 0;
                const pct = Math.max(0, Math.min(100, parseFloat(this.value) || 0));
                const tokenAmt = finalAmt > 0 ? (finalAmt * pct) / 100 : 0;
                const tokenInput = document.querySelector('[name="booking_token"]');
                const dueInput = document.querySelector('[name="due_amount"]');
                if (tokenInput) tokenInput.value = tokenAmt.toFixed(2);
                if (dueInput) dueInput.value = Math.max(0, finalAmt - tokenAmt).toFixed(2);
            });
        }

        document.querySelector('[name="status"]')?.addEventListener('change', function() {
            if (this.value !== 'Confirmed') {
                return;
            }
            const receiptAmount = parseFloat(document.querySelector('[name="receipt_amount"]')?.value) || 0;
            if (receiptAmount <= 0) {
                this.value = 'Enquiry';
                alert('Receipt Amount is required before setting status to Confirmed.');
            }
        });
        
        document.querySelector('form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            checkRoomAvailability().then(() => {
                if (!document.querySelector('button[type="submit"]').disabled) {
                    form.submit();
                }
            });
        });

        document.addEventListener('click', function (e) {
            const toggle = e.target.closest('#bookedHistoryToggle');
            if (!toggle) {
                return;
            }

            e.preventDefault();
            const historyList = document.getElementById('room-booking-history-list');
            if (!historyList) {
                return;
            }

            bookingHistoryOpen = !bookingHistoryOpen;
            historyList.style.display = bookingHistoryOpen ? 'block' : 'none';
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

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<?php include BASE_PATH . '/includes/footer.php'; ?>