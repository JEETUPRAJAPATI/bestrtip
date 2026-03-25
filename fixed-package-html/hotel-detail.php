<?php
require_once('../config/config.php');
$db = getDbInstance();

// Get ID from URL or default to 1
$package_id = isset($_GET['id']) ? $_GET['id'] : 1;
$db->where('id', $package_id);
$package = $db->getOne("fixed_package");

if (!$package) {
    // Fallback if ID not found
    $package = $db->getOne("fixed_package");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Of Orglodi Vill By Mogul Khan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #B19470;
            --primary-light: #FFF9F2;
            --bg: #FDF8F4;
            --text-dark: #1a1a1a;
            --text-muted: #888;
            --border: #F2F2F2;
            --white: #ffffff;
        }

        body {
            font-family: 'Outfit', 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-dark);
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }

        .container {
            max-width: 1200px;
            width: 100%;
            background: var(--white);
            border-radius: 40px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.05);
            padding: 50px;
            box-sizing: border-box;
        }

        .main-layout {
            display: flex;
            gap: 50px;
        }

        .content-area {
            flex: 1;
        }

        .sidebar-area {
            width: 320px;
        }

        /* Gallery Grid */
        .gallery {
            display: grid;
            grid-template-columns: 2.2fr 1fr;
            gap: 20px;
            margin-bottom: 40px;
        }

        .hero-img {
            width: 100%;
            height: 480px;
            object-fit: cover;
            border-radius: 30px;
        }

        .sub-imgs {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sub-img {
            width: 100%;
            height: 230px;
            object-fit: cover;
            border-radius: 30px;
        }

        /* Header Info */
        h1 {
            font-size: 48px;
            font-weight: 700;
            margin: 0 0 10px 0;
            letter-spacing: -1px;
        }

        .address {
            color: var(--text-muted);
            font-size: 18px;
            margin-bottom: 35px;
        }

        .tags {
            display: flex;
            gap: 15px;
            margin-bottom: 40px;
        }

        .tag {
            padding: 12px 24px;
            border-radius: 40px;
            font-size: 15px;
            background: #F8F8F8;
            color: #666;
        }

        .tag.active {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }

        /* Hotel Features */
        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 25px 0;
        }

        .features {
            display: flex;
            gap: 30px;
            margin-bottom: 50px;
            flex-wrap: wrap;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            color: #555;
        }

        .feature-item img {
            width: 20px;
            height: 20px;
        }

        /* Interactive Tabs */
        .tabs {
            border-bottom: 2px solid #F5F5F5;
            display: flex;
            gap: 40px;
            margin-bottom: 40px;
        }

        .tab-btn {
            padding: 0 5px 15px 5px;
            cursor: pointer;
            font-size: 16px;
            color: var(--text-muted);
            font-weight: 500;
            position: relative;
            background: none;
            border: none;
            font-family: inherit;
        }

        .tab-btn.active {
            color: var(--primary);
            font-weight: 700;
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary);
        }

        .tab-content {
            display: none;
            line-height: 1.8;
            color: #555;
            font-size: 17px;
        }

        .tab-content.active {
            display: block;
        }

        .description-row {
            display: flex;
            gap: 30px;
        }

        .description-text {
            flex: 1;
        }

        .map-box {
            width: 280px;
            height: 200px;
            border-radius: 30px;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .map-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Sidebar Styling */
        .sidebar {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 35px;
            padding: 35px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.02);
            position: sticky;
            top: 40px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .price {
            font-size: 32px;
            font-weight: 700;
        }

        .unit {
            font-size: 16px;
            color: var(--text-muted);
            font-weight: 400;
        }

        .discount-badge {
            background: var(--primary);
            color: var(--white);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 700;
        }

        .info-grid {
            border-top: 1px solid #F9F9F9;
            padding-top: 30px;
            margin-bottom: 35px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
            font-size: 16px;
        }

        .info-label {
            color: #777;
        }

        .info-value {
            font-weight: 700;
        }

        .summary-card {
            background: var(--bg);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 35px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .summary-row.total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #EEE;
            font-size: 22px;
            font-weight: 700;
        }

        .token-paid {
            color: #28a745;
        }

        .payment-logos {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 35px;
        }

        .payment-logos img {
            height: 24px;
        }

        .action-row {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .promo-input {
            flex: 1;
            border: 1px solid var(--border);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #AAA;
            font-size: 14px;
            background: var(--white);
        }

        .pay-btn {
            flex: 1;
            background: var(--primary);
            color: var(--white);
            padding: 16px;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 10px 20px rgba(177, 148, 112, 0.2);
            transition: transform 0.2s;
        }

        .pay-btn:hover {
            transform: translateY(-2px);
        }

        .policies {
            text-align: center;
            font-size: 12px;
            color: #AAA;
            line-height: 1.6;
            margin: 0;
        }

        .policies span {
            text-decoration: underline;
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 1000px) {
            .main-layout {
                flex-direction: column;
            }
            .sidebar-area {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="main-layout">
        <!-- Main Content -->
        <div class="content-area">
            <!-- Gallery -->
            <div class="gallery">
                <img src="https://agent.go2ladakh.in/<?php echo $package['image'] ?>" alt="Hero" class="hero-img">
                <div class="sub-imgs">
                    <img src="https://images.unsplash.com/photo-1540518614846-7eded433c457?q=80&w=600" alt="Sub 1" class="sub-img">
                    <img src="https://images.unsplash.com/photo-1584132967334-10e028bd69f7?q=80&w=600" alt="Sub 2" class="sub-img">
                </div>
            </div>

            <!-- Title Section -->
            <h1><?php echo $package['name'] ?></h1>
            <p class="address"><?php echo $package['name'] ?> - Specialized Tour Package</p>


            <div class="tags">
                <span class="tag">Minimalist</span>
                <span class="tag active">Beach House</span>
                <span class="tag">Tropic</span>
                <span class="tag">Private Pool</span>
            </div>

            <!-- Features -->
            <h3 class="section-title">Hotel Features</h3>
            <div class="features">
                <div class="feature-item"><img src="https://img.icons8.com/material-rounded/32/B19470/wifi.png"> Wifi</div>
                <div class="feature-item"><img src="https://img.icons8.com/material-rounded/32/B19470/bed.png"> King Bed</div>
                <div class="feature-item"><img src="https://img.icons8.com/material-rounded/32/B19470/bath.png"> Bathup</div>
                <div class="feature-item"><img src="https://img.icons8.com/material-rounded/32/B19470/restaurant.png"> Breakfast</div>
                <div class="feature-item"><img src="https://img.icons8.com/material-rounded/32/B19470/resize-diagonal.png"> 4m 6m</div>
            </div>


            <!-- Tabs Section -->
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab(this, 'description')">Description</button>
                <button class="tab-btn" onclick="showTab(this, 'feature')">Feature</button>
                <button class="tab-btn" onclick="showTab(this, 'virtual')">Virtual</button>
                <button class="tab-btn" onclick="showTab(this, 'reviews')">Reviews</button>
            </div>


            <div id="description" class="tab-content active">
                <div class="description-row">
                    <div class="description-text">
                        <h3 class="section-title">Overview</h3>
                        <p><?php echo $package['description'] ?></p>
                    </div>
                </div>
            </div>

            <div id="feature" class="tab-content">
                <h3 class="section-title">Amenities & Offerings</h3>
                <div style="column-count: 2; column-gap: 30px;">
                    <?php echo $package['add_inclusions'] ? $package['add_inclusions'] : 'Premium amenities included in this package.' ?>
                </div>
            </div>


            <div id="virtual" class="tab-content">
                <h3 class="section-title">Virtual Tour</h3>
                <p>Experience the villa in 360 degrees. Coming soon...</p>
            </div>

            <div id="reviews" class="tab-content">
                <h3 class="section-title">User Reviews</h3>
                <p>See what our guests have to say about their stay.</p>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar-area">
            <div class="sidebar">
                <div class="price-row">
                    <div>
                        <span class="price">₹31,200.00</span><span class="unit">/Night</span>
                    </div>
                    <span class="discount-badge">10% Off</span>
                </div>

                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">Check In</span>
                        <span class="info-value">2026-05-05</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Check Out</span>
                        <span class="info-value">2026-05-06</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Guests</span>
                        <span class="info-value">02/01</span>
                    </div>
                </div>

                <div class="section-title" style="font-size: 18px; margin-bottom: 20px;">Extra Service</div>
                <div class="info-row">
                    <span class="info-label">Room Clean</span>
                    <span class="info-value">₹500/Night</span>
                </div>
                <div class="info-row" style="margin-bottom: 35px;">
                    <span class="info-label">Breakfast</span>
                    <span class="info-value">₹0/Free</span>
                </div>

                <div class="summary-card">
                    <div class="summary-row">
                        <span class="info-label">Package Price</span>
                        <span class="info-value">₹<?php echo $package['dbl_twin'] ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="info-label">Token Paid</span>
                        <span class="info-value token-paid">-₹<?php echo round($package['dbl_twin'] * 0.1) ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="info-label">Status</span>
                        <span class="info-value">10% Deposit</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total Cost</span>
                        <span>₹<?php echo $package['dbl_twin'] ?></span>
                    </div>
                </div>


                <div class="section-title" style="font-size: 18px; margin-bottom: 20px;">Payment</div>
                <div class="payment-logos">
                    <img src="https://img.icons8.com/color/48/000000/visa.png">
                    <img src="https://img.icons8.com/color/48/000000/amex.png">
                    <img src="https://img.icons8.com/color/48/000000/stripe.png">
                    <img src="https://img.icons8.com/color/48/000000/google-pay.png">
                    <img src="https://img.icons8.com/color/48/000000/mastercard.png">
                </div>

                <div class="action-row">
                    <div class="promo-input">Promo Code</div>
                    <a href="https://wa.me/919512087057" class="pay-btn">Pay Now →</a>
                </div>

                <p class="policies">
                    By clicking "Pay Now" you agree to the <span>Reservation</span> and <span>Cancellation</span> policies.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    function showTab(btn, tabId) {
        // Hide all content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        // Deactivate all buttons
        document.querySelectorAll('.tab-btn').forEach(btnEl => {
            btnEl.classList.remove('active');
        });
        
        // Show specific content
        document.getElementById(tabId).classList.add('active');
        // Activate specific button
        btn.classList.add('active');
    }

</script>

</body>
</html>
