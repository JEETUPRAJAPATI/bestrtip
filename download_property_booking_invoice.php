<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/Dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id = isset($_GET['crm']) && !empty($_GET['crm']) ? decryptId($_GET['crm']) : '';
if (empty($id) || !ctype_digit((string)$id)) {
    exit('Invalid booking reference.');
}

$db = getDbInstance();
$db->join('properties p', 'pb.property_id = p.id', 'LEFT');
$db->where('pb.id', (int)$id);
$booking = $db->getOne('property_booking pb', [
    'pb.id',
    'pb.booking_id',
    'pb.guest_name',
    'pb.guest_email',
    'pb.guest_whatsapp',
    'pb.check_in_date',
    'pb.check_out_date',
    'pb.no_of_nights',
    'pb.meal_plan',
    'pb.double_room_count',
    'pb.single_room_count',
    'pb.extra_bed_count',
    'pb.child_no_bed_count',
    'pb.total_pax',
    'pb.total_amount',
    'pb.extra_services',
    'pb.extra_services_total',
    'pb.discount_percent',
    'pb.discount_amount',
    'pb.final_total',
    'pb.booking_token',
    'pb.due_amount',
    'pb.status',
    'pb.created_at',
    'p.hotel_name',
    'p.address'
]);

if (!$booking) {
    exit('Booking not found.');
}

$serviceRows = '';
$servicesTotalFromJson = 0;
if (!empty($booking['extra_services'])) {
    $services = json_decode($booking['extra_services'], true);
    if (is_array($services)) {
        foreach ($services as $service) {
            $name = htmlspecialchars($service['name'] ?? 'Service');
            $price = (float)($service['price'] ?? 0);
            $servicesTotalFromJson += $price;
            $serviceRows .= '<tr><td>' . $name . '</td><td style="text-align:right;">₹' . number_format($price, 2) . '</td></tr>';
        }
    }
}
if ($serviceRows === '') {
    $serviceRows = '<tr><td colspan="2">No addon services</td></tr>';
}

$guestName = htmlspecialchars($booking['guest_name'] ?? 'Guest');
$bookingRef = htmlspecialchars($booking['booking_id'] ?? ('BK-' . $booking['id']));
$hotelName = htmlspecialchars($booking['hotel_name'] ?? 'Saser Scenic Pangong');
$hotelAddress = htmlspecialchars($booking['address'] ?? 'Pangong, Ladakh');
$checkIn = !empty($booking['check_in_date']) ? date('d M Y', strtotime($booking['check_in_date'])) : '-';
$checkOut = !empty($booking['check_out_date']) ? date('d M Y', strtotime($booking['check_out_date'])) : '-';
$invoiceDate = date('d M Y');

$baseTotal = (float)($booking['total_amount'] ?? 0);
$storedServicesTotal = (float)($booking['extra_services_total'] ?? 0);
$addonServicesTotal = $storedServicesTotal;
if ($servicesTotalFromJson > 0 && abs($servicesTotalFromJson - $storedServicesTotal) > 0.01) {
    $addonServicesTotal = $servicesTotalFromJson;
}
$discountPercent = (float)($booking['discount_percent'] ?? 0);
$discountAmount = (float)($booking['discount_amount'] ?? 0);
if ($discountAmount <= 0 && $discountPercent > 0) {
    $discountAmount = $baseTotal * ($discountPercent / 100);
}
$finalTotal = max(0, $baseTotal + $addonServicesTotal - $discountAmount);
$tokenPaid = (float)($booking['booking_token'] ?? 0);
$dueAmount = max(0, $finalTotal - $tokenPaid);
$nights = max(1, (int)($booking['no_of_nights'] ?? 1));
$perNight = $finalTotal / $nights;

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 14px 16px; }
    body { font-family: DejaVu Sans, sans-serif; color: #2b2b2b; font-size: 11px; margin: 0; line-height: 1.28; }
    .topbar { background: #b19470; color: #fff; border-radius: 7px; padding: 10px 12px; margin-bottom: 8px; page-break-inside: avoid; }
    .title { font-size: 17px; font-weight: 700; }
    .subtitle { font-size: 12px; opacity: 0.95; }
    .meta-box { border: 1px solid #e5dccf; border-radius: 7px; padding: 9px; margin-bottom: 6px; page-break-inside: avoid; }
    .meta-title { font-size: 12px; font-weight: 700; color: #8f775a; margin-bottom: 4px; }
    .meta-line { margin-bottom: 2px; }
    .section-title { margin: 7px 0 4px; font-weight: 700; color: #8f775a; font-size: 12px; }
    .grid { width: 100%; border-collapse: collapse; margin-bottom: 6px; page-break-inside: avoid; }
    .grid td, .grid th { border: 1px solid #e6e2dc; padding: 5px 6px; }
    .grid th { background: #f4eee6; text-align: left; font-weight: 700; }
    .grid .label { background: #fcfaf7; width: 34%; }
    .right { text-align: right; }
    .summary { width: 100%; border-collapse: collapse; margin-top: 4px; page-break-inside: avoid; }
    .summary td { padding: 4px 0; }
    .summary .head { font-weight: 700; color: #8f775a; padding-top: 6px; }
    .summary .strong { font-weight: 700; font-size: 14px; }
    .footer-note { margin-top: 8px; border-top: 1px dashed #d8cdbd; padding-top: 6px; color: #666; font-size: 10px; page-break-inside: avoid; }
    .no-break { page-break-inside: avoid; }
    tr { page-break-inside: avoid; }
</style>
</head>
<body>
    <div class="topbar">
        <table style="width:100%; border-collapse:collapse; color:#fff;">
            <tr>
                <td>
                    <div class="title">Payment Invoice</div>
                    <div class="subtitle">' . $hotelName . '</div>
                </td>
                <td style="text-align:right; vertical-align:top;">
                    <div style="font-size:12px;">Booking Ref: <strong>' . $bookingRef . '</strong></div>
                    <div style="font-size:12px;">Invoice Date: ' . $invoiceDate . '</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="no-break" style="width:100%; border-collapse:collapse; margin-bottom:4px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:50%; padding-right:6px; vertical-align:top;">
                <div class="meta-box">
                    <div class="meta-title">Guest Details</div>
                    <div class="meta-line">Name: ' . $guestName . '</div>
                    <div class="meta-line">Email: ' . htmlspecialchars($booking['guest_email'] ?? '-') . '</div>
                    <div class="meta-line">WhatsApp: ' . htmlspecialchars($booking['guest_whatsapp'] ?? '-') . '</div>
                </div>
            </td>
            <td style="width:50%; padding-left:6px; vertical-align:top;">
                <div class="meta-box">
                    <div class="meta-title">Booking Snapshot</div>
                    <div class="meta-line">Status: ' . htmlspecialchars($booking['status'] ?? '-') . '</div>
                    <div class="meta-line">Check In: ' . $checkIn . '</div>
                    <div class="meta-line">Check Out: ' . $checkOut . '</div>
                    <div class="meta-line">Rate / Night: ₹' . number_format($perNight, 2) . '</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="no-break">
        <div class="section-title">Stay Details</div>
        <table class="grid">
            <tr><td class="label">Property</td><td>' . $hotelName . '</td></tr>
            <tr><td class="label">Address</td><td>' . $hotelAddress . '</td></tr>
            <tr><td class="label">Check In</td><td>' . $checkIn . '</td></tr>
            <tr><td class="label">Check Out</td><td>' . $checkOut . '</td></tr>
            <tr><td class="label">Nights</td><td>' . (int)($booking['no_of_nights'] ?? 0) . '</td></tr>
            <tr><td class="label">Meal Plan</td><td>' . htmlspecialchars($booking['meal_plan'] ?? '-') . '</td></tr>
        </table>

        <div class="section-title">Occupancy</div>
        <table class="grid">
            <tr><td class="label">Double Rooms</td><td>' . (int)($booking['double_room_count'] ?? 0) . '</td></tr>
            <tr><td class="label">Single Rooms</td><td>' . (int)($booking['single_room_count'] ?? 0) . '</td></tr>
            <tr><td class="label">Extra Beds</td><td>' . (int)($booking['extra_bed_count'] ?? 0) . '</td></tr>
            <tr><td class="label">Child No Bed</td><td>' . (int)($booking['child_no_bed_count'] ?? 0) . '</td></tr>
            <tr><td class="label">Total Guests</td><td>' . (int)($booking['total_pax'] ?? 0) . '</td></tr>
        </table>

        <div class="section-title">Addon Services</div>
        <table class="grid">
            <tr><th>Service</th><th class="right">Amount</th></tr>
            ' . $serviceRows . '
        </table>

        <div class="section-title">Payment Summary</div>
        <table class="summary">
            <tr><td>Total Amount</td><td class="right">₹' . number_format($baseTotal, 2) . '</td></tr>
            <tr><td>Addon Services Total</td><td class="right">₹' . number_format($addonServicesTotal, 2) . '</td></tr>
            <tr><td>Discount (' . number_format($discountPercent, 2) . '%)</td><td class="right">-₹' . number_format($discountAmount, 2) . '</td></tr>
            <tr><td class="head">Final Total</td><td class="right head">₹' . number_format($finalTotal, 2) . '</td></tr>
            <tr><td>Token Paid</td><td class="right">-₹' . number_format($tokenPaid, 2) . '</td></tr>
            <tr><td class="strong">Due Amount</td><td class="right strong">₹' . number_format($dueAmount, 2) . '</td></tr>
        </table>
    </div>

    <div class="footer-note">
        This is a system-generated invoice for booking confirmation and payment reference.
    </div>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = 'booking_invoice_' . preg_replace('/[^A-Za-z0-9\-_]/', '', ($booking['booking_id'] ?? $booking['id'])) . '.pdf';
$dompdf->stream($filename, ['Attachment' => true]);
exit;
