<?php
session_start();
require_once 'config/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/helpers/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/helpers/vendor/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/helpers/vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$id = isset($_GET['crm']) && !empty($_GET['crm']) ? decryptId($_GET['crm']) : "";

if (!$id) {
  $_SESSION['failure'] = "Invalid booking ID.";
  header("Location: property_booking_list.php");
  exit;
}

$db = getDbInstance();
$db->where('id', $id);
$booking = $db->getOne('property_booking');

if (!$booking) {
  $_SESSION['failure'] = "Booking not found.";
  header("Location: property_booking_list.php");
  exit;
}

// Fetch property name
$db->where('id', $booking['property_id']);
$property = $db->getOne('properties');

// Set default values for potentially missing fields
$booking['no_of_rooms'] = $booking['no_of_rooms'] ?? 'N/A';
$booking['payment_details'] = $booking['payment_details'] ?? 'Not specified';
$booking['special_remarks'] = $booking['special_remarks'] ?? 'None';
$property['google_map_link'] = $property['google_map_link'] ?? '#';

$guestEmail = $booking['guest_email'] ?? '';
$agentEmail = $booking['agent_email'] ?? '';

if (!$guestEmail && !$agentEmail) {
  $_SESSION['failure'] = "No email address available to send.";
  header("Location: property_booking_list.php");
  exit;
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

$mail->setFrom(GMAIL_FROM, "Ladakh DMC");

if ($guestEmail) {
  $mail->addAddress($guestEmail);
}
if ($agentEmail && $agentEmail !== $guestEmail) {
  $mail->addAddress($agentEmail);
}

$propName = htmlspecialchars($property['name'] ?? 'Saser Scenic Pangong');
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

// Hero Gallery Images
$hero_img_1 = BASE_URL . "/assets/img/email/IMG_8132.JPEG";
$hero_img_2 = BASE_URL . "/assets/img/email/IMG_8158.JPEG";
$hero_img_3 = BASE_URL . "/assets/img/email/96f91e51-f56d-47f1-bb42-1f3359df9655.JPG";
$hero_img_4 = BASE_URL . "/assets/img/email/IMG_8196.jpg";
// Replicating the Map from UI
$map_img = BASE_URL . "/assets/img/email/map.png";

// Icon URLs (PNGs for email support)
$icon_wifi = "https://img.icons8.com/material-rounded/32/B19470/wifi.png";
$icon_bed = "https://img.icons8.com/material-rounded/32/B19470/bed.png";
$icon_bath = "https://img.icons8.com/material-rounded/32/B19470/bath.png";
$icon_breakfast = "https://img.icons8.com/material-rounded/32/B19470/restaurant.png";
$icon_size = "https://img.icons8.com/material-rounded/32/B19470/resize-diagonal.png";



// Payment Symbols
$pay_visa = "https://img.icons8.com/color/32/000000/visa.png";
$pay_amex = "https://img.icons8.com/color/32/000000/amex.png";
$pay_stripe = "https://img.icons8.com/color/32/000000/stripe.png";
$pay_google = "https://img.icons8.com/color/32/000000/google-pay.png";
$pay_master = "https://img.icons8.com/color/32/000000/mastercard.png";

// WhatsApp Payment link
$wa_number = "+919906991500";
$wa_message = urlencode("Hi, I would like to pay for my booking: " . $booking['booking_id'] . ". My name is " . $booking['guest_name']);
$wa_url = "https://wa.me/$wa_number?text=$wa_message";
$payment_terms_url = BASE_URL . "/payment_terms.php?crm=" . urlencode(encryptId($booking['id']));

$special_remarks_html = "";
if (!empty($booking['special_remarks'])) {
  $special_remarks_html = '
    <div style="background: #FAF9F6; border-left: 4px solid #B19470; padding: 15px; margin-top: 20px;">
        <h4 style="margin: 0 0 8px 0; color: #B19470; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Special Remarks</h4>
        <p style="margin: 0; font-size: 15px; line-height: 1.6; color: #444;">' . nl2br(htmlspecialchars($booking['special_remarks'])) . '</p>
    </div>';
}

$special_note_tag = !empty($booking['special_remarks']) ? '<span style="display: inline-block; background: #FDF4F4; padding: 10px 22px; border-radius: 30px; font-size: 14px; color: #D63384; font-weight: 500; margin-bottom: 5px;">Special Note</span>' : '';

// ── NEW: Dynamic addon services ──
$extra_services_html = '';
$extra_services_total = 0;
if (!empty($booking['extra_services'])) {
  $svcs = json_decode($booking['extra_services'], true);
  if (is_array($svcs) && count($svcs)) {
    foreach ($svcs as $svc) {
      $name = htmlspecialchars($svc['name'] ?? 'Service');
      $price = floatval($svc['price'] ?? 0);
      $extra_services_total += $price;
      $extra_services_html .= '
        <tr>
          <td style="padding-bottom:10px;color:#777;">' . $name . '</td>
          <td style="text-align:right;font-weight:700;">₹' . number_format($price, 2) . '</td>
        </tr>';
    }
  }
}
if (empty($extra_services_html)) {
  $extra_services_html = '<tr><td style="padding-bottom:10px;color:#999;">No addon services added</td><td></td></tr>';
}

$storedServicesTotal = floatval($booking['extra_services_total'] ?? 0);
if ($storedServicesTotal > 0) {
  $extra_services_total = $storedServicesTotal;
}

// ── NEW: Room counts ──
$dbl_count = intval($booking['double_room_count'] ?? 0);
$cnb_count = intval($booking['child_no_bed_count'] ?? 0);
$eb_count = intval($booking['extra_bed_count'] ?? 0);
$sgl_count = intval($booking['single_room_count'] ?? 0);
$total_pax_calc = intval($booking['total_pax'] ?? (($dbl_count * 2) + $sgl_count + $eb_count + $cnb_count));
$room_rows = '';
if ($dbl_count)
  $room_rows .= '<tr><td style="padding:3px 0;color:#666;">DBL (Double)</td><td style="text-align:right;font-weight:600;">' . $dbl_count . '</td></tr>';
if ($cnb_count)
  $room_rows .= '<tr><td style="padding:3px 0;color:#666;">CNB (Child No Bed)</td><td style="text-align:right;font-weight:600;">' . $cnb_count . '</td></tr>';
if ($eb_count)
  $room_rows .= '<tr><td style="padding:3px 0;color:#666;">EB (Extra Bed)</td><td style="text-align:right;font-weight:600;">' . $eb_count . '</td></tr>';
if ($sgl_count)
  $room_rows .= '<tr><td style="padding:3px 0;color:#666;">SGL (Single)</td><td style="text-align:right;font-weight:600;">' . $sgl_count . '</td></tr>';

// ── NEW: Discount row ──
$discount_pct = floatval($booking['discount_percent'] ?? 0);
$discount_base = floatval($booking['total_amount']);
$discount_amt = floatval($booking['discount_amount'] ?? ($discount_base * $discount_pct / 100));
$discount_display = $discount_pct > 0
  ? '<tr><td style="padding-bottom:12px;color:#777;">Discount (' . $discount_pct . '%)</td><td style="text-align:right;font-weight:700;color:#e53935;">-₹' . number_format($discount_amt, 2) . '</td></tr>'
  : '';

$discount_badge = $discount_pct > 0 ? '<div style="float: right; background: #B19470; color: #fff; padding: 5px 12px; border-radius: 25px; font-size: 12px; font-weight: 700;">' . $discount_pct . '% Off</div>' : '';

$base_total = floatval($booking['total_amount'] ?? 0);
$final_total_calc = max(0, $base_total + $extra_services_total - $discount_amt);
$due_amount_calc = max(0, $final_total_calc - floatval($booking['booking_token'] ?? 0));

// ── NEW: Facilities & Amenities ──
$fac_items = [
  ['⚡', '24hrs Power Supply'],
  ['⭐', 'Stargazing'],
  ['🍽️', 'Restaurant'],
  ['🚿', 'Hot &amp; Cold Water'],
  ['🛁', 'Luxury Bathroom'],
];
$amen_items = [
  ['☕', 'Tea &amp; Coffee Kettle'],
  ['💨', 'Hair Dryer'],
  ['💧', 'Water Bottle'],
  ['👔', 'Iron'],
];
$build_fac = function ($items) {
  $html = '<table style="width:100%;border-collapse:collapse;">';
  $row = '<tr>';
  foreach ($items as $k => $it) {
    $row .= '<td class="mob-fac-item" style="width:33%;padding:7px 4px;font-size:13px;color:#444;">' . $it[0] . '  ' . $it[1] . '</td>';
    if (($k + 1) % 3 === 0 && $k + 1 < count($items)) {
      $row .= '</tr><tr>';
    }
  }
  $html .= $row . '</tr></table>';
  return $html;
};
$fac_html = $build_fac($fac_items);
$amen_html = $build_fac($amen_items);
// ── END NEW DATA PREP ──

$mail->Body = '<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  @media only screen and (max-width: 768px) {
    .mob-block { display: block !important; width: 100% !important; padding-right: 0 !important; padding-left: 0 !important; box-sizing: border-box !important; }
    .mob-pad { padding: 10px !important; }
    .mob-inner-pad { padding: 25px 15px !important; }
    .mob-img { height: auto !important; max-height: 250px !important; width: 100% !important; }
    .mob-mb { margin-bottom: 25px !important; }
    .mob-font-lg { font-size: 32px !important; }
    .mob-fac-item { display: inline-block !important; width: 50% !important; box-sizing: border-box !important; margin-bottom: 8px !important; }
  }
</style>
</head>
<body style="margin: 0; padding: 0;">
<div class="mob-pad" style="font-family: \'Outfit\', \'Inter\', Arial, sans-serif; background-color: #FDF8F4; padding: 40px 10px; color: #1a1a1a;">
  <div class="mob-inner-pad" style="max-width: 1150px; margin: auto; background-color: #ffffff; border-radius: 32px; overflow: hidden; box-shadow: 0 30px 80px rgba(0,0,0,0.06); padding: 45px;">
    
    <div style="margin-bottom: 30px; padding-bottom: 10px; border-bottom: 1px solid #F0F0F0;">
      <p style="font-size: 18px; font-weight: 500; margin: 0 0 10px 0; color: #1a1a1a;">Dear ' . $guestName . ',</p>
      <p style="font-size: 16px; line-height: 1.6; color: #444; margin: 0;">' . $email_greeting_body . '</p>
    </div>

    <table cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse;">
      <tr>
        <!-- Left Column (72%) -->
        <td class="mob-block mob-mb" style="width: 72%; vertical-align: top; padding-right: 45px;">
          
          <!-- Image Gallery Section -->
          <table cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
            <tr>
              <!-- Left Column: Tall Image -->
              <td class="mob-block mob-mb" style="width: 45%; vertical-align: top; padding-right: 15px;">
                <img src="' . $hero_img_1 . '" alt="Hero Image 1" class="mob-img" style="width: 100%; height: 420px; object-fit: cover; border-radius: 24px; display: block;">
              </td>
              <!-- Right Column -->
              <td class="mob-block" style="width: 55%; vertical-align: top;">
                <!-- Top Image -->
                <img src="' . $hero_img_2 . '" alt="Hero Image 2" class="mob-img" style="width: 100%; height: 202px; object-fit: cover; border-radius: 24px; margin-bottom: 15px; display: block;">
                
                <!-- Bottom Two Images -->
                <table cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                  <tr>
                    <td class="mob-block mob-mb" style="width: 50%; padding-right: 7.5px;">
                      <img src="' . $hero_img_3 . '" alt="Hero Image 3" class="mob-img" style="width: 100%; height: 203px; object-fit: cover; border-radius: 24px; display: block;">
                    </td>
                    <td class="mob-block" style="width: 50%; padding-left: 7.5px;">
                      <img src="' . $hero_img_4 . '" alt="Hero Image 4" class="mob-img" style="width: 100%; height: 203px; object-fit: cover; border-radius: 24px; display: block;">
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <h1 class="mob-font-lg" style="font-size: 42px; font-weight: 700; margin: 0 0 12px 0; color: #1a1a1a; letter-spacing: -0.5px;">' . $propName . '</h1>
          <p style="color: #888; font-size: 16px; margin: 0 0 30px 0;">' . ($property['address'] ?? '3891 Ranchview Dr. Richardson, California 62639') . '</p>
          
          <div style="margin-bottom: 35px;">
            <span style="display: inline-block; background: #F8F8F8; padding: 10px 22px; border-radius: 30px; font-size: 14px; margin-right: 15px; color: #B19470; font-weight: 600; margin-bottom: 5px;">' . ($booking['meal_plan'] ?? 'Breakfast') . '</span>
            ' . $special_note_tag . '
          </div>

          <div style="margin-bottom: 25px;">
              <div style="margin-bottom: 20px;">
                  <table cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                      <tr>
                          <td style="vertical-align: top;">
                              <h3 style="font-size: 20px; font-weight: 700; margin: 0 0 15px 0;">About Us</h3>
                              <p style="font-size: 16px; line-height: 1.7; color: #555; margin: 0;">
                                  “Experience Pangong with the Warmth of Home and the Elegance of Luxury.” Nestled in the tranquil Merak Village,
                                  right on the pristine shores of Pangong Lake, <strong>' . $propName . '</strong> offers an unparalleled blend of comfort,
                                  elegance, and Himalayan charm. Each spacious, centrally heated room is thoughtfully designed with large windows that frame sweeping views
                                  of the turquoise lake and snow-capped Mountains.
                              </p>
                          </td>
                      </tr>
                  </table>
              </div>
              ' . $special_remarks_html . '
              <!-- NEW MAP ROW -->
              <div style="margin-top: 25px; margin-bottom: 25px; border-radius: 4px;">
                  <a href="' . ($property['google_map_link'] ?? 'https://maps.app.goo.gl/EK2xgFzPVEw42FDe8') . '" target="_blank" style="display:block; border-radius:12px; overflow:hidden;">
                      <img src="' . $map_img . '" alt="Map Location" style="width: 100%; height: auto; max-height: 350px; object-fit: cover; border: 1px solid #E5E5E5; display:block;">
                  </a>
              </div>

              <!-- NEW: Facilities & Amenities -->
              <div style="background:#FDF8F4;border-radius:20px;padding:20px;margin-top:10px;">
                  <h3 style="font-size:16px;font-weight:700;margin:0 0 14px 0;color:#1a1a1a;">Facilities &amp; Amenities</h3>
                  <p style="margin:0 0 8px 0;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#B19470;">Facilities</p>
                  ' . $fac_html . '
                  <p style="margin:14px 0 8px 0;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#B19470;">In-Room Amenities</p>
                  ' . $amen_html . '
              </div>
              <!-- END NEW -->
          </div>
        </td>

        <!-- Right Column (28%): Sidebar Card -->
        <td class="mob-block" style="width: 28%; vertical-align: top;">
          <div style="border: 1px solid #F2F2F2; border-radius: 28px; padding: 30px; background: #ffffff; box-shadow: 0 15px 30px rgba(0,0,0,0.03);">
            <div style="margin-bottom: 30px;">
              <span style="font-size: 28px; font-weight: 700; color: #1a1a1a;">₹' . number_format($final_total_calc / max(1, (int)($booking['no_of_nights'] ?? 1)), 2) . '</span><span style="font-size: 15px; color: #888;">/Night</span>
              ' . $discount_badge . '
            </div>

            <table style="width: 100%; font-size: 15px; border-top: 1px solid #F8F8F8; padding-top: 25px; margin-bottom: 30px;">
              <tr><td style="padding-bottom: 14px; color: #777;">Check In</td><td style="text-align: right; font-weight: 700;">' . date('d M Y', strtotime($booking['check_in_date'])) . '</td></tr>
              <tr><td style="padding-bottom: 14px; color: #777;">Check Out</td><td style="text-align: right; font-weight: 700;">' . date('d M Y', strtotime($booking['check_out_date'])) . '</td></tr>
              <tr><td style="padding-bottom: 14px; color: #777;">Nights</td><td style="text-align: right; font-weight: 700;">' . intval($booking['no_of_nights'] ?? 1) . '</td></tr>
              <tr><td style="color: #777;">Total Guests</td><td style="text-align: right; font-weight: 700;">' . intval($total_pax_calc) . '</td></tr>
            </table>

            <div style="font-weight: 700; font-size: 15px; margin-bottom: 12px; color: #1a1a1a;">Addon Services</div>
            <table style="width: 100%; font-size: 14px; margin-bottom: 18px;">
              ' . $extra_services_html . '
            </table>

            <div style="background: #FDF8F4; padding: 22px; border-radius: 16px; margin-bottom: 30px;">
              <table style="width: 100%; font-size: 15px;">
                <tr><td style="padding-bottom: 12px; color: #777;">Total Amount</td><td style="text-align: right; font-weight: 700;">₹' . number_format($booking['total_amount'], 2) . '</td></tr>
                <tr><td style="padding-bottom: 12px; color: #777;">Addon Services Total</td><td style="text-align: right; font-weight: 700;">₹' . number_format($extra_services_total, 2) . '</td></tr>
                <tr><td style="padding-bottom: 12px; color: #777;">Token Paid</td><td style="text-align: right; font-weight: 700; color: #28a745;">-₹' . number_format($booking['booking_token'], 2) . '</td></tr>
                ' . $discount_display . '
                <tr style="font-size: 18px; border-top: 1px solid #EED;">
                  <td style="padding-top: 15px; font-weight: 700;">Final Total</td>
                  <td style="padding-top: 15px; text-align: right; font-weight: 700; color: #B19470;">₹' . number_format($final_total_calc, 2) . '</td>
                </tr>
                <tr style="font-size: 18px;">
                  <td style="padding-top: 15px; font-weight: 700;">Due Amount</td>
                  <td style="padding-top: 15px; text-align: right; font-weight: 700; color: #B19470;">₹' . number_format($due_amount_calc, 2) . '</td>
                </tr>
              </table>
            </div>

            <div style="font-weight: 700; font-size: 15px; margin-bottom: 18px;">Payment</div>
            <div style="margin-bottom: 30px; text-align: center;">
              <img src="' . $pay_visa . '" style="margin-right: 12px; height: 22px;"><img src="' . $pay_amex . '" style="margin-right: 12px; height: 22px;"><img src="' . $pay_stripe . '" style="margin-right: 12px; height: 22px;"><img src="' . $pay_google . '" style="margin-right: 12px; height: 22px;"><img src="' . $pay_master . '" style="height: 22px;">
            </div>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
              <tr>
                <td><a href="' . $payment_terms_url . '" target="_blank" style="display: block; background: #B19470; color: #fff; padding: 18px; border-radius: 12px; text-align: center; text-decoration: none; font-weight: 700; font-size: 18px; box-shadow: 0 8px 16px rgba(177, 148, 112, 0.2);">Pay Now →</a></td>
              </tr>
            </table>

            
            <p style="text-align: center; color: #AAA; font-size: 11px; margin: 0; line-height: 1.5;">By clicking "Pay Now" you agree to the <span style="text-decoration: underline;">Reservation</span> and <span style="text-decoration: underline;">Cancellation</span> policies.</p>
          </div>
        </td>
      </tr>
    </table>

    <div style="margin-top: 60px; padding-top: 35px; border-top: 1px solid #F8F8F8; text-align: center;">
      <p style="margin-bottom: 10px; font-size: 14px; color: #999;">Need assistance? Contact our luxury concierge at +91-9906991500</p>
      <p style="margin: 0; font-size: 14px; color: #999;">© ' . date('Y') . ' ' . $propName . ' . Experience the extraordinary.</p>
    </div>
  </div>
</div>
</body>
</html>';




if ($mail->send()) {
  $_SESSION['success'] = "Booking email sent successfully to guest and agent.";
}
else {
  $_SESSION['failure'] = "Email failed: " . $mail->ErrorInfo;
}

header("Location: property_booking_list.php");
exit;
