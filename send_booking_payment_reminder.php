<?php
session_start();
require_once 'config/config.php';
require_once __DIR__ . '/helpers/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/helpers/vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/helpers/vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$id = isset($_GET['crm']) && !empty($_GET['crm']) ? decryptId($_GET['crm']) : '';

if (!$id || !ctype_digit((string)$id)) {
    $_SESSION['failure'] = 'Invalid booking ID.';
    header('Location: property_booking_list.php');
    exit;
}

$db = getDbInstance();
$db->join('properties p', 'pb.property_id = p.id', 'LEFT');
$db->where('pb.id', (int)$id);
$booking = $db->getOne('property_booking pb', [
    'pb.id',
    'pb.booking_id',
    'pb.guest_name',
    'pb.guest_email',
    'pb.agent_email',
    'pb.check_in_date',
    'pb.check_out_date',
    'pb.no_of_nights',
    'pb.total_amount',
    'pb.final_total',
    'pb.booking_token',
    'pb.due_amount',
    'p.hotel_name'
]);

if (!$booking) {
    $_SESSION['failure'] = 'Booking not found.';
    header('Location: property_booking_list.php');
    exit;
}

$guestEmail = $booking['guest_email'] ?? '';
$agentEmail = $booking['agent_email'] ?? '';

if (!$guestEmail && !$agentEmail) {
    $_SESSION['failure'] = 'No email address available to send.';
    header('Location: property_booking_list.php');
    exit;
}

$bookingRef = htmlspecialchars($booking['booking_id'] ?? $booking['id']);
$guestName = htmlspecialchars($booking['guest_name'] ?? 'Guest');
$hotelName = htmlspecialchars($booking['hotel_name'] ?? 'Saser Scenic Pangong');
$dueAmount = (float)($booking['due_amount'] ?? 0);
$finalTotal = (float)($booking['final_total'] ?? 0);
$tokenPaid = (float)($booking['booking_token'] ?? 0);
$tokenPaidPct = $finalTotal > 0 ? min(100, max(0, ($tokenPaid / $finalTotal) * 100)) : 0;
$dueAmountPct = $finalTotal > 0 ? min(100, max(0, ($dueAmount / $finalTotal) * 100)) : 0;
$paymentUrl = BASE_URL . '/payment_terms.php?crm=' . urlencode(encryptId($booking['id']));

$mail = new PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = GMAIL_USER;
$mail->Password = GMAIL_PASSWORD;
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->setFrom(GMAIL_FROM, 'Ladakh DMC');

if ($guestEmail) {
    $mail->addAddress($guestEmail);
}
if ($agentEmail && $agentEmail !== $guestEmail) {
    $mail->addAddress($agentEmail);
}

$mail->Subject = 'Payment Reminder: ' . $bookingRef . ' - ' . $hotelName;
$mail->Body = '
    <html>
    <body style="font-family: Arial, sans-serif; color: #222; line-height: 1.6;">
        <p>Dear ' . $guestName . ',</p>
        <p>This is a friendly reminder for your pending payment at <strong>' . $hotelName . '</strong>.</p>
        <p><strong>Booking Ref:</strong> ' . $bookingRef . '<br>
        <strong>Final Total:</strong> &#8377;' . number_format($finalTotal, 2) . ' (100%)<br>
        <strong>Token Paid:</strong> &#8377;' . number_format($tokenPaid, 2) . ' (' . number_format($tokenPaidPct, 2) . '%)<br>
        <strong>Pending Amount:</strong> &#8377;' . number_format($dueAmount, 2) . ' (' . number_format($dueAmountPct, 2) . '%)</p>
        <p>Please complete the remaining payment at your earliest convenience.</p>
        <p><a href="' . $paymentUrl . '" style="display:inline-block;background:#b19470;color:#fff;padding:10px 16px;text-decoration:none;border-radius:8px;">Open Payment Details</a></p>
        <p>Regards,<br>Ladakh DMC</p>
    </body>
    </html>';

try {
    $mail->send();
    $_SESSION['success'] = 'Payment reminder sent successfully.';
} catch (Exception $e) {
    $_SESSION['failure'] = 'Payment reminder failed: ' . $mail->ErrorInfo;
}

header('Location: property_booking_list.php');
exit;
