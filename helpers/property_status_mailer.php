<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';

if (!function_exists('sendPropertyStatusEmail')) {
    function sendPropertyStatusEmail(int $id): array
    {
        $db = getDbInstance();
        $db->where('id', $id);
        $booking = $db->getOne('property_booking');

        if (!$booking) {
            return ['success' => false, 'message' => 'Booking not found.'];
        }

        $db->where('id', $booking['property_id']);
        $property = $db->getOne('properties');

        $guestEmail = $booking['guest_email'] ?? '';
        $agentEmail = $booking['agent_email'] ?? '';

        if (!$guestEmail && !$agentEmail) {
            return ['success' => false, 'message' => 'No email address available to send.'];
        }

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
        $mail->setFrom(GMAIL_FROM, "Ladakh DMC");

        if ($guestEmail) {
            $mail->addAddress($guestEmail);
        }
        if ($agentEmail && $agentEmail !== $guestEmail) {
            $mail->addAddress($agentEmail);
        }

        $propName = htmlspecialchars($property['hotel_name'] ?? 'Saser Scenic Pangong');
        $guestName = htmlspecialchars($booking['guest_name'] ?? 'Guest');
        $status = $booking['status'] ?? 'Confirmed';
        $bookingId = $booking['booking_id'] ?? $booking['id'];

        $subject = "";
        $email_greeting_body = "";

        switch ($status) {
            case 'Enquiry':
                $subject = "Bespoke Stay at " . $propName . " | Availability for " . $guestName . " - " . date('d M Y');
                $email_greeting_body = "Greetings from the shores of Pangong Lake.<br>Thank you for considering $propName for your upcoming journey to Ladakh. It is our pleasure to confirm that we currently have availability to host you for your requested dates (Ref: $bookingId).<br>At Saser Scenic, we pride ourselves on offering an unparalleled sanctuary amidst the rugged beauty of the Himalayas. To ensure your stay is as seamless as it is memorable, we are delighted to extend a specially curated preferred rate for your consideration.<br>Please let us know if you wish to proceed, and we will be happy to assist you in finalizing the details of your retreat.";
                break;
            case 'Hold':
                $subject = "Exclusive Hold: Your Upcoming Experience at " . $propName . " - " . date('d M Y');
                $email_greeting_body = "Following your recent inquiry, we have placed a priority hold on your requested accommodation at $propName (Booking ID: $bookingId).<br>To ensure you do not lose your preferred dates and our exclusive rate, we are pleased to maintain this reservation for a 24-hour window. Should you wish to guarantee your stay, please provide confirmation at your earliest convenience.<br>Please note that after this period, the hold will automatically expire to accommodate other discerning travelers. We would be honored to secure this space for you.";
                break;
            case 'Cancel':
                $subject = "Update Regarding Your Reservation – " . $propName . " - " . date('d M Y');
                $email_greeting_body = "We are writing to formally acknowledge the cancellation of your reservation at $propName (Booking ID: $bookingId), as per your request.<br>While we regret that we will not have the opportunity to host you on this occasion, we hope that your travels bring you to the shores of Pangong in the near future. Should your plans change or if you require assistance with a future booking for a more suitable date, please feel free to contact us directly.<br>We wish you safe and pleasant travels.";
                break;
            case 'Confirmed':
            default:
                $subject = "Confirmation of Stay: We Look Forward to Welcoming You to " . $propName . " - " . date('d M Y');
                $email_greeting_body = "It is with great pleasure that we formally confirm your reservation at $propName (Booking ID: $bookingId).<br>Your sanctuary by the lake is now secured for the dates mentioned. We are already preparing our property to ensure your stay is defined by comfort and the breathtaking tranquility of our high-altitude surroundings.<br>Please find your Official Guest Voucher attached, which includes essential details regarding your stay and arrival. Our team remains at your complete disposal should you require assistance with transport arrangements or local permits.<br>We await your arrival with great anticipation.";
                break;
        }

        $mail->Subject = $bookingId . " - " . $subject;

        // Assets
        $hero_img_1 = BASE_URL . "/assets/img/email/IMG_8132.JPEG";
        $hero_img_2 = BASE_URL . "/assets/img/email/IMG_8158.JPEG";
        $hero_img_3 = "https://r2imghtlak.mmtcdn.com/r2-mmt-htl-image/htl-imgs/202308230727264183-9061e247-281b-490a-989b-3e39248571c3.jpg";
        $hero_img_4 = BASE_URL . "/assets/img/email/IMG_8196.jpg";
        $map_img = BASE_URL . "/assets/img/email/map.png";

        $payment_terms_url = BASE_URL . "/payment_terms.php?crm=" . urlencode(encryptId($booking['id']));

        $special_remarks_html = "";
        if (!empty($booking['special_remarks'])) {
            $special_remarks_html = '
            <div style="background: #FAF9F6; border-left: 4px solid #B19470; padding: 15px; margin-top: 20px;">
                <h4 style="margin: 0 0 8px 0; color: #B19470; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Special Remarks</h4>
                <p style="margin: 0; font-size: 15px; line-height: 1.6; color: #444;">' . nl2br(htmlspecialchars($booking['special_remarks'])) . '</p>
            </div>';
        }

        $extra_services_html = '';
        $extra_services_total = 0;
        if (!empty($booking['extra_services'])) {
            $svcs = json_decode($booking['extra_services'], true);
            if (is_array($svcs)) {
                foreach ($svcs as $svc) {
                    $name = htmlspecialchars($svc['name'] ?? 'Service');
                    $price = floatval($svc['price'] ?? 0);
                    $extra_services_total += $price;
                    $extra_services_html .= '<tr><td style="padding-bottom:10px;color:#777;">' . $name . '</td><td style="text-align:right;font-weight:700;">&#8377;' . number_format($price, 2) . '</td></tr>';
                }
            }
        }
        $storedServicesTotal = floatval($booking['extra_services_total'] ?? 0);
        if ($storedServicesTotal > 0)
            $extra_services_total = $storedServicesTotal;

        $base_total = floatval($booking['total_amount'] ?? 0);
        $discount_pct = floatval($booking['discount_percent'] ?? 0);
        $discount_amt = floatval($booking['discount_amount'] ?? ($base_total * $discount_pct / 100));
        $final_total_calc = max(0, $base_total + $extra_services_total - $discount_amt);
        $token_paid_calc = floatval($booking['booking_token'] ?? 0);
        $token_paid_pct = $final_total_calc > 0 ? min(100, max(0, ($token_paid_calc / $final_total_calc) * 100)) : 0;

        $mail->Body = '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>@media only screen and (max-width: 768px) { .mob-block { display: block !important; width: 100% !important; padding-right: 0 !important; padding-left: 0 !important; box-sizing: border-box !important; } .mob-inner-pad { padding: 25px 15px !important; } .mob-img { height: auto !important; max-height: 250px !important; width: 100% !important; } .mob-font-lg { font-size: 32px !important; } }</style></head><body style="margin: 0; padding: 0;"><div style="font-family: \'Outfit\', Arial, sans-serif; background-color: #FDF8F4; padding: 40px 10px; color: #1a1a1a;"><div style="max-width: 1150px; margin: auto; background-color: #ffffff; border-radius: 32px; overflow: hidden; box-shadow: 0 30px 80px rgba(0,0,0,0.06); padding: 45px;"><div style="margin-bottom: 30px; padding-bottom: 10px; border-bottom: 1px solid #F0F0F0;"><p style="font-size: 18px; font-weight: 500; margin: 0 0 10px 0; color: #1a1a1a;">Dear ' . $guestName . ',</p><p style="font-size: 16px; line-height: 1.6; color: #444; margin: 0;">' . $email_greeting_body . '</p></div><table cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse;"><tr><td class="mob-block" style="width: 72%; vertical-align: top; padding-right: 45px;"><h1 class="mob-font-lg" style="font-size: 42px; font-weight: 700; margin: 0 0 12px 0; color: #1a1a1a;">' . $propName . '</h1><p style="color: #888; font-size: 16px; margin: 0 0 30px 0;">' . htmlspecialchars($property['address'] ?? '') . '</p>' . $special_remarks_html . '</td><td class="mob-block" style="width: 28%; vertical-align: top;"><div style="border: 1px solid #F2F2F2; border-radius: 28px; padding: 30px; background: #ffffff;"><span style="font-size: 28px; font-weight: 700; color: #1a1a1a;">&#8377;' . number_format($final_total_calc, 2) . '</span><table style="width: 100%; font-size: 15px; border-top: 1px solid #F8F8F8; padding-top: 25px; margin-bottom: 30px;"><tr><td style="padding-bottom: 14px; color: #777;">Check In</td><td style="text-align: right; font-weight: 700;">' . date('d M Y', strtotime($booking['check_in_date'])) . '</td></tr><tr><td style="padding-bottom: 14px; color: #777;">Check Out</td><td style="text-align: right; font-weight: 700;">' . date('d M Y', strtotime($booking['check_out_date'])) . '</td></tr></table><a href="' . $payment_terms_url . '" style="display: block; background: #B19470; color: #fff; padding: 18px; border-radius: 12px; text-align: center; text-decoration: none; font-weight: 700; font-size: 18px;">Pay Now</a></div></td></tr></table></div></div></body></html>';

        try {
            $mail->send();
            return ['success' => true, 'message' => 'Status email sent successfully.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Email failed: ' . $mail->ErrorInfo];
        }
    }
}
