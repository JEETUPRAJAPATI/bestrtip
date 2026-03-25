<?php
require_once 'config/config.php';
$db = getDbInstance();

$booking_id = $_POST['booking_id'];
$booking_status = $_POST['booking_status'];
$payment_status = $_POST['payment_status'];

// Get booking_token for checking due_amount
$db->where('booking_id', $booking_id);
$booking = $db->getOne('property_booking');

$due_amount = 0;
if ($payment_status === 'Pending') {
    $due_amount = $booking['total_amount'] - $booking['booking_token'];
}

$data = [
    'status' => $booking_status,
    'due_amount' => $due_amount
];

$db->where('booking_id', $booking_id);
$updated = $db->update('property_booking', $data);

if ($updated) {
    $_SESSION['success'] = 'Status updated successfully';
} else {
    $_SESSION['failure'] = 'Failed to update status';
}

header('Location: reservation_detail.php');
exit;
