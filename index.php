<?php

require_once './config/config.php'; // Include database connection file if needed
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); // Ensure session is started
$db = getDbInstance();

$isLoggedIn = !empty($_SESSION['user_id']);

$data = [];
if ($isLoggedIn) {
    $db->where('id', $_SESSION['user_id']);
    $data = $db->getOne("agents");
}

$db->where('p.status', 'Active');
$db->where('p.custom_package', '1');
$db->where("(p.agents IS NULL OR p.agents = '')");
$db->join("overview_section_images i", "i.package_id = p.id", "LEFT");
$db->join("destination d", "d.id = p.destination", "LEFT"); // Join with destinations table
$db->groupBy("p.id"); // Ensure only one row per package
$packages_with_images = $db->get("packages p", null, "p.*, d.name AS destination_name, IFNULL(GROUP_CONCAT(i.image_path), '') as images");

// Process images into an array
foreach ($packages_with_images as &$package) {
    $package['images'] = explode(',', $package['images'] ?: '');
}

unset($package); // Avoid reference issues
$destinations = $db->get("destination", null, 'id, name');
$traveling_from = $db->get("traveling_from", null, 'id, name');


$db->where('status', 'active');
$carList = $db->get("carlist", null, 'id, name,cover_image');
// Debugging output
// echo "<pre>";
// print_r($carList);
// echo "</pre>";
// die();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Home Page</title>
    <link rel="profile" href="https://gmpg.org/xfn/11" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Platypi:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link href="assets/css/home.min.css" rel="stylesheet" />
    <link href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css' rel='stylesheet'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.js"></script>
</head>
<style>
    .dynamic-form {
        display: none;
    }

    .dynamic-form.active {
        display: flex;
    }

    @media screen and (max-width: 1024px) {
        .dynamic-form.active {
            display: block;
        }
    }

    .form-container {
        margin-top: 20px;
    }

    .single {
        margin-bottom: 10px;
    }

    .custom-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999;
    }

    .custom-modal {
        background: #ffffff;
        border-radius: 10px;
        width: 80%;
        max-width: 800px;
        padding: 20px;
        box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
        animation: fadeIn 0.3s ease-in-out;
        position: relative;
        max-height: 80vh;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        margin: auto;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* Modal Header */
    .custom-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ccc;
        padding-bottom: 10px;
    }

    /* Title */
    .custom-modal-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #333;
    }

    /* Close button */
    .custom-modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #555;
    }

    /* Modal Body */
    .custom-modal-body {
        margin: 20px 0;
        max-height: 400px;
        /* Adjusted height */
        overflow-y: auto;
    }

    /* Table styling */
    .table {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }

    /* Modal Footer */
    .custom-modal-footer {
        text-align: right;
        margin-top: 20px;
    }

    /* Footer button */
    .custom-modal-footer button {
        border-radius: 20px;
        background-color: #6c757d;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
    }

    /* Footer button hover effect */
    .custom-modal-footer button:hover {
        background-color: #5a6268;
    }

    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .profile-icon {
        width: 30px;
        height: 30px;
        border-radius: 50px;
        background-color: #000;
        display: block;
        overflow: hidden;
    }

    .profile-icon img {
        width: 30px;
        height: 30px;
        object-fit: cover;
    }



    .text-white {
        color: #fff !important;
    }

    .car-container {
        display: flex;
        flex-wrap: nowrap;
        /* Prevent wrapping */
        overflow-x: auto;
        /* Enable horizontal scrolling if needed */
        gap: 20px;
        justify-content: flex-start;
        /* Align items from the left */
        padding-bottom: 10px;
    }

    .car_card {
        position: relative;
        width: 350px;
        /* Increase width */
        height: 300px;
        /* Increase height */
        overflow: hidden;
        border-radius: 10px;
        text-align: center;
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.125);
        transition: 0.3s ease-in-out;
    }

    .card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
    }

    .card-img-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        top: 189px;
        width: 100%;
        padding: 10px 0;
        text-align: center;
    }

    .card-title {
        font-size: 16px;
        margin-bottom: 5px;
        color: white;
    }

    .card-text {
        font-size: 12px;
        /* Increased font size */
        font-weight: bold;
        color: white;
    }


    /* car style */
</style>

<body>
    <div class="container">
        <!-- Top Band -->
        <div class="top-panel">
            <div class="block">
                <div class="left-panel">
                    <div class="icon"></div>
                </div>
                <div class="right-panel">
                    <ul>
                        <li><a href="#">List Your Property</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Create Free Account</a></li>
                        <li><a href="driver/driver_register.php">Driver Register</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Navigation header -->
        <div class="header">
            <div class="block">
                <header>
                    <div class="nav-toggle-btn">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" fill="#3E1E66" />
                        </svg>
                    </div>
                    <div class="logo">
                        <!-- <a href="#"><img src="assets/images/logo.png" alt="logo" style="height: 200px;" /></a> -->
                    </div>
                    <div class="nav" id="toggle-nav">
                        <nav>

                            <ul>
                                <li><a href="#"><span class="nav-icon"><img src="/assets/images/nav-item-icon-1.svg" /></span><span>Travel</span></a></li>
                                <li><a href="#"><span class="nav-icon"><img src="/assets/images/nav-item-icon-1.svg" /></span><span>Hotel</span></a></li>
                                <li><a href="#"><span class="nav-icon"><img src="/assets/images/nav-item-icon-1.svg" /></span><span>Tour</span></a></li>
                                <li><a href="#"><span class="nav-icon"><img src="/assets/images/nav-item-icon-1.svg" /></span><span>Blog</span></a></li>

                                <?php if (!$isLoggedIn): ?>
                                    <li class="login-btn"><a href="login.php"><span class="nav-icon"><img src="/assets/images/login-icon.svg" /></span><span>Login</span></a></li>
                                <?php else: ?>
                                    <li><a href="logout.php">Logout</a></li>
                                    <a class="profile-icon" href="profile.php">
                                        <?php if (!empty($data['logo'])): ?>
                                            <img width="200px" src="<?= BASE_URL . $data['logo'] ?>" alt="Profile" />
                                        <?php else: ?>
                                            <img src="assets/img/avatars/1.png" alt="Profile" />
                                        <?php endif; ?>
                                    </a>
                                <?php endif; ?>
                            </ul>

                        </nav>
                    </div>
                    <div class="nav-login-btn">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 6a3.75 3.75 0 1 0 0 7.5A3.75 3.75 0 0 0 12 6zm0 6a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5z" fill="#3E1E66" />
                            <path d="M12 1.5A10.5 10.5 0 1 0 22.5 12 10.512 10.512 0 0 0 12 1.5zM7.5 19.783V18.75a2.252 2.252 0 0 1 2.25-2.25h4.5a2.252 2.252 0 0 1 2.25 2.25v1.033a8.924 8.924 0 0 1-9 0zm10.494-1.088A3.751 3.751 0 0 0 14.25 15h-4.5a3.752 3.752 0 0 0-3.744 3.695 9 9 0 1 1 11.989 0h-.001z" fill="#3E1E66" />
                        </svg>
                    </div>
                </header>
            </div>
        </div>

        <!-- Home Page Banner -->
        <div class="main-banner-section">
            <div class="block">
                <div class="left-part">
                    <div class="header-tag">Book with us</div>
                    <h1>Find Next Place To <span>Ladakh</span></h1>
                    <p>Discover amzaing places at exclusive deals. Eat, Shop, Visit interesting places around the world. Discover amzaing places at exclusive deals. Eat, Shop, Visit interesting places around the world.</p>
                    <!-- Mega Search Module-->
                    <div class="mega-search">

                        <div class="group-radio-selection">
                            <div class="select-tour selected">
                                <input type="radio" name="tour-type" id="group-tour" value="Group Tour" checked />
                                <label for="group-tour">Group Tour</label>
                            </div>
                            <div class="select-tour">
                                <input type="radio" name="tour-type" id="customize" value="Customize Journey" />
                                <label for="customize">Customize Journey</label>
                            </div>
                            <div class="select-tour">
                                <input type="radio" name="tour-type" id="taxi" value="Taxi" />
                                <label for="taxi">Hire Taxi</label>
                            </div>
                            <div class="select-tour">
                                <input type="radio" name="tour-type" id="airport" value="airport" />
                                <label for="airport">Airport/City Transfer</label>
                            </div>
                            <!-- <div class="select-tour">
                                <input type="radio" name="tour-type" id="hotels" value="Hotels" />
                                <label for="hotels">Hotels</label>
                            </div>
                            <div class="select-tour">
                                <input type="radio" name="tour-type" id="holiday-package" value="Holiday Package" />
                                <label for="holiday-package">Holiday Package</label>
                            </div>
                            <div class="select-tour">
                                <input type="radio" name="tour-type" id="taxi" value="Taxi" />
                                <label for="taxi">Taxi</label>
                            </div>
                            <div class="select-tour">
                                <input type="radio" name="tour-type" id="adventure" value="Adventure" />
                                <label for="adventure">Adventure</label>
                            </div> -->
                        </div>

                        <!-- Forms -->
                        <div class="group-tour-form">
                            <!-- Group Tour Form -->
                            <div class="dynamic-form" id="form-group-tour">
                                <div class="single tour-type">
                                    <select>
                                        <option>Tour Type</option>
                                        <option>Bike Tour</option>
                                        <option>Road Tour</option>
                                    </select>
                                </div>
                                <div class="single traveling-from">
                                    <select>
                                        <option>Travelling From</option>
                                        <option>Delhi</option>
                                        <option>Mumbai</option>
                                    </select>
                                </div>
                                <div class="single select-destination">
                                    <select>
                                        <option>Destinations</option>
                                        <option>Leh</option>
                                        <option>Ladakh</option>
                                    </select>
                                </div>
                                <div class="single calendar">
                                    <input type="date" id="calendar">
                                </div>
                                <div class="single">
                                    <button class="search">Search</button>
                                </div>
                            </div>

                            <!-- Customize Journey Form -->
                            <!-- Customize Journey Form -->
                            <div class="dynamic-form" id="form-customize">

                                <div class="input-group single tour-type">
                                    <select class="form-select" name="duration" id="durationDropdown" required="">
                                        <option value="" disabled="" selected="">Choose...</option>
                                        <option value="1 Nights 2 Days">1 Nights 2 Days</option>
                                        <option value="2 Nights 3 Days">2 Nights 3 Days</option>
                                        <option value="3 Nights 4 Days">3 Nights 4 Days</option>
                                        <option value="4 Nights 5 Days">4 Nights 5 Days</option>
                                        <option value="5 Nights 6 Days">5 Nights 6 Days</option>
                                        <option value="6 Nights 7 Days">6 Nights 7 Days</option>
                                        <option value="7 Nights 8 Days">7 Nights 8 Days</option>
                                        <option value="8 Nights 9 Days">8 Nights 9 Days</option>
                                        <option value="9 Nights 10 Days">9 Nights 10 Days</option>
                                        <option value="10 Nights 11 Days">10 Nights 11 Days</option>
                                        <option value="11 Nights 12 Days">11 Nights 12 Days</option>
                                        <option value="12 Nights 13 Days">12 Nights 13 Days</option>
                                        <option value="13 Nights 14 Days">13 Nights 14 Days</option>
                                        <option value="14 Nights 15 Days">14 Nights 15 Days</option>
                                        <option value="15 Nights 16 Days">15 Nights 16 Days</option>
                                        <option value="16 Nights 17 Days">16 Nights 17 Days</option>
                                        <option value="17 Nights 18 Days">17 Nights 18 Days</option>
                                        <option value=">18 Nights 19 Days">18 Nights 19 Days</option>
                                        <option value="19 Nights 20 Days">19 Nights 20 Days</option>
                                    </select>
                                </div>


                                <div class="single traveling-from">
                                    <select id="traveling_from" name="traveling_from">
                                        <option value="">Traveling From</option>
                                        <?php foreach ($traveling_from as $location): ?>
                                            <option value="<?= $location['id']; ?>">
                                                <?= htmlspecialchars($location['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="single select-destination">
                                    <select id="destination" name="destination">
                                        <option value="">Destination</option>
                                        <?php foreach ($destinations as $destination): ?>
                                            <option value="<?= $destination['id']; ?>">
                                                <?= htmlspecialchars($destination['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="single calendar">
                                    <input type="date" id="travel_date">
                                </div>

                                <div class="single">
                                    <button type="button" class="search" id="searchBtn">Search</button>
                                </div>
                            </div>

                            <div class="dynamic-form" id="form-taxi">

                                <div class="input-group single tour-type">
                                    <select class="form-select" name="duration" id="taxiDuration" required="">
                                        <option value="" disabled="" selected="">Choose...</option>
                                        <option value="1 Nights 2 Days">1 Nights 2 Days</option>
                                        <option value="2 Nights 3 Days">2 Nights 3 Days</option>
                                        <option value="3 Nights 4 Days">3 Nights 4 Days</option>
                                        <option value="4 Nights 5 Days">4 Nights 5 Days</option>
                                        <option value="5 Nights 6 Days">5 Nights 6 Days</option>
                                        <option value="6 Nights 7 Days">6 Nights 7 Days</option>
                                        <option value="7 Nights 8 Days">7 Nights 8 Days</option>
                                        <option value="8 Nights 9 Days">8 Nights 9 Days</option>
                                        <option value="9 Nights 10 Days">9 Nights 10 Days</option>
                                        <option value="10 Nights 11 Days">10 Nights 11 Days</option>
                                        <option value="11 Nights 12 Days">11 Nights 12 Days</option>
                                        <option value="12 Nights 13 Days">12 Nights 13 Days</option>
                                        <option value="13 Nights 14 Days">13 Nights 14 Days</option>
                                        <option value="14 Nights 15 Days">14 Nights 15 Days</option>
                                        <option value="15 Nights 16 Days">15 Nights 16 Days</option>
                                        <option value="16 Nights 17 Days">16 Nights 17 Days</option>
                                        <option value="17 Nights 18 Days">17 Nights 18 Days</option>
                                        <option value=">18 Nights 19 Days">18 Nights 19 Days</option>
                                        <option value="19 Nights 20 Days">19 Nights 20 Days</option>
                                    </select>
                                </div>
                                <div class="single select-destination">
                                    <select id="taxi_destination" name="destination">
                                        <option value="">Destination</option>
                                        <?php foreach ($destinations as $destination): ?>
                                            <option value="<?= $destination['id']; ?>">
                                                <?= htmlspecialchars($destination['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="single select-pickup">
                                    <select id="pickup" name="pickup">
                                        <option value="">Pickup</option>
                                        <option value="hotel">Hotel</option>
                                        <option value="airport">Airport</option>
                                    </select>
                                </div>

                                <div class="single calendar">
                                    <input type="datetime-local" id="taxi_date_time" style="width: 100%;
    border: none;
    outline: none;
    border-bottom: solid 1px #f0f0f0;
    padding-bottom: 15px;">
                                </div>

                                <div class="single">
                                    <button type="button" class="search" id="taxiSearchBtn">Search</button>
                                </div>
                            </div>


                            <div class="dynamic-form" id="form-airport">

                                <div class="input-group single tour-type">
                                    <select class="form-select" name="duration" id="taxiDuration" required="">
                                        <option value="" disabled="" selected="">Choose...</option>
                                        <option value="1 Nights 2 Days">1 Nights 2 Days</option>
                                        <option value="2 Nights 3 Days">2 Nights 3 Days</option>
                                        <option value="3 Nights 4 Days">3 Nights 4 Days</option>
                                        <option value="4 Nights 5 Days">4 Nights 5 Days</option>
                                        <option value="5 Nights 6 Days">5 Nights 6 Days</option>
                                        <option value="6 Nights 7 Days">6 Nights 7 Days</option>
                                        <option value="7 Nights 8 Days">7 Nights 8 Days</option>
                                        <option value="8 Nights 9 Days">8 Nights 9 Days</option>
                                        <option value="9 Nights 10 Days">9 Nights 10 Days</option>
                                        <option value="10 Nights 11 Days">10 Nights 11 Days</option>
                                        <option value="11 Nights 12 Days">11 Nights 12 Days</option>
                                        <option value="12 Nights 13 Days">12 Nights 13 Days</option>
                                        <option value="13 Nights 14 Days">13 Nights 14 Days</option>
                                        <option value="14 Nights 15 Days">14 Nights 15 Days</option>
                                        <option value="15 Nights 16 Days">15 Nights 16 Days</option>
                                        <option value="16 Nights 17 Days">16 Nights 17 Days</option>
                                        <option value="17 Nights 18 Days">17 Nights 18 Days</option>
                                        <option value=">18 Nights 19 Days">18 Nights 19 Days</option>
                                        <option value="19 Nights 20 Days">19 Nights 20 Days</option>
                                    </select>
                                </div>
                                <div class="single select-destination">
                                    <select id="taxi_destination" name="destination">
                                        <option value="">Destination</option>
                                        <?php foreach ($destinations as $destination): ?>
                                            <option value="<?= $destination['id']; ?>">
                                                <?= htmlspecialchars($destination['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="single select-pickup">
                                    <select id="pickup" name="pickup">
                                        <option value="">Pickup</option>
                                        <option value="hotel">Hotel</option>
                                        <option value="airport">Airport</option>
                                    </select>
                                </div>

                                <div class="single calendar">
                                    <input type="datetime-local" id="taxi_date_time" style="width: 100%;
    border: none;
    outline: none;
    border-bottom: solid 1px #f0f0f0;
    padding-bottom: 15px;">
                                </div>

                                <div class="single">
                                    <button type="button" class="search" id="taxiSearchBtn">Search</button>
                                </div>
                            </div>

                            <!-- Modal for selecting package -->
                            <!-- Modal for selecting package -->
                            <div class="custom-modal-overlay" id="customModalOverlay" style="display: none;">
                                <div class="custom-modal" id="customModal">
                                    <div class="custom-modal-header">
                                        <h5 class="custom-modal-title">Select a Package</h5>
                                        <button type="button" class="custom-modal-close" id="closeCustomModal">&times;</button>
                                    </div>
                                    <div class="custom-modal-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th class="text-white">Select Package</th>
                                                        <th class="text-white">Package Code</th>
                                                        <th class="text-white">Package Name</th>
                                                        <th class="text-white">Duration</th>
                                                        <th class="text-white">Hotel Category</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="package_list">
                                                    <!-- Dynamic rows will be added here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="custom-modal-footer">
                                        <!-- New submit button added -->
                                        <button type="button" class="btn btn-primary" id="submitPackageSelection">Select Package</button>
                                        <button type="button" class="btn btn-secondary" id="closeCustomModalFooter">Close</button>
                                    </div>
                                </div>
                            </div>




                            <!-- Hotels Form -->
                            <div class="hotels-form dynamic-form hidden">
                                <div class="single">
                                    <select>
                                        <option>Room Type</option>
                                        <option>Single</option>
                                        <option>Double</option>
                                    </select>
                                </div>
                                <div class="single">
                                    <select>
                                        <option>Hotel Location</option>
                                        <option>Beach</option>
                                        <option>City Center</option>
                                    </select>
                                </div>
                                <div class="single">
                                    <button class="search">Find Hotels</button>
                                </div>
                            </div>

                            <!-- Holiday Package Form -->
                            <div class="holiday-package-form dynamic-form hidden">
                                <div class="single">
                                    <select>
                                        <option>Package Type</option>
                                        <option>Standard</option>
                                        <option>Luxury</option>
                                    </select>
                                </div>
                                <div class="single">
                                    <input type="text" placeholder="Additional Preferences" />
                                </div>
                                <div class="single">
                                    <button class="search">Browse Packages</button>
                                </div>
                            </div>

                            <!-- Taxi Form -->
                            <div class="taxi-form dynamic-form hidden">
                                <div class="single">
                                    <select>
                                        <option>Taxi Type</option>
                                        <option>Economy</option>
                                        <option>Luxury</option>
                                    </select>
                                </div>
                                <div class="single">
                                    <input type="text" placeholder="Pickup Location" />
                                </div>
                                <div class="single">
                                    <button class="search">Book Taxi</button>
                                </div>
                            </div>

                            <!-- Adventure Form -->
                            <div class="adventure-form dynamic-form hidden">
                                <div class="single">
                                    <select>
                                        <option>Adventure Type</option>
                                        <option>Hiking</option>
                                        <option>Rafting</option>
                                    </select>
                                </div>
                                <div class="single">
                                    <select>
                                        <option>Difficulty Level</option>
                                        <option>Easy</option>
                                        <option>Hard</option>
                                    </select>
                                </div>
                                <div class="single">
                                    <button class="search">Explore Adventures</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="right-part">
                    <div class="slider">
                        <div class="slide">
                            <figure>
                                <img src="./assets/images/cards.png" alt="banner" />
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Freshly tour package added-->
        <div class="new-tour-package">
            <div class="block">
                <div class="blocks-header">
                    <div class="heading">
                        <h2>Freshly Tour Packages Added</h2>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry</p>
                    </div>
                    <div class="button"><a href="#">Explore all Tour Packages</a></div>
                </div>
                <div class="cards">
                    <a class="card" href="#">
                        <div class="img-part">
                            <span>5D | 4N</span>
                            <figure><img src="./assets/images/tour-1.png" alt="tour" /></figure>
                        </div>
                        <h3>Srinagar Ladakh Manali Bike Trip Group Tour - Srinagar to Manali</h3>
                        <div class="column">Price: <span class="price">5,321/-</span></div>
                        <div class="column">Trip Start: <span>25 May, 2025</span></div>
                        <div class="column">Total No. of People: <span>20</span></div>
                        <div class="band">Only 12 slots are available</div>
                    </a>
                    <a class="card" href="#">
                        <div class="img-part">
                            <span>5D | 4N</span>
                            <figure><img src="./assets/images/tour-2.png" alt="tour" /></figure>
                        </div>
                        <h3>Srinagar Ladakh Manali Bike Trip Group Tour - Srinagar to Manali</h3>
                        <div class="column">Price: <span class="price">5,321/-</span></div>
                        <div class="column">Trip Start: <span>25 May, 2025</span></div>
                        <div class="column">Total No. of People: <span>20</span></div>
                        <div class="band">Only 12 slots are available</div>
                    </a>
                    <a class="card" href="#">
                        <div class="img-part">
                            <span>5D | 4N</span>
                            <figure><img src="./assets/images/tour-3.png" alt="tour" /></figure>
                        </div>
                        <h3>Srinagar Ladakh Manali Bike Trip Group Tour - Srinagar to Manali</h3>
                        <div class="column">Price: <span class="price">5,321/-</span></div>
                        <div class="column">Trip Start: <span>25 May, 2025</span></div>
                        <div class="column">Total No. of People: <span>20</span></div>
                        <div class="band red">Only 2 slots are available</div>
                    </a>
                    <a class="card" href="#">
                        <div class="img-part">
                            <span>5D | 4N</span>
                            <figure><img src="./assets/images/tour-4.png" alt="tour" /></figure>
                        </div>
                        <h3>Srinagar Ladakh Manali Bike Trip Group Tour - Srinagar to Manali</h3>
                        <div class="column">Price: <span class="price">5,321/-</span></div>
                        <div class="column">Trip Start: <span>25 May, 2025</span></div>
                        <div class="column">Total No. of People: <span>20</span></div>
                        <div class="band">Only 12 slots are available</div>
                    </a>
                </div>
                <div class="button btn-for-mobile"><a href="#">Explore all Tour Packages</a></div>
            </div>
        </div>


        <!-- Hotel Destinations -->
        <div class="hotel-destinations">
            <!-- <div class="block">
                <div class="blocks-header">
                    <div class="heading">
                        <h2>Hotel Destinations</h2>
                        <p>Explore our top destinations voted by more than 100,000+ customers around the world.s</p>
                    </div>
                    <div class="button"><a href="#">Explore all Destination</a></div>
                </div>
                <div class="cards">
                    <a class="card" href="#">
                        <figure>
                            <img src="./assets/images/dest-1.png" alt="tour" />
                        </figure>
                    </a>
                    <a class="card" href="#">
                        <figure>
                            <img src="./assets/images/dest-1.png" alt="tour" />
                        </figure>
                    </a>
                    <a class="card" href="#">
                        <figure>
                            <img src="./assets/images/dest-1.png" alt="tour" />
                        </figure>
                    </a>
                    <a class="card" href="#">
                        <figure>
                            <img src="./assets/images/dest-1.png" alt="tour" />
                        </figure>
                    </a>
                    <a class="card" href="#">
                        <figure>
                            <img src="./assets/images/dest-1.png" alt="tour" />
                        </figure>
                    </a>
                </div>
                <div class="button btn-for-mobile"><a href="#">Explore all Destinations</a></div>
            </div> -->

            <div class="block">
                <div class="blocks-header">
                    <div class="heading">
                        <h2>Hotel Destinations</h2>
                        <p>Explore our top destinations voted by more than 100,000+ customers around the world.</p>
                    </div>
                    <div class="button"><a href="#">Explore all Destinations</a></div>
                </div>
                <div class="car-container">
                    <?php foreach ($carList as $car): ?>
                        <div class="car_card">
                            <img class="card-img" src="<?= !empty($car['cover_image']) ? htmlspecialchars($car['cover_image']) : 'assets/images/default-car.jpg'; ?>" alt="<?= htmlspecialchars($car['name']); ?>">
                            <div class="card-img-overlay">
                                <h2 class="card-title"><?= htmlspecialchars($car['name']); ?></h2>
                                <p class="card-text">50 Drivers</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="button btn-for-mobile"><a href="#">Explore all Destinations</a></div>
            </div>
        </div>

        <!-- customize package -->
        <div class="new-tour-package pink">
            <div class="block">
                <div class="blocks-header">
                    <div class="heading">
                        <h2>Custom Package</h2>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry</p>
                    </div>
                    <div class="button"><a href="#">Explore all Packages</a></div>
                </div>
                <div class="cards">
                    <?php foreach ($packages_with_images as $package): ?>
                        <a class="card" onclick="redirectToQuery('<?= htmlspecialchars($package['duration']) ?>')" href="javascript:void(0);">
                            <div class="img-part">
                                <span><?= htmlspecialchars($package['duration']) ?></span>
                                <figure>
                                    <?php if (!empty($package['images'])): ?>
                                        <img src="<?= htmlspecialchars($package['images'][0]) ?>" alt="tour" />
                                    <?php else: ?>
                                        <img src="./assets/images/default.png" alt="default image" />
                                    <?php endif; ?>
                                </figure>
                            </div>
                            <h3><?= htmlspecialchars($package['package_name']) ?></h3>
                            <div class="column">Price: <span class="price">5,321/-</span></div>
                            <div class="plan-3d">
                                <img src="./assets/images/3d.png" alt="3d" /> Customize Your Plan
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                <div class="button btn-for-mobile"><a href="#">Explore all Packages</a></div>
            </div>
        </div>


        <!-- Blog section -->
        <div class="blog-section">
            <div class="block">
                <div class="blocks-header">
                    <div class="heading">
                        <h2>Our Blogs</h2>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry</p>
                    </div>
                    <div class="button"><a href="#">Explore all Blogs</a></div>
                </div>
                <div class="cards">
                    <div class="card">
                        <a href="#">
                            <figure><img src="./assets/images/blog-1.png" alt="blog" /></figure>
                        </a>
                        <div class="category-name"><a href="#">Category Name</a></div>
                        <h3><a href="#">ipsum is simply dummy text impsum has been the industry's...</a></h3>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard...</p>
                        <div class="date-and-author">
                            <date>25-Aug-2024</date>
                            <div class="author-name">Author Name</div>
                        </div>
                    </div>
                    <div class="card">
                        <a href="#">
                            <figure><img src="./assets/images/blog-2.png" alt="blog" /></figure>
                        </a>
                        <div class="category-name"><a href="#">Category Name</a></div>
                        <h3><a href="#">ipsum is simply dummy text impsum has been the industry's...</a></h3>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard...</p>
                        <div class="date-and-author">
                            <date>25-Aug-2024</date>
                            <div class="author-name">Author Name</div>
                        </div>
                    </div>
                    <div class="card">
                        <a href="#">
                            <figure><img src="./assets/images/blog-3.png" alt="blog" /></figure>
                        </a>
                        <div class="category-name"><a href="#">Category Name</a></div>
                        <h3><a href="#">ipsum is simply dummy text impsum has been the industry's...</a></h3>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard...</p>
                        <div class="date-and-author">
                            <date>25-Aug-2024</date>
                            <div class="author-name">Author Name</div>
                        </div>
                    </div>
                </div>
                <div class="button btn-for-mobile"><a href="#">Explore all Blogs</a></div>
            </div>
        </div>

        <div class="footer">
            <footer>
                <div class="left-footer">
                    <div class="col-1">
                        <div class="logo"></div>
                        <p>Rursus mal suada faci lisis the Lorem that the more ipsolarorit more that add in many ametion consectetur bulum as.</p>
                        <div class="social-icons">
                            <a class="#" class="twitter"></a>
                        </div>
                    </div>
                    <div class="col-2">
                        <h3>Company</h3>
                        <ul>
                            <li><a href="#">About Us</a></li>
                            <li><a href="#">Terms & Conditions</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">Blog</a></li>
                        </ul>
                    </div>
                </div>
                <div class="right-footer">
                    <h4>Newsletters & Updates</h4>
                    <p>Subscribe to get the latest tech career trends, guidance, and tips in your inbox.</p>
                    <div class="subscribe-news">
                        <input type="text" placeholder="Email Address" />
                        <button>Subscribe</button>
                    </div>

                </div>
            </footer>
        </div>
    </div>







    <script src="/assets/js/new-common-ui.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.dynamic-form');
            const radioButtons = document.querySelectorAll('input[name="tour-type"]');

            function toggleForm(selectedId) {
                forms.forEach(form => {
                    form.classList.remove('active');
                    if (form.id === `form-${selectedId}`) {
                        form.classList.add('active');
                    }
                });
            }
            radioButtons.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    toggleForm(e.target.id);
                });
            });
            toggleForm('group-tour');
        });
        $(document).ready(function() {
            var selectedDuration = null;
            var texiSelectedDuration = null;
            var selectedTourType = $("input[name='tour-type']:checked").val();
            console.log('selected tyoe', selectedTourType);
            $('#durationDropdown').change(function() {
                selectedDuration = $(this).val();
            });
            $('#taxiDuration').change(function() {
                texiSelectedDuration = $(this).val();
            });
            // Update selected tour type when radio button changes
            $("input[name='tour-type']").change(function() {
                selectedTourType = $(this).val();
            });

            // Handle search button click
            $('#searchBtn').click(function() {
                var travelingFrom = $('#traveling_from').val();
                var destination = $('#destination').val();
                var travelDate = $('#travel_date').val();

                if (!selectedDuration) {
                    alert('Please select a duration before searching!');
                    return;
                }

                if (!travelingFrom) {
                    alert('Please select a departure location!');
                    return;
                }

                if (!destination) {
                    alert('Please select a destination!');
                    return;
                }
                if (!travelDate) {
                    alert('Please select a travel date!');
                    return;
                }
                // Prepare query parameters
                var queryString = '?traveling_from=' + encodeURIComponent(travelingFrom) +
                    '&destination=' + encodeURIComponent(destination) +
                    '&travel_date=' + encodeURIComponent(travelDate) +
                    '&duration=' + encodeURIComponent(selectedDuration) +
                    '&tour-type=' + encodeURIComponent(selectedTourType);
                // Redirect with query parameters
                window.location.href = 'https://besttripdeal.com/agent_query_generate.php' + queryString;
            });


            $('#taxiSearchBtn').click(function() {
                var destination = $('#taxi_destination').val();
                var travelDate = $('#taxi_date_time').val();

                if (!texiSelectedDuration) {
                    alert('Please select a duration before searching!');
                    return;
                }
                if (!destination) {
                    alert('Please select a destination!');
                    return;
                }
                if (!travelDate) {
                    alert('Please select a travel date!');
                    return;
                }
                // Prepare query parameters
                var queryString = '?traveling_from=' + encodeURIComponent() +
                    '&destination=' + encodeURIComponent(destination) +
                    '&travel_date=' + encodeURIComponent(travelDate) +
                    '&duration=' + encodeURIComponent(texiSelectedDuration) +
                    '&tour-type=' + encodeURIComponent(selectedTourType);
                // Redirect with query parameters
                window.location.href = 'https://besttripdeal.com/taxi-detail.php' + queryString;
            });


        });

        function redirectToQuery(duration) {
            // Encode duration value to make it URL-safe
            var queryString = '?duration=' + encodeURIComponent(duration);

            // Redirect with query parameters
            window.location.href = 'https://besttripdeal.com/agent_query_generate.php' + queryString;
        }
    </script>
</body>

</html>