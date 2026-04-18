<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
require 'vendor/autoload.php';
require_once __DIR__ . '/helpers/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/helpers/vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/helpers/vendor/phpmailer/phpmailer/src/Exception.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$managerEmails = [
    'kapoorsameer887@gmail.com',
    'Ladakh@go2ladakh.in'
];
$reportDate = date('Y-m-d');
$reportLabel = date('d M Y');

$db = getDbInstance();
$db->join('properties p', 'pb.property_id = p.id', 'LEFT');
$db->where('pb.check_in_date', $reportDate);

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
    'pb.no_of_rooms',
    'pb.total_pax',
    'pb.total_amount',
    'pb.booking_token',
    'pb.due_amount',
    'pb.final_total',
    'pb.discount_percent',
    'pb.discount_amount',
    'pb.agent_type',
    'pb.agent_name',
    'pb.agent_email',
    'pb.guest_email',
    'pb.guest_whatsapp',
    'pb.status',
    'pb.created_at'
];

$rows = $db->arraybuilder()->get('property_booking pb', null, $select);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$headers = [
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
    'No. of Rooms',
    'Total Guests',
    'Total Amount',
    'Booking Token',
    'Due Amount',
    'Final Total',
    'Discount %',
    'Discount Amount',
    'Agent Type',
    'Agent Name',
    'Agent Email',
    'Guest Email',
    'Guest WhatsApp',
    'Status',
    'Created At'
];

$sheet->fromArray($headers, null, 'A1');

if (!empty($rows)) {
    $dataRows = [];
    foreach ($rows as $row) {
        $dataRows[] = [
            $row['hotel_name'] ?? '',
            $row['booking_id'] ?? '',
            $row['guest_name'] ?? '',
            $row['check_in_date'] ?? '',
            $row['check_out_date'] ?? '',
            $row['no_of_nights'] ?? '',
            $row['meal_plan'] ?? '',
            $row['single_room_count'] ?? '',
            $row['double_room_count'] ?? '',
            $row['extra_bed_count'] ?? '',
            $row['child_no_bed_count'] ?? '',
            $row['no_of_rooms'] ?? '',
            $row['total_pax'] ?? '',
            $row['total_amount'] ?? '',
            $row['booking_token'] ?? '',
            $row['due_amount'] ?? '',
            $row['final_total'] ?? '',
            $row['discount_percent'] ?? '',
            $row['discount_amount'] ?? '',
            $row['agent_type'] ?? '',
            $row['agent_name'] ?? '',
            $row['agent_email'] ?? '',
            $row['guest_email'] ?? '',
            $row['guest_whatsapp'] ?? '',
            $row['status'] ?? '',
            $row['created_at'] ?? '',
        ];
    }
    $sheet->fromArray($dataRows, null, 'A2');
} else {
    $sheet->setCellValue('A2', 'No check-in bookings found for ' . $reportLabel);
}

$sheet->setTitle('Today Bookings');

$tmpFile = tempnam(sys_get_temp_dir(), 'today_bookings_') . '.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($tmpFile);

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = GMAIL_USER;
    $mail->Password = GMAIL_PASSWORD;
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->isHTML(true);
    $mail->setFrom(GMAIL_FROM, 'Ladakh DMC');
    foreach ($managerEmails as $managerEmail) {
        $mail->addAddress($managerEmail);
    }
    $mail->Subject = 'Today Check-In Booking Report - ' . $reportLabel;
    $mail->Body = '<p>Hello Manager,</p><p>Please find attached today\'s check-in booking report for <strong>' . $reportLabel . '</strong>.</p><p>Regards,<br>Ladakh DMC</p>';
    $mail->addAttachment($tmpFile, 'today_booking_report_' . $reportDate . '.xlsx');

    $mail->send();
    @unlink($tmpFile);
    $_SESSION['success'] = 'Today booking report sent to managers successfully.';
} catch (Exception $e) {
    @unlink($tmpFile);
    $_SESSION['failure'] = 'Unable to send today booking report: ' . $mail->ErrorInfo;
}

header('Location: property_booking_list.php');
exit;
