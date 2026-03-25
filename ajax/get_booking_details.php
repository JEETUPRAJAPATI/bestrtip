<?php
require_once '../config/config.php';
header('Content-Type: application/json');

$bookingCode = $_GET['booking_code'] ?? '';

if (!$bookingCode) {
    echo json_encode(['status' => 'error', 'message' => 'Booking code is required']);
    exit;
}

$db = getDbInstance();
$booking = $db->where('booking_code', $bookingCode)->getOne('agent_queries');

if ($booking) {
    echo json_encode(['status' => 'success', 'details' => $booking]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
}
