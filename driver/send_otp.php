<?php
session_start();
require_once '../config/config.php';

if (!isset($_POST['mobileNumber']) || empty($_POST['mobileNumber'])) {
    echo json_encode(['error' => 'Mobile number is required.']);
    exit;
}

$db = getDbInstance();
$db->where('mobile', $_POST['mobileNumber']);
$data = $db->getOne("vehicles");

if (!$data) {
    echo json_encode(['error' => 'Mobile number not registered.']);
    exit;
}

// Generate OTP and store in session
$otp = generateOTP();

// Send OTP via API
sendOTPMessage($otp, $data['mobile']);

// Store OTP in the database
$updateData = ['otp' => $otp];
$db->where('id', $data['id']);
$db->update('vehicles', $updateData);

echo json_encode(['success' => 'OTP sent successfully.']);
exit;
