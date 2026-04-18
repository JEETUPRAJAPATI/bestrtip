<?php
session_start();
require_once '../config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

header('Content-Type: application/json');

$propertyId = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;
$checkInDate = $_POST['check_in_date'] ?? '';
$checkOutDate = $_POST['check_out_date'] ?? '';
$excludeBookingId = isset($_POST['booking_id']) && ctype_digit((string)$_POST['booking_id']) ? (int)$_POST['booking_id'] : null;

if (!$propertyId || empty($checkInDate) || empty($checkOutDate)) {
    echo json_encode([
        'success' => false,
        'message' => 'Property and dates are required.',
        'total_rooms' => 0,
        'booked_rooms' => 0,
        'available_rooms' => 0,
    ]);
    exit;
}

$db = getDbInstance();
$property = $db->where('id', $propertyId)->getOne('properties', ['no_of_rooms']);

if (!$property) {
    echo json_encode([
        'success' => false,
        'message' => 'Property not found.',
        'total_rooms' => 0,
        'booked_rooms' => 0,
        'available_rooms' => 0,
    ]);
    exit;
}

$db->where('property_id', $propertyId);
$db->where('status', ['Confirmed', 'Hold'], 'IN');
$db->where('check_in_date', $checkOutDate, '<=');
$db->where('check_out_date', $checkInDate, '>=');

if (!empty($excludeBookingId)) {
    $db->where('id', $excludeBookingId, '!=');
}

$bookedRooms = (int)$db->getValue('property_booking', 'COALESCE(SUM(no_of_rooms), 0)');
$totalRooms = (int)($property['no_of_rooms'] ?? 0);
$availableRooms = max(0, $totalRooms - $bookedRooms);

echo json_encode([
    'success' => true,
    'message' => 'Availability calculated successfully.',
    'total_rooms' => $totalRooms,
    'booked_rooms' => $bookedRooms,
    'available_rooms' => $availableRooms,
]);
