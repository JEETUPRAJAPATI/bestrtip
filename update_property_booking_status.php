<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
require_once __DIR__ . '/helpers/property_booking_invoice_mailer.php';

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
$currentBooking = $db->getOne('property_booking', ['id', 'status']);

$previousStatus = $currentBooking['status'] ?? '';
$db->where('id', $bookingId);
$updated = $db->update('property_booking', ['status' => $status]);

if ($updated) {
    $message = 'Status updated successfully';

    if ($status === 'Confirmed' && $previousStatus !== 'Confirmed') {
        $emailResult = sendPropertyBookingInvoiceEmail($bookingId);
        if (!empty($emailResult['success'])) {
            $message .= ' and invoice email sent automatically';
        } else {
            $message .= '. Invoice email failed: ' . ($emailResult['message'] ?? 'Unknown error');
        }
    }

    echo json_encode(['success' => true, 'message' => $message]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}
