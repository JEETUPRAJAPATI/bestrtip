<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Get decrypted ID
$id = isset($_GET['crm']) && !empty($_GET['crm']) ? decryptId($_GET['crm']) : "";

if (!empty($id)) {
    $db = getDbInstance();
    $db->where('id', $id);

    $booking_deleted = $db->delete('property_booking');

    if ($booking_deleted) {
        $_SESSION['success'] = "Property booking deleted successfully!";
    } else {
        $_SESSION['failure'] = "Failed to delete booking: " . $db->getLastError();
    }
    header("Location: booking_list.php");
    exit();
} else {
    $_SESSION['failure'] = "Invalid booking ID.";
    header("Location: booking_list.php");
    exit();
}
