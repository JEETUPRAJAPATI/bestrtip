<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

header('Content-Type: application/json');

$bookingId = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
$status = trim($_POST['status'] ?? '');
$allowedStatuses = ['Confirmed', 'Hold', 'Enquiry', 'Cancel'];

if (empty($bookingId)) {
    echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
    exit;
}

if (!in_array($status, $allowedStatuses, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

$db = getDbInstance();
$db->where('id', $bookingId);
$updated = $db->update('property_booking', ['status' => $status]);

if ($updated) {
    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}
