<?php

if (!function_exists('sendPropertyBookingInvoiceEmail')) {
    function sendPropertyBookingInvoiceEmail(int $bookingId): array
    {
        require_once __DIR__ . '/../config/config.php';
        require_once BASE_PATH . '/Dompdf/vendor/autoload.php';
        require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';
        require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';

        $db = getDbInstance();
        $db->join('properties p', 'pb.property_id = p.id', 'LEFT');
        $db->where('pb.id', $bookingId);
        $booking = $db->getOne('property_booking pb', [
            'pb.id', 'pb.booking_id', 'pb.guest_name', 'pb.guest_email', 'pb.agent_email', 'pb.guest_whatsapp',
            'pb.check_in_date', 'pb.check_out_date', 'pb.no_of_nights', 'pb.meal_plan',
            'pb.double_room_count', 'pb.single_room_count', 'pb.extra_bed_count', 'pb.child_no_bed_count',
            'pb.total_pax', 'pb.total_amount', 'pb.extra_services', 'pb.extra_services_total',
            'pb.discount_percent', 'pb.discount_amount', 'pb.final_total', 'pb.booking_token', 'pb.due_amount',
            'pb.status', 'p.hotel_name', 'p.address'
        ]);

        if (!$booking) {
            return ['success' => false, 'message' => 'Booking not found.'];
        }

        $guestEmail = $booking['guest_email'] ?? '';
        $agentEmail = $booking['agent_email'] ?? '';
        if (!$guestEmail && !$agentEmail) {
            return ['success' => false, 'message' => 'No email address available to send.'];
        }

        $services = [];
        $servicesTotal = 0;
        if (!empty($booking['extra_services'])) {
            $decoded = json_decode($booking['extra_services'], true);
            if (is_array($decoded)) {
                foreach ($decoded as $svc) {
                    $svcName = htmlspecialchars($svc['name'] ?? 'Service');
                    $svcPrice = (float)($svc['price'] ?? 0);
                    $servicesTotal += $svcPrice;
                    $services[] = ['name' => $svcName, 'price' => $svcPrice];
                }
            }
        }

        $storedServicesTotal = (float)($booking['extra_services_total'] ?? 0);
        if ($storedServicesTotal > 0) {
            $servicesTotal = $storedServicesTotal;
        }

        $baseTotal = (float)($booking['total_amount'] ?? 0);
        $discountPct = (float)($booking['discount_percent'] ?? 0);
        $discountAmount = (float)($booking['discount_amount'] ?? 0);
        if ($discountAmount <= 0 && $discountPct > 0) {
            $discountAmount = $baseTotal * ($discountPct / 100);
        }
        $finalTotal = max(0, $baseTotal + $servicesTotal - $discountAmount);
        $tokenPaid = (float)($booking['booking_token'] ?? 0);
        $dueAmount = max(0, $finalTotal - $tokenPaid);
        $nights = max(1, (int)($booking['no_of_nights'] ?? 1));
        $perNight = $finalTotal / $nights;

        $serviceRows = '';
        if (!empty($services)) {
            foreach ($services as $svc) {
                $serviceRows .= '<tr><td style="padding:6px 8px;border:1px solid #e6e2dc;">' . $svc['name'] . '</td><td style="padding:6px 8px;border:1px solid #e6e2dc;text-align:right;">₹' . number_format($svc['price'], 2) . '</td></tr>';
            }
        } else {
            $serviceRows = '<tr><td colspan="2" style="padding:6px 8px;border:1px solid #e6e2dc;">No addon services</td></tr>';
        }

        $checkIn = !empty($booking['check_in_date']) ? date('d M Y', strtotime($booking['check_in_date'])) : '-';
        $checkOut = !empty($booking['check_out_date']) ? date('d M Y', strtotime($booking['check_out_date'])) : '-';

        $html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; color: #2b2b2b; font-size: 11px; margin: 0; line-height: 1.28; }
    @page { margin: 14px 16px; }
    .topbar { background: #b19470; color: #fff; border-radius: 7px; padding: 10px 12px; margin-bottom: 8px; }
    .title { font-size: 17px; font-weight: 700; }
    .subtitle { font-size: 12px; opacity: 0.95; }
    .box { border: 1px solid #e5dccf; border-radius: 7px; padding: 9px; margin-bottom: 6px; }
    .section-title { margin: 7px 0 4px; font-weight: 700; color: #8f775a; font-size: 12px; }
    .grid { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    .grid td, .grid th { border: 1px solid #e6e2dc; padding: 5px 6px; }
    .grid th { background: #f4eee6; text-align: left; font-weight: 700; }
    .label { background: #fcfaf7; width: 34%; }
    .right { text-align: right; }
    .summary { width: 100%; border-collapse: collapse; margin-top: 4px; }
    .summary td { padding: 4px 0; }
    .summary .head { font-weight: 700; color: #8f775a; padding-top: 6px; }
    .summary .strong { font-weight: 700; font-size: 14px; }
    .terms-page { page-break-before: always; padding-top: 4px; }
    .terms-title { font-size: 16px; font-weight: 700; color: #8f775a; margin-bottom: 8px; }
    .terms-box { border: 1px solid #e5dccf; border-radius: 7px; padding: 10px; }
    .terms-line { margin-bottom: 8px; }
    .accept-row { margin-top: 12px; border-top: 1px dashed #d8cdbd; padding-top: 10px; font-weight: 700; }
    .checkbox { display: inline-block; width: 15px; height: 15px; line-height: 15px; text-align: center; border: 1px solid #2b2b2b; border-radius: 2px; margin-right: 7px; font-size: 12px; font-weight: 700; vertical-align: middle; }
    .accept-text { vertical-align: middle; }
</style>
</head>
<body>
    <div class="topbar">
        <table style="width:100%; border-collapse:collapse; color:#fff;">
            <tr>
                <td>
                    <div class="title">Invoice</div>
                    <div class="subtitle">' . htmlspecialchars($booking['hotel_name'] ?? 'Saser Scenic Pangong') . '</div>
                </td>
                <td style="text-align:right; vertical-align:top;">
                    <div style="font-size:12px;">Booking Ref: <strong>' . htmlspecialchars($booking['booking_id'] ?? $booking['id']) . '</strong></div>
                    <div style="font-size:12px;">Status: ' . htmlspecialchars($booking['status'] ?? '-') . '</div>
                </td>
            </tr>
        </table>
    </div>

    <table style="width:100%; border-collapse:collapse; margin-bottom:4px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:50%; padding-right:6px; vertical-align:top;">
                <div class="box">
                    <div style="font-size:12px; font-weight:700; color:#8f775a; margin-bottom:4px;">Guest Details</div>
                    <div>Name: ' . htmlspecialchars($booking['guest_name'] ?? 'Guest') . '</div>
                    <div>Email: ' . htmlspecialchars($booking['guest_email'] ?? '-') . '</div>
                    <div>WhatsApp: ' . htmlspecialchars($booking['guest_whatsapp'] ?? '-') . '</div>
                </div>
            </td>
            <td style="width:50%; padding-left:6px; vertical-align:top;">
                <div class="box">
                    <div style="font-size:12px; font-weight:700; color:#8f775a; margin-bottom:4px;">Booking Snapshot</div>
                    <div>Check In: ' . $checkIn . '</div>
                    <div>Check Out: ' . $checkOut . '</div>
                    <div>Rate / Night: ₹' . number_format($perNight, 2) . '</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Stay Details</div>
    <table class="grid">
        <tr><td class="label">Property</td><td>' . htmlspecialchars($booking['hotel_name'] ?? '-') . '</td></tr>
        <tr><td class="label">Address</td><td>' . htmlspecialchars($booking['address'] ?? '-') . '</td></tr>
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
        <tr><td>Addon Services Total</td><td class="right">₹' . number_format($servicesTotal, 2) . '</td></tr>
        <tr><td>Discount (' . number_format($discountPct, 2) . '%)</td><td class="right">-₹' . number_format($discountAmount, 2) . '</td></tr>
        <tr><td class="head">Final Total</td><td class="right head">₹' . number_format($finalTotal, 2) . '</td></tr>
        <tr><td>Token Paid</td><td class="right">-₹' . number_format($tokenPaid, 2) . '</td></tr>
        <tr><td class="strong">Due Amount</td><td class="right strong">₹' . number_format($dueAmount, 2) . '</td></tr>
    </table>

    <div class="terms-page">
        <div class="terms-title">Terms and Conditions</div>
        <div class="terms-box">
            <div class="terms-line">Saser Scenic Pangong is a pure vegetarian retreat, thoughtfully designed to offer a peaceful and comfortable stay amidst the breathtaking beauty of Pangong. Non-vegetarian meals may also be arranged on special request at the time of booking.</div>
            <div class="terms-line">Guests can enjoy geyser-based hot water shower facility, with up to 25 litres of hot water provided. While we always try our best to offer hot water 24 hours, owing to the extreme weather conditions in the region, morning hot shower availability may remain limited until the end of April. Hot shower service is generally available till 12:00 AM. Whenever required, our team will be happy to arrange an alternative hot water bucket service for your comfort.</div>
            <div class="terms-line">The property is equipped with 24-hour emergency power backup, along with central dual AC heating from 4:00 PM to 9:30 AM, and electric blankets in all rooms to ensure a warm and pleasant stay.</div>
            <div class="terms-line">Please note that stargazing experiences are subject to weather conditions and clear sky visibility.</div>
            <div class="terms-line">The booking amount is non-refundable for cancellations made within 15 days prior to the check-in date.</div>
            <div class="terms-line">For cancellations due to medical reasons, a valid doctor\'s prescription or medical report issued by Leh Hospital must be provided for review.</div>
            <div class="terms-line">Guests are requested to kindly contact our property staff, Mr. Sammer, at least 1 hour prior to arrival, so that the room can be prepared and kept ready for check-in.</div>
            <div class="terms-line"><strong>Acceptance Note</strong></div>
            <div class="terms-line">I hereby confirm that I have read, understood, and accepted the above terms and conditions, and I confirm my booking by paying the token amount.</div>

            <div class="accept-row">
                <span class="checkbox">&#10003;</span>
                <span class="accept-text">I have read and accept Terms &amp; Conditions.</span>
            </div>
        </div>
    </div>
</body>
</html>';

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $tmpPdf = tempnam(sys_get_temp_dir(), 'booking_invoice_') . '.pdf';
        file_put_contents($tmpPdf, $dompdf->output());

        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = GMAIL_USER;
        $mail->Password = GMAIL_PASSWORD;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->isHTML(true);
        $mail->setFrom(GMAIL_FROM, 'Ladakh DMC');

        if ($guestEmail) {
            $mail->addAddress($guestEmail);
        }
        if ($agentEmail && $agentEmail !== $guestEmail) {
            $mail->addAddress($agentEmail);
        }

        $mail->Subject = 'Invoice: ' . ($booking['booking_id'] ?? $booking['id']) . ' - ' . ($booking['hotel_name'] ?? 'Saser Scenic Pangong');
        $mail->Body = '<p>Dear ' . htmlspecialchars($booking['guest_name'] ?? 'Guest') . ',</p><p>Please find attached your booking invoice.</p><p>Regards,<br>Ladakh DMC</p>';
        $mail->addAttachment($tmpPdf, 'invoice_' . preg_replace('/[^A-Za-z0-9\-_]/', '', ($booking['booking_id'] ?? $booking['id'])) . '.pdf');

        try {
            $mail->send();
            @unlink($tmpPdf);
            return ['success' => true, 'message' => 'Invoice sent successfully.'];
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            @unlink($tmpPdf);
            return ['success' => false, 'message' => 'Invoice sending failed: ' . $mail->ErrorInfo];
        }
    }
}