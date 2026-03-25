<?php
session_start();
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

// Sanitize input
$otp = filter_input(INPUT_POST, 'otp');
$mobileNumber = filter_input(INPUT_POST, 'mobileNumber');

if (!$otp || !$mobileNumber) {
    echo json_encode(['error' => 'OTP and mobile number are required.']);
    exit;
}

// Check if OTP is stored in session
// if (!isset($_SESSION['otp']) || $_SESSION['otp'] !== $otp || $_SESSION['otp_mobile'] !== $mobileNumber) {
//     echo json_encode(['error' => 'Invalid OTP.']);
//     exit;
// }

// Get user from database
$db = getDbInstance();
$db->where('otp', $otp);
$db->where("mobile", $mobileNumber);
// $db->where("status", 'Active');
$user = $db->getOne('vehicles');

if (!$user) {
    echo json_encode(['error' => 'User not found or inactive.']);
    exit;
}

// OTP verification successful, set session
$_SESSION['user_logged_in'] = TRUE;
$_SESSION['admin_type'] = $user['type'];
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['driver_name'];

// Clear OTP session
// unset($_SESSION['otp']);
// unset($_SESSION['otp_mobile']);

// Redirect based on user type
echo json_encode(['success' => 'OTP verified successfully.']);
exit;
