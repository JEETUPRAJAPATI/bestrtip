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
        'booking_history' => [],
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
        'booking_history' => [],
    ]);
    exit;
}

$db->where('property_id', $propertyId);
$db->where('status', ['Confirmed', 'Hold'], 'IN');
// Hotel-style overlap: checkout date is non-occupied (exclusive boundary)
$db->where('check_in_date', $checkOutDate, '<');
$db->where('check_out_date', $checkInDate, '>');

if (!empty($excludeBookingId)) {
    $db->where('id', $excludeBookingId, '!=');
}

$bookedRooms = (int)$db->getValue('property_booking', 'COALESCE(SUM(no_of_rooms), 0)');
$totalRooms = (int)($property['no_of_rooms'] ?? 0);
$availableRooms = max(0, $totalRooms - $bookedRooms);

$historyDb = getDbInstance();
$historyDb->where('property_id', $propertyId);
$historyDb->where('status', ['Confirmed', 'Hold', 'Enquiry'], 'IN');
$historyDb->where('check_in_date', $checkOutDate, '<');
$historyDb->where('check_out_date', $checkInDate, '>');
if (!empty($excludeBookingId)) {
    $historyDb->where('id', $excludeBookingId, '!=');
}
$historyDb->orderBy('check_in_date', 'ASC');
$historyRows = $historyDb->get('property_booking', null, [
    'booking_id',
    'guest_name',
    'check_in_date',
    'check_out_date',
    'no_of_rooms',
    'single_room_count',
    'double_room_count',
    'status',
]);

$bookingHistory = [];
if (is_array($historyRows)) {
    foreach ($historyRows as $row) {
        $roomsCount = (int)($row['no_of_rooms'] ?? 0);
        if ($roomsCount <= 0) {
            $roomsCount = (int)($row['single_room_count'] ?? 0) + (int)($row['double_room_count'] ?? 0);
        }

        $bookingHistory[] = [
            'booking_id' => $row['booking_id'] ?? '',
            'guest_name' => $row['guest_name'] ?? '',
            'check_in_date' => !empty($row['check_in_date']) ? date('d-m-Y', strtotime($row['check_in_date'])) : '',
            'check_out_date' => !empty($row['check_out_date']) ? date('d-m-Y', strtotime($row['check_out_date'])) : '',
            'no_of_rooms' => $roomsCount,
            'status' => $row['status'] ?? '',
        ];
    }
}

echo json_encode([
    'success' => true,
    'message' => 'Availability calculated successfully.',
    'total_rooms' => $totalRooms,
    'booked_rooms' => $bookedRooms,
    'available_rooms' => $availableRooms,
    'booking_history' => $bookingHistory,
]);
