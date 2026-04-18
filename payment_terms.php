<?php
session_start();
require_once 'config/config.php';

$booking = null;
$bookingRef = '';
$guestName = 'Guest';

if (!empty($_GET['crm'])) {
    $bookingId = decryptId($_GET['crm']);
    if (!empty($bookingId) && ctype_digit((string)$bookingId)) {
        $db = getDbInstance();
        $db->where('id', (int)$bookingId);
        $booking = $db->getOne('property_booking', ['id', 'booking_id', 'guest_name', 'check_in_date', 'check_out_date', 'booking_token']);
        if ($booking) {
            $bookingRef = $booking['booking_id'] ?? '';
            $guestName = $booking['guest_name'] ?? 'Guest';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Terms - Saser Scenic Pangong</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f4ee;
            color: #2a2a2a;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .wrap {
            max-width: 900px;
            margin: 32px auto;
            padding: 0 14px;
        }
        .card-box {
            border: 1px solid #eadfce;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.05);
        }
        .head {
            background: linear-gradient(135deg, #b19470 0%, #8f775a 100%);
            color: #fff;
            border-radius: 16px 16px 0 0;
            padding: 18px 22px;
        }
        .terms-text {
            white-space: pre-line;
            line-height: 1.72;
            font-size: 15px;
        }
        .pay-section {
            display: none;
            border-top: 1px solid #eee2d2;
            margin-top: 18px;
            padding-top: 20px;
        }
        .qr-box {
            text-align: center;
            border: 1px dashed #ccb18f;
            border-radius: 14px;
            padding: 16px;
            background: #fffdf9;
        }
        .qr-box img {
            width: 240px;
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            border: 1px solid #e6d8c4;
        }
        .bank-box {
            background: #f9f5ef;
            border-radius: 12px;
            padding: 18px;
            border: 1px solid #eadfce;
        }
        .lbl {
            color: #77624a;
            min-width: 160px;
            display: inline-block;
            font-weight: 600;
        }
        .confirm-wrap {
            margin-top: 16px;
            text-align: center;
        }
        .btn-paid {
            background: #198754;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
        }
        .btn-paid:hover {
            background: #157347;
            color: #fff;
        }
        .thank-you-box {
            display: none;
            margin-top: 18px;
            border: 1px solid #cde7d7;
            background: #f5fff9;
            border-radius: 12px;
            padding: 16px;
        }
        .thank-you-fullscreen {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(248, 244, 238, 0.98);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .thank-you-card {
            max-width: 760px;
            width: 100%;
            border: 1px solid #cde7d7;
            background: #f5fff9;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }
        .thank-actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn-back {
            background: #6c757d;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 16px;
            font-weight: 600;
        }
        .btn-invoice {
            background: #b19470;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card-box">
        <div class="head">
            <h4 class="mb-1">Terms & Conditions</h4>
            <div class="small">Saser Scenic Pangong</div>
        </div>

        <div class="p-4">
            <div class="mb-3">
                <div><strong>Dear <?= htmlspecialchars($guestName) ?>,</strong></div>
                <?php if (!empty($bookingRef)): ?>
                    <div class="text-muted">Booking Ref: <?= htmlspecialchars($bookingRef) ?></div>
                <?php endif; ?>
            </div>

            <div class="terms-text">
Saser Scenic Pangong is a pure vegetarian retreat, thoughtfully designed to offer a peaceful and comfortable stay amidst the breathtaking beauty of Pangong. Non-vegetarian meals may also be arranged on special request at the time of booking.

Guests can enjoy geyser-based hot water shower facility, with up to 25 litres of hot water provided. While we always try our best to offer hot water 24 hours, owing to the extreme weather conditions in the region, morning hot shower availability may remain limited until the end of April. Hot shower service is generally available till 12:00 AM. Whenever required, our team will be happy to arrange an alternative hot water bucket service for your comfort.

The property is equipped with 24-hour emergency power backup, along with central dual AC heating from 4:00 PM to 9:30 AM, and electric blankets in all rooms to ensure a warm and pleasant stay.

Please note that stargazing experiences are subject to weather conditions and clear sky visibility.

The booking amount is non-refundable for cancellations made within 15 days prior to the check-in date.

For cancellations due to medical reasons, a valid doctor's prescription or medical report issued by Leh Hospital must be provided for review.

Guests are requested to kindly contact our property staff, Mr. Sammer, at least 1 hour prior to arrival, so that the room can be prepared and kept ready for check-in.

Acceptance Note
I hereby confirm that I have read, understood, and accepted the above terms and conditions, and I confirm my booking by paying the token amount.
            </div>

            <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" id="accept_terms">
                <label class="form-check-label fw-semibold" for="accept_terms">
                    I have read and accept Terms & Conditions
                </label>
            </div>

            <div id="pay_section" class="pay-section">
                <h5 class="mb-3">Booking / Payment Details</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="bank-box h-100">
                            <p class="mb-2"><strong>Dear Guest,</strong></p>
                            <p class="mb-3">Please find our bank account details below for your kind reference:</p>

                            <div class="mb-2"><span class="lbl">Account Name:</span> Go 2 LADAKH</div>
                            <div class="mb-2"><span class="lbl">Bank Name:</span> Jammu & Kashmir Bank</div>
                            <div class="mb-2"><span class="lbl">Account Number:</span> 0069020100000599</div>
                            <div class="mb-2"><span class="lbl">IFSC Code:</span> JAKA0PRIEST</div>

                            <p class="mt-3 mb-0">Please feel free to contact us for any further assistance.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="qr-box h-100">
                            <div class="fw-semibold mb-2">Scan QR Code</div>
                            <img src="assets/img/email/qrcode.jpeg" alt="Payment QR Code">
                        </div>
                    </div>
                </div>

                <div class="confirm-wrap">
                    <button type="button" id="confirm_paid_btn" class="btn-paid">I Have Paid</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="thank_you_fullscreen" class="thank-you-fullscreen">
    <div class="thank-you-card">
        <h3 class="mb-2 text-success">Thank you for your payment.</h3>
        <p class="mb-2">We have received your payment confirmation.</p>
        <?php if (!empty($bookingRef)): ?>
            <p class="mb-1"><strong>Booking Ref:</strong> <?= htmlspecialchars($bookingRef) ?></p>
        <?php endif; ?>
        <?php if (!empty($booking['booking_token'])): ?>
            <p class="mb-1"><strong>Token Amount:</strong> ₹<?= number_format((float)$booking['booking_token'], 2) ?></p>
        <?php endif; ?>
        <?php if (!empty($booking['check_in_date']) || !empty($booking['check_out_date'])): ?>
            <p class="mb-1"><strong>Stay Dates:</strong>
                <?= !empty($booking['check_in_date']) ? date('d M Y', strtotime($booking['check_in_date'])) : '-' ?>
                to
                <?= !empty($booking['check_out_date']) ? date('d M Y', strtotime($booking['check_out_date'])) : '-' ?>
            </p>
        <?php endif; ?>
        <p class="mb-0 text-muted">Our team will verify and confirm your booking shortly.</p>

        <div class="thank-actions">
            <button type="button" id="thank_back_btn" class="btn-back">Back</button>
            <?php if (!empty($bookingRef)): ?>
                <a class="btn-invoice" href="download_property_booking_invoice.php?crm=<?= urlencode($_GET['crm'] ?? '') ?>">Download Invoice (PDF)</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const checkbox = document.getElementById('accept_terms');
    const paySection = document.getElementById('pay_section');
    const paidBtn = document.getElementById('confirm_paid_btn');
    const thankYouFullscreen = document.getElementById('thank_you_fullscreen');
    const thankBackBtn = document.getElementById('thank_back_btn');

    checkbox.addEventListener('change', function () {
        paySection.style.display = this.checked ? 'block' : 'none';
    });

    paidBtn.addEventListener('click', function () {
        thankYouFullscreen.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    });

    if (thankBackBtn) {
        thankBackBtn.addEventListener('click', function () {
            thankYouFullscreen.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    }
</script>
</body>
</html>
