<?php
require_once 'config/config.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$db = getDbInstance();
$db->join('properties p', 'pb.property_id = p.id', 'LEFT');

// Apply filters
if (!empty($_GET['status'])) $db->where('pb.status', $_GET['status']);
if (!empty($_GET['guest_name'])) $db->where('pb.guest_name', '%' . $_GET['guest_name'] . '%', 'LIKE');
if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $db->where('pb.check_in_date', [$_GET['start_date'], $_GET['end_date']], 'BETWEEN');
}

$select = [
    'p.hotel_name',
    'pb.booking_id',
    'pb.guest_name',
    'pb.check_in_date',
    'pb.check_out_date',
    'pb.no_of_nights',
    'pb.meal_plan',
    'pb.single_room_count',
    'pb.double_room_count',
    'pb.extra_bed_count',
    'pb.child_no_bed_count',
    'pb.total_amount',
    'pb.booking_token',
    'pb.due_amount',
    'pb.agent_type',
    'pb.agent_name',
    'pb.agent_email',
    'pb.guest_email',
    'pb.guest_whatsapp',
    'pb.status',
    'pb.created_at'
];

$rows = $db->arraybuilder()->get('property_booking pb', null, $select);

// Create Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header Row
$sheet->fromArray([
    'Hotel Name',
    'Booking ID',
    'Guest Name',
    'Check-In',
    'Check-Out',
    'Nights',
    'Meal Plan',
    'Single Rooms',
    'Double Rooms',
    'Extra Beds',
    'Child No Bed',
    'Total Amount',
    'Booking Token',
    'Due Amount',
    'Agent Type',
    'Agent Name',
    'Agent Email',
    'Guest Email',
    'Guest WhatsApp',
    'Status',
    'Created At'
], null, 'A1');

// Data Rows
$sheet->fromArray($rows, null, 'A2');

// Output Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="booking_report.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
