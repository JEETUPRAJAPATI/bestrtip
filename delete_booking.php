<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';


$id = decryptId($_GET['crm']);
if (!$id) {
    $_SESSION['failure'] = "Invalid booking ID";
    header("Location: property_booking_list.php");
    exit;
}

$db = getDbInstance();

// First, fetch booking details for logging/notification
$db->where('id', $id);
$booking = $db->getOne('property_booking');

if (!$booking) {
    $_SESSION['failure'] = "Booking not found";
    header("Location: property_booking_list.php");
    exit;
}

// **PERMANENT DELETE** (Hard Delete)
$db->where('id', $id);
$result = $db->delete('property_booking'); // This removes the record completely
if ($result) {
    $_SESSION['success'] = "Booking #" . $booking['booking_id'] . " has been permanently deleted!";
} else {
    $_SESSION['failure'] = "Failed to delete booking #" . $booking['booking_id'];
}

// OR for hard delete:
// $db->where('id', $id);
// $result = $db->delete('property_booking');
header("Location: property_booking_list.php");
exit;
