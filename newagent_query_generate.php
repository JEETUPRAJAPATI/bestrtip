<?php
session_start();
require_once './config/config.php';
//require_once 'includes/agent_header.php';
// is_agent_login();
$edit = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data_to_store = array_filter($_POST);

  $save_data = [];
  $save_data["name"] = $data_to_store['name'];
  $save_data["duration"] = $data_to_store['duration'];
  $save_data["tour_start_date"] = $data_to_store['tour_start_date'];
  $save_data["package_id"] = $data_to_store['package_id'];
  $save_data["category"] = $data_to_store['category'];
  $save_data["your_budget"] = $data_to_store['your_budget'];
  $save_data["cumulative"] = json_encode($data_to_store['cumulative'] ?? []);
  $save_data["per_person"] = json_encode($data_to_store['per_person'] ?? []);
  $save_data["per_service"] = json_encode($data_to_store['per_service'] ?? []);
  $save_data["person"] = json_encode($data_to_store['person'] ?? []);
  $save_data["transport"] = json_encode($data_to_store['transport'] ?? []);
  $save_data["permit"] = $data_to_store['permit'] ?? "off";
  $save_data["guide"] = $data_to_store['guide'] ?? "off";
  $save_data["created_by"] = $_SESSION['user_id'];
  $save_data["updated_by"] = $_SESSION['user_id'];
  $save_data["inclusive"] = !empty($data_to_store['inclusive']) ? $data_to_store['inclusive'] : json_encode([]);
  $save_data["exclusive"] = !empty($data_to_store['exclusive']) ? $data_to_store['exclusive'] : json_encode([]);
  $save_data["total_amount"] = $data_to_store['total_amount'];
  $save_data["without_gst"] = $data_to_store['without_gst'];
  $save_data["total_pax"] = $data_to_store['total_pax'];
  $save_data["tour_end_date"] = $data_to_store['tour_end_date'];

  $db = getDbInstance();
  $db->orderBy('id', 'desc');
  $booking_last = $db->getOne("agent_queries");
  //print_r($booking_last);
  if ($booking_last) {
    $save_data['booking_code'] = sprintf("GLB%04d", $booking_last['id'] + 1);
    $save_data['query_code'] = sprintf("QG%04d", $booking_last['id'] + 1);
  } else {
    $save_data['booking_code'] = sprintf("GLB%04d",  1);
    $save_data['query_code'] = sprintf("QG%04d",  1);
  }
  $inserted_id = $db->insert('agent_queries', $save_data);
  $_SESSION['success'] = "The query has been generated successfully.";
  header("Location: agent_query_edit.php?ID=" . encryptId($inserted_id));
}



$db = getDbInstance();
$vehicles = $db->get("vehicles", null, 'driver_name, vehicle_number, mobile, vehicle_type');
$vehicleData = [];
foreach ($vehicles as $vehicle) {
  $vehicleData[$vehicle['vehicle_type']] = $vehicle;
}
$json_vehicle = json_encode($vehicleData);


$selectedPackage = [
  'id' => isset($_GET['id']) ? $_GET['id'] : null,
  'name' => isset($_GET['name']) ? $_GET['name'] : null,
  'code' => isset($_GET['code']) ? $_GET['code'] : null,
  'duration' => isset($_GET['duration']) ? $_GET['duration'] : null,
  'category' => isset($_GET['category']) ? $_GET['category'] : null,
];

// Fetch destinations
$destinations = $db->get("destination", null, 'id, name');
$traveling_from = $db->get("traveling_from", null, 'id, name');
// echo "<pre>";
// print_r($destinations);
// echo "</pre>";
// die();

?>
<!DOCTYPE html>

<!--
 // WEBSITE: https://NodeAscend.com
 // TWITTER: https://twitter.com/NodeAscend
 // FACEBOOK: https://www.facebook.com/NodeAscend
 // GITHUB: https://github.com/NodeAscend/
-->

<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="description" content="Travilio Ladakh,business,company,agency,modern,bootstrap4,tech,software">

  <!-- theme meta -->
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <meta name="theme-name" content="Travilio Ladakh" />

  <title>Travel Website</title>

  <!-- Favicon -->
  <link rel="shortcut icon" type="image/x-icon" href="assets/trip-assets/images/favicon.ico" />

  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <!-- Icon Font Css -->
  <link rel="stylesheet" href="assets/trip-assets/plugins/themify/css/themify-icons.css" />
  <link rel="stylesheet" href="assets/trip-assets/plugins/fontawesome/css/all.css" />
  <!-- <link rel="stylesheet" href="plugins/magnific-popup/dist/magnific-popup.css"> -->
  <!-- <link rel="stylesheet" href="plugins/modal-video/modal-video.css"> -->
  <link rel="stylesheet" href="assets/trip-assets/plugins/animate-css/animate.css" />
  <!-- Slick Slider  CSS -->
  <link rel="stylesheet" href="assets/trip-assets/plugins/slick-carousel/slick/slick.css" />
  <link rel="stylesheet" href="assets/trip-assets/plugins/slick-carousel/slick/slick-theme.css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      corePlugins: {
        preflight: false,  // This disables Tailwind's reset CSS
      }
    }
  </script>
  <!-- Main Stylesheet -->
  <link rel="stylesheet" href="assets/trip-assets/css/style.css">
 
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<!--<script src="assets/trip-assets/plugins/jquery/jquery.js"></script>-->
</head>

<body>
<!-- Navbar -->
<nav class="bg-white py-3 shadow-sm">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center">
            <!-- Logo Section -->
            <a href="#" class="flex items-center">
                <div class="mr-2">
                    <!-- Font Awesome umbrella-beach icon -->
                    <i class="fas fa-umbrella-beach fs-2 text-gray-800"></i>
                </div>
                <span class="font-bold fs-2">LOGO</span>
            </a>

            <!-- Mobile Menu Button -->
            <div class="lg:hidden">
                <button
                    class="flex items-center px-3 py-2 border rounded text-gray-500 border-gray-500 hover:text-gray-700 hover:border-gray-700"
                    id="mobile-menu-button">
                    <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" />
                    </svg>
                </button>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex flex-1 justify-center">
                <ul class="flex space-x-8">
                    <li><a href="#" class="text-gray-700 hover:text-gray-900">Link1</a></li>
                    <li><a href="#" class="text-gray-700 hover:text-gray-900">Link2</a></li>
                    <li><a href="#" class="text-gray-700 hover:text-gray-900">Link3</a></li>
                    <li><a href="#" class="text-gray-700 hover:text-gray-900">Link4</a></li>
                    <li><a href="#" class="text-gray-700 hover:text-gray-900">Link5</a></li>
                </ul>
            </div>

            <!-- Contact and Sign In -->
            <div class="hidden lg:flex items-center">
                <div class="mr-3">
                    <i class="fas fa-phone-alt mr-1"></i>
                    <span>+75 123 456 789</span>
                </div>
                <a href="#" class="px-4 py-2 text-blue-600 hover:text-blue-800 flex items-center">
                    <i class="fas fa-user mr-1"></i>
                    Sign in
                </a>
            </div>
        </div>

        <!-- Mobile Menu (hidden by default) -->
        <div class="hidden lg:hidden" id="mobile-menu">
            <ul class="mt-2 space-y-2 pb-3">
                <li><a href="#"
                        class="block px-3 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded">Link1</a>
                </li>
                <li><a href="#"
                        class="block px-3 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded">Link2</a>
                </li>
                <li><a href="#"
                        class="block px-3 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded">Link3</a>
                </li>
                <li><a href="#"
                        class="block px-3 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded">Link4</a>
                </li>
                <li><a href="#"
                        class="block px-3 py-2 text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded">Link5</a>
                </li>
            </ul>
            <div class="border-t border-gray-200 pt-3 pb-2">
                <div class="flex items-center px-3 py-2">
                    <i class="fas fa-phone-alt mr-1"></i>
                    <span>+75 123 456 789</span>
                </div>
                <a href="#" class="flex items-center px-3 py-2 text-blue-600">
                    <i class="fas fa-user mr-1"></i>
                    Sign in
                </a>
            </div>
        </div>
    </div>
</nav>


<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <h1 class="hero-title text-center">Trip Details</h1>
    </div>
</section>

<!-- Booking Form -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-10 col-sm-12 col-md-10 col-lg-10">
            <div class="booking-form booking-search-box">
                <div class="row align-items-start">
                    <div class="col-md-9">
                        <h2 class="form-title text-theme heading-theme">Select Trip</h2>
                    </div>
                    <div class="col-md-3 text-end">
                        <a class="help-text text-theme" href="#">
                            <i class="fas fa-question-circle me-1"></i>
                            Need some help?
                        </a>
                    </div>
                </div>

                <!-- Search Form -->
                <div class="row mt-2 g-1 search-form-controls">
                    <div class="col-md col-sm-12 col-md-8 offset-md-2 col-lg-3 offset-lg-0 pe-1">
                        <label class="form-label">Location</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white p-0 pe-1">
                                <i class="fas fa-map-marker-alt text-theme"></i>
                            </span>
                            <select class="form-select p-0" id="inputGroupSelect01">
                                <option selected>bangkok</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>

                        </div>
                    </div>
                    <div class="col-md col-sm-12 col-md-8 offset-md-2 col-lg-2 offset-lg-0">
                        <label class="form-label">Start Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white p-0">
                                <i class="fas fa-calendar text-theme"></i>
                                <input type="text" id="datepicker-start" class="date border-0">
                                <input id="calendar" type="hidden" name="tour_start_date" onChange="return itinerary_list()" value="<?= $_REQUEST['travel_date']; ?>">
                            </span>

                        </div>
                    </div>
                    <div class="col-md col-sm-12 col-md-8 offset-md-2 col-lg-2 offset-lg-0">
                        <label class="form-label">End Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white p-0">
                                <i class="fas fa-calendar text-theme"></i>
                                <input type="text" id="datepicker-end" class="date border-0">
                            </span>

                        </div>
                    </div>
                    <div class="col-md col-sm-12 col-md-8 offset-md-2 col-lg-3 offset-lg-0">
                        <label class="form-label">Guest</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white p-0 pe-2">
                                <i class="fas fa-user text-theme"></i>
                            </span>
                            <input type="text" class="p-0 form-control bg-transparent" value="2 adults, 2 children"
                                readonly>
                            <span class="p-0 input-group-text bg-white">
                                <i class="fas fa-chevron-down text-secondary"></i>
                            </span>
                        </div>
                    </div>
                    <div class=" col-md-3 offset-md-4 col-lg-1 offset-lg-0 ms-lg-4">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-theme text-white w-100 text-md-center text-center">
                            <i class="fas fa-search me-1"></i>
                            Search
                        </button>
                    </div>
                </div>

                <!-- Room Selector -->
                <div class="room-selector ">
                    <div class="row ms-md-5">
                        <div class="room-card">
                            <div class="room-type cwb">
                                <i class="">
                                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_287_460)">
                                            <path
                                                d="M12.6875 10.2812C13.2916 10.2812 13.7812 9.79156 13.7812 9.1875C13.7812 8.58344 13.2916 8.09375 12.6875 8.09375C12.0834 8.09375 11.5938 8.58344 11.5938 9.1875C11.5938 9.79156 12.0834 10.2812 12.6875 10.2812Z"
                                                fill="#3F1E63" />
                                            <path
                                                d="M8.3125 10.2812C8.91656 10.2812 9.40625 9.79156 9.40625 9.1875C9.40625 8.58344 8.91656 8.09375 8.3125 8.09375C7.70844 8.09375 7.21875 8.58344 7.21875 9.1875C7.21875 9.79156 7.70844 10.2812 8.3125 10.2812Z"
                                                fill="#3F1E63" />
                                            <path
                                                d="M20.0725 9.9225C19.8538 8.60125 18.8825 7.525 17.6137 7.14875C17.15 6.16875 16.4938 5.31125 15.6975 4.6025C14.315 3.36875 12.495 2.625 10.5 2.625C8.505 2.625 6.685 3.36875 5.3025 4.6025C4.4975 5.31125 3.84125 6.1775 3.38625 7.14875C2.1175 7.525 1.14625 8.5925 0.9275 9.9225C0.8925 10.1063 0.875 10.2987 0.875 10.5C0.875 10.7013 0.8925 10.8938 0.9275 11.0775C1.14625 12.3988 2.1175 13.475 3.38625 13.8512C3.84125 14.8225 4.4975 15.68 5.285 16.38C6.6675 17.6225 8.49625 18.375 10.5 18.375C12.5037 18.375 14.3325 17.6225 15.7237 16.38C16.5112 15.68 17.1675 14.8138 17.6225 13.8512C18.8825 13.475 19.8538 12.4075 20.0725 11.0775C20.1075 10.8938 20.125 10.7013 20.125 10.5C20.125 10.2987 20.1075 10.1063 20.0725 9.9225ZM16.625 12.25C16.5375 12.25 16.4587 12.2325 16.3712 12.2238C16.1962 12.81 15.9425 13.3525 15.6188 13.8512C14.525 15.5225 12.6438 16.625 10.5 16.625C8.35625 16.625 6.475 15.5225 5.38125 13.8512C5.0575 13.3525 4.80375 12.81 4.62875 12.2238C4.54125 12.2325 4.4625 12.25 4.375 12.25C3.4125 12.25 2.625 11.4625 2.625 10.5C2.625 9.5375 3.4125 8.75 4.375 8.75C4.4625 8.75 4.54125 8.7675 4.62875 8.77625C4.80375 8.19 5.0575 7.6475 5.38125 7.14875C6.475 5.4775 8.35625 4.375 10.5 4.375C12.6438 4.375 14.525 5.4775 15.6188 7.14875C15.9425 7.6475 16.1962 8.19 16.3712 8.77625C16.4587 8.7675 16.5375 8.75 16.625 8.75C17.5875 8.75 18.375 9.5375 18.375 10.5C18.375 11.4625 17.5875 12.25 16.625 12.25ZM10.5 14.875C12.2587 14.875 13.7725 13.7987 14.4375 12.25H6.5625C7.2275 13.7987 8.74125 14.875 10.5 14.875Z"
                                                fill="#3F1E63" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_287_460">
                                                <rect width="21" height="21" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </i>
                                CWB
                            </div>
                            <div class="room-counter">
                                <button class="counter-btn">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="counter-value">2</span>
                                <button class="counter-btn">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="room-card">
                            <div class="room-type cnb">
                                <i class="">
                                    <svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_429_1008)">
                                            <path
                                                d="M12.6875 10.2812C13.2916 10.2812 13.7812 9.79156 13.7812 9.1875C13.7812 8.58344 13.2916 8.09375 12.6875 8.09375C12.0834 8.09375 11.5938 8.58344 11.5938 9.1875C11.5938 9.79156 12.0834 10.2812 12.6875 10.2812Z"
                                                fill="#3F1E63" />
                                            <path
                                                d="M8.3125 10.2812C8.91656 10.2812 9.40625 9.79156 9.40625 9.1875C9.40625 8.58344 8.91656 8.09375 8.3125 8.09375C7.70844 8.09375 7.21875 8.58344 7.21875 9.1875C7.21875 9.79156 7.70844 10.2812 8.3125 10.2812Z"
                                                fill="#3F1E63" />
                                            <path
                                                d="M20.0725 9.9225C19.8538 8.60125 18.8825 7.525 17.6137 7.14875C17.15 6.16875 16.4938 5.31125 15.6975 4.6025C14.315 3.36875 12.495 2.625 10.5 2.625C8.505 2.625 6.685 3.36875 5.3025 4.6025C4.4975 5.31125 3.84125 6.1775 3.38625 7.14875C2.1175 7.525 1.14625 8.5925 0.9275 9.9225C0.8925 10.1063 0.875 10.2987 0.875 10.5C0.875 10.7013 0.8925 10.8938 0.9275 11.0775C1.14625 12.3988 2.1175 13.475 3.38625 13.8512C3.84125 14.8225 4.4975 15.68 5.285 16.38C6.6675 17.6225 8.49625 18.375 10.5 18.375C12.5037 18.375 14.3325 17.6225 15.7237 16.38C16.5112 15.68 17.1675 14.8138 17.6225 13.8512C18.8825 13.475 19.8538 12.4075 20.0725 11.0775C20.1075 10.8938 20.125 10.7013 20.125 10.5C20.125 10.2987 20.1075 10.1063 20.0725 9.9225ZM16.625 12.25C16.5375 12.25 16.4587 12.2325 16.3712 12.2238C16.1962 12.81 15.9425 13.3525 15.6188 13.8512C14.525 15.5225 12.6438 16.625 10.5 16.625C8.35625 16.625 6.475 15.5225 5.38125 13.8512C5.0575 13.3525 4.80375 12.81 4.62875 12.2238C4.54125 12.2325 4.4625 12.25 4.375 12.25C3.4125 12.25 2.625 11.4625 2.625 10.5C2.625 9.5375 3.4125 8.75 4.375 8.75C4.4625 8.75 4.54125 8.7675 4.62875 8.77625C4.80375 8.19 5.0575 7.6475 5.38125 7.14875C6.475 5.4775 8.35625 4.375 10.5 4.375C12.6438 4.375 14.525 5.4775 15.6188 7.14875C15.9425 7.6475 16.1962 8.19 16.3712 8.77625C16.4587 8.7675 16.5375 8.75 16.625 8.75C17.5875 8.75 18.375 9.5375 18.375 10.5C18.375 11.4625 17.5875 12.25 16.625 12.25ZM10.5 14.875C12.2587 14.875 13.7725 13.7987 14.4375 12.25H6.5625C7.2275 13.7987 8.74125 14.875 10.5 14.875Z"
                                                fill="#3F1E63" />
                                        </g>
                                        <path d="M1 2L18 18" stroke="#3F1E63" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <defs>
                                            <clipPath id="clip0_429_1008">
                                                <rect width="21" height="21" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>

                                </i>
                                CNB
                            </div>
                            <div class="room-counter">
                                <button class="counter-btn">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="counter-value">2</span>
                                <button class="counter-btn">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="room-card">
                            <div class="room-type twin">
                                <i class="">
                                    <svg width="26" height="25" viewBox="0 0 26 25" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M23.0152 13C23.0152 11.9 22.1145 11 21.0137 11V8C21.0137 6.9 20.113 6 19.0122 6H7.00304C5.9022 6 5.00152 6.9 5.00152 8V11C3.90068 11 3 11.9 3 13V18H4.33101L5.00152 20H6.00228L6.67279 18H19.3524L20.0129 20H21.0137L21.6842 18H23.0152V13ZM19.0122 11H14.0084V8H19.0122V11ZM7.00304 8H12.0068V11H7.00304V8ZM5.00152 13H21.0137V16H5.00152V13Z"
                                            fill="#3F1E63" />
                                        <path
                                            d="M20.0116 10C22.7751 10 25.0154 7.76142 25.0154 5C25.0154 2.23858 22.7751 0 20.0116 0C17.2481 0 15.0078 2.23858 15.0078 5C15.0078 7.76142 17.2481 10 20.0116 10Z"
                                            fill="#3F1E63" />
                                        <path
                                            d="M19.4947 7.21077L20.6989 5.79385C20.8589 5.61385 21.0021 5.42923 21.1368 5.24923C21.2716 5.06923 21.3853 4.88923 21.4821 4.70923C21.5789 4.52923 21.6547 4.34923 21.7095 4.16923C21.7642 3.98923 21.7895 3.80462 21.7895 3.62462C21.7895 3.38 21.7516 3.15385 21.6758 2.95077C21.6 2.74769 21.4905 2.57692 21.3474 2.43846C21.2042 2.29538 21.0232 2.18923 20.8168 2.11077C20.6021 2.03692 20.3621 2 20.0926 2C19.8021 2 19.5411 2.05077 19.3137 2.14769C19.0863 2.24462 18.8926 2.38308 18.7411 2.55385C18.5853 2.72462 18.4674 2.92308 18.3874 3.15385C18.3116 3.37077 18.2737 3.60154 18.2695 3.84615H19.1705C19.1747 3.70308 19.1916 3.56923 19.2253 3.44462C19.2632 3.31077 19.3221 3.19538 19.3937 3.09846C19.4695 3.00154 19.5663 2.92769 19.68 2.87231C19.7937 2.81692 19.9326 2.78923 20.0842 2.78923C20.2147 2.78923 20.3284 2.81231 20.4253 2.85846C20.5221 2.90462 20.6063 2.97385 20.6737 3.05692C20.7411 3.14 20.7916 3.24154 20.8295 3.35692C20.8632 3.47231 20.8842 3.59692 20.8842 3.73077C20.8842 3.83231 20.8716 3.92923 20.8505 4.03077C20.8253 4.13231 20.7874 4.23846 20.7284 4.35385C20.6695 4.46923 20.5937 4.59846 20.4926 4.73692C20.3958 4.87538 20.2737 5.03692 20.1221 5.21231L18.3663 7.31231V8H22V7.21077H19.4947Z"
                                            fill="white" />
                                    </svg>
                                </i>
                                TWIN
                            </div>
                            <div class="room-counter">
                                <button class="counter-btn" data-action="decrease">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="counter-value">2</span>
                                <button class="counter-btn" data-action="increase">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="room-card">
                            <div class="room-type triple">
                                <i class="">
                                    <svg width="26" height="25" viewBox="0 0 26 25" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M23.5386 13C23.5386 11.9 22.638 11 21.5371 11V8C21.5371 6.9 20.6364 6 19.5356 6H7.52648C6.42564 6 5.52496 6.9 5.52496 8V11C4.42412 11 3.52344 11.9 3.52344 13V18H4.85445L5.52496 20H6.52572L7.19623 18H19.8759L20.5364 20H21.5371L22.2076 18H23.5386V13ZM19.5356 11H14.5318V8H19.5356V11ZM7.52648 8H12.5303V11H7.52648V8ZM5.52496 13H21.5371V16H5.52496V13Z"
                                            fill="#3F1E63" />
                                        <path
                                            d="M20.535 10C23.2986 10 25.5388 7.76142 25.5388 5C25.5388 2.23858 23.2986 0 20.535 0C17.7715 0 15.5312 2.23858 15.5312 5C15.5312 7.76142 17.7715 10 20.535 10Z"
                                            fill="#3F1E63" />
                                        <path
                                            d="M25.2125 0.000679136L15.8578 0.00071785C15.1235 0.00071785 15.857 0.000685841 15.857 0.000685841L15.868 10C15.868 10 21.5633 10 25.2125 10C25.2125 6.09493 25.2125 0.000679136 25.2125 0.000679136C25.9467 0.000679136 25.2125 -0.00084892 25.2125 0.000679136ZM25.2125 10C21.5633 10 15.868 10 15.868 10L15.8578 0.00071785L25.2125 0.000679136C25.2125 0.000679136 25.2125 6.09493 25.2125 10ZM22.5427 7.14305V6.0717C22.5427 5.47888 22.0955 5.00034 21.5415 5.00034C22.0955 5.00034 22.5427 4.5218 22.5427 3.92899V2.85763C22.5427 2.06483 21.942 1.42916 21.2077 1.42916H18.5379V2.85763H21.2077V4.28611H19.8728V5.71458H21.2077V7.14305H18.5379V8.57153H21.2077C21.942 8.57153 22.5427 7.93586 22.5427 7.14305Z"
                                            fill="white" />
                                    </svg>

                                </i>
                                TRIPLE
                            </div>
                            <div class="room-counter">
                                <button class="counter-btn">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="counter-value">2</span>
                                <button class="counter-btn">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="room-card">
                            <div class="room-type single">
                                <i class="">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_287_454)">
                                            <path
                                                d="M23 12C23 10.9 22.1 10 21 10V7C21 5.9 20.1 5 19 5H7C5.9 5 5 5.9 5 7V10C3.9 10 3 10.9 3 12V17H4.33L5 19H6L6.67 17H19.34L20 19H21L21.67 17H23V12ZM19 10H14V7H19V10ZM7 7H12V10H7V7ZM5 12H21V15H5V12Z"
                                                fill="#3F1E63" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_287_454">
                                                <rect width="24" height="24" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>

                                </i>
                                SINGLE
                            </div>
                            <div class="room-counter">
                                <button class="counter-btn">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="counter-value">2</span>
                                <button class="counter-btn">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="room-card">
                            <div class="room-type quad">
                                <i class="">
                                    <svg width="26" height="25" viewBox="0 0 26 25" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_287_526)">
                                            <path
                                                d="M23 13C23 11.9 22.1 11 21 11V8C21 6.9 20.1 6 19 6H7C5.9 6 5 6.9 5 8V11C3.9 11 3 11.9 3 13V18H4.33L5 20H6L6.67 18H19.34L20 20H21L21.67 18H23V13ZM19 11H14V8H19V11ZM7 8H12V11H7V8ZM5 13H21V16H5V13Z"
                                                fill="#3F1E63" />
                                        </g>
                                        <path
                                            d="M20.8632 10C23.6267 10 25.867 7.76142 25.867 5C25.867 2.23858 23.6267 0 20.8632 0C18.0997 0 15.8594 2.23858 15.8594 5C15.8594 7.76142 18.0997 10 20.8632 10Z"
                                            fill="#3F1E63" />
                                        <path
                                            d="M25.8677 0.000434009L15.2589 0C14.4254 0 15.2589 0.000961019 15.2589 0.000961019L15.2555 10C15.2313 9.99957 14.3946 9.99957 15.2281 9.99957H25.8397L25.8677 0.000434009ZM25.8397 9.99957L15.2589 10V0L25.8677 0.000434009L25.8397 9.99957ZM21.3211 8.57149H22.8366V1.42894H21.3211V4.28596H19.8056V1.42894H18.29V5.71447H21.3211V8.57149Z"
                                            fill="white" />
                                        <defs>
                                            <clipPath id="clip0_287_526">
                                                <rect width="24" height="24" fill="white" transform="translate(0 1)" />
                                            </clipPath>
                                        </defs>
                                    </svg>

                                </i>
                                QUAD
                            </div>
                            <div class="room-counter">
                                <button class="counter-btn">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="counter-value">2</span>
                                <button class="counter-btn">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="pt-5 row">
        <div class="col-lg-8 col-sm-12 col-md-12">
            <h1 class="tour-title" id="package_name"></h1>
<div class="d-flex align-items-center mb-3">
    <div class="location-badge">
        <i class="fas fa-map-marker-alt me-1 text-primary"></i>
        <?php
            $target_id = $_REQUEST['destination'];
$destination_name = '';

foreach ($destinations as $destination) {
    if ($destination['id'] == $target_id) {
        $destination_name = $destination['name'];
        break;
    }
}
        ?>
        <span id="destination"><?=$destination_name;?></span>
    </div>
    <div class="rating">
       <?php
$rating = $_REQUEST['package_category'];

if ($rating == '1*') {
    ?>
    <i class="fas fa-star"></i>
    <i class="far fa-star"></i>
    <i class="far fa-star"></i>
    <i class="far fa-star"></i>
    <i class="far fa-star"></i>
    <?php
} elseif ($rating == '2**') {
    ?>
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i>
    <i class="far fa-star"></i>
    <i class="far fa-star"></i>
    <i class="far fa-star"></i>
    <?php
} elseif ($rating == '3***') {
    ?>
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i>
    <i class="far fa-star"></i>
    <i class="far fa-star"></i>
    <?php
} elseif ($rating == '4****') {
    ?>
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i>
    <i class="far fa-star"></i>
    <?php
} elseif ($rating == '5*****') {
    ?>
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i>
    <?php
}
?>

        <!--<i class="fas fa-star"></i>-->
        <!--<i class="fas fa-star"></i>-->
        <!--<i class="fas fa-star"></i>-->
        <!--<i class="fas fa-star"></i>-->
        <!--<i class="fas fa-star-half-alt"></i>-->
        <span class="text-muted ms-1">(148 reviews)</span>
    </div>
</div>
<div class="gallery-images">

</div>

<!-- Tabs -->
<h2 class="section-title">Overview</h2>
<div class="overview-content bg-white overview-section">

</div>

            <h3 class="section-title">Itinerary</h2>


    <!-- Itinerary Container -->
 
    <!--<div id="itinerary-list">-->
    <!--    </div>-->
    <div class="d-flex flex-column py-1 mt-5 itenary-container bg-white" id="itinerary-list">
        
    </div>
            <section class="flight-section">
                <div class="d-flex align-items-center justify-content-between mt-5 mb-4">
                    <h3 class="text-s sm:text-2xl section-title">Select Flight</h3>
                    <a href="#" class="btn-theme">
                        View More
                        <i class="fa-arrow-right mx-2 fas"></i>
                    </a>
                </div>
                <div class="search-container">
    <div class="row g-3">
        <div class="col-md">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-plane-departure"></i>
                </span>
                <input type="text" class="form-control border-start-0" placeholder="From Where">
            </div>
        </div>
        <div class="col-md">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-plane-arrival"></i>
                </span>
                <input type="text" class="form-control border-start-0" placeholder="To Where">
            </div>
        </div>
        <div class="col-md">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="far fa-calendar"></i>
                </span>
                <input type="text" class="form-control border-start-0" placeholder="Depart">
            </div>
        </div>
        <div class="col-md">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="far fa-calendar"></i>
                </span>
                <input type="text" class="form-control border-start-0" placeholder="Return">
            </div>
        </div>
        <div class="col-md">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-user"></i>
                </span>
                <input type="text" class="form-control border-start-0" placeholder="Travellers">
            </div>
        </div>
    </div>

    <div class="mt-3">
        <div class="text-end">
            <a class="me-3 text-theme" href="#"><i class="far fa-question-circle"></i> Need some help?</a>
            <button class="search-btn">
                <i class="fas fa-search me-2"></i> Search
            </button>
        </div>
    </div>
</div>
<div class="flight-list">
    <div class="row">
        <!-- Indigo Flight Card -->

        <!-- Lufthansa Flight Card -->
        <div class="col-md-4 mb-3 col-sm-6 col-12">
            <div class="card flight-card">

                <div style="position: relative;  overflow: hidden;">

                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQLIEyJ-EYgoHpuFHPKfA4Qr4BqkDChWRbjsQ&s"
                        class="card-img-top flight-img" alt="Indigo">

                    <div class="flight-card-overlay">
                    </div>

                    <h4 class="flight-card-title">
                        Indigo Airlines <small>LE2639</small>
                        </h5>

                        <!-- Selection indicator -->
                        <div class="selection-indicator">
                            <!-- Check icon using SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="white" stroke-width="3" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                </div>
                <div class="card-body">
                    <p class="card-text mb-0">Delhi to Kerala</p>
                    <p class="text-muted mb-0">11:00 AM - 3:00 PM</p>
                    <p class="baggage-info mb-0"><i class="fas fa-suitcase-rolling"></i> Carry-on
                        baggage
                        included
                    </p>
                    <p class="text-danger mb-0">Non-refundable</p>
                    <div style="display: flex; justify-content: flex-end;">
                        <button class="btn btn-theme">
                            <i class="fas fa-plane me-1"></i> Add Flight
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3 col-sm-6 col-12">
            <div class="card flight-card">

                <div style="position: relative;  overflow: hidden;">

                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQLIEyJ-EYgoHpuFHPKfA4Qr4BqkDChWRbjsQ&s"
                        class="card-img-top flight-img" alt="Indigo">

                    <div class="flight-card-overlay">
                    </div>

                    <h4 class="flight-card-title">
                        Indigo Airlines <small>LE2639</small>
                        </h5>

                        <!-- Selection indicator -->
                        <div class="selection-indicator">
                            <!-- Check icon using SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="white" stroke-width="3" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                </div>
                <div class="card-body">
                    <p class="card-text mb-0">Delhi to Kerala</p>
                    <p class="text-muted mb-0">11:00 AM - 3:00 PM</p>
                    <p class="baggage-info mb-0"><i class="fas fa-suitcase-rolling"></i> Carry-on
                        baggage
                        included
                    </p>
                    <p class="text-danger mb-0">Non-refundable</p>
                    <div style="display: flex; justify-content: flex-end;">
                        <button class="btn btn-theme">
                            <i class="fas fa-plane me-1"></i> Add Flight
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3 col-sm-6 col-12">
            <div class="card flight-card">

                <div style="position: relative;  overflow: hidden;">

                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQLIEyJ-EYgoHpuFHPKfA4Qr4BqkDChWRbjsQ&s"
                        class="card-img-top flight-img" alt="Indigo">

                    <div class="flight-card-overlay">
                    </div>

                    <h4 class="flight-card-title">
                        Indigo Airlines <small>LE2639</small>
                        </h5>

                        <!-- Selection indicator -->
                        <div class="selection-indicator">
                            <!-- Check icon using SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="white" stroke-width="3" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                </div>
                <div class="card-body">
                    <p class="card-text mb-0">Delhi to Kerala</p>
                    <p class="text-muted mb-0">11:00 AM - 3:00 PM</p>
                    <p class="baggage-info mb-0"><i class="fas fa-suitcase-rolling"></i> Carry-on
                        baggage
                        included
                    </p>
                    <p class="text-danger mb-0">Non-refundable</p>
                    <div style="display: flex; justify-content: flex-end;">
                        <button class="btn btn-theme">
                            <i class="fas fa-plane me-1"></i> Add Flight
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

document.querySelectorAll('.flight-card').forEach(card => {
        card.addEventListener('click', function () {
            // Reset all cards
            document.querySelectorAll('.flight-card').forEach(c => {
                c.style.border = '1px solid #e0e0e0';
                c.querySelector('.selection-indicator').style.display = 'none';
            });

            // Select this card
            this.style.border = '2px solid #0275d8';
            this.querySelector('.selection-indicator').style.display = 'flex';
        });
    });

</script>
            </section>

            <section class="hotel-section">
                <div class="mt-5 mb-4">
                    <!-- Header and Filters Row -->
                    <div
                        class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-md-between">
                        <!-- Left side with title and rating buttons -->
                        <div
                            class="d-flex flex-column flex-md-row align-items-start align-items-md-center mb-3 mb-md-0">
                            <h3 class="section-title me-md-3 fs-5">Select Hotel</h3>
                            <div class="d-flex gap-2 mt-2 mt-md-0">
                                <button class="btn rounded-3 rating-btn text-theme hover:bg-purple-900 hover:text-white"
                                    onclick="selectRating(this)">
                                    <i class="fas fa-star"></i> 2+
                                </button>
                                <button class="btn rounded-3 rating-btn text-theme hover:bg-purple-900 hover:text-white"
                                    onclick="selectRating(this)">
                                    <i class="fas fa-star"></i> 3+
                                </button>
                                <button class="btn rounded-3 rating-btn text-theme hover:bg-purple-900 hover:text-white"
                                    onclick="selectRating(this)">
                                    <i class="fas fa-star"></i> 4+
                                </button>
                                <button class="btn rounded-3 rating-btn text-theme hover:bg-purple-900 hover:text-white"
                                    onclick="selectRating(this)">
                                    <i class="fas fa-star"></i> 5
                                </button>
                            </div>
                        </div>

                        <!-- View More button - shows below on mobile, to the right on larger screens -->
                        <a href="#" class="btn-theme">
                            View More
                            <i class="fa-arrow-right mx-2 fas"></i>
                        </a>
                    </div>
                </div>
                <!-- Hotel card would be included here -->
                <div class="row" id="hotel-list">
                    
                </div>
            </section>
            <section class="taxi-section">
                <div class="d-flex align-items-center justify-content-between mt-5 mb-4">
                    <h3 class="text-s sm:text-2xl section-title">Select Taxi</h3>
                    <a href="#" class="btn-theme">
                        View More
                        <i class="fa-arrow-right mx-2 fas"></i>
                    </a>
                </div>
                <input type="hidden" name="package_id" id="pid">
<input type="hidden" name="category">
<input type="hidden" name="total_amount">
<input type="hidden" name="without_gst">
<input type="hidden" name="total_pax">
<input type="hidden" name="tour_end_date">
<input type="hidden" name="exclusive">
<input type="hidden" name="inclusive">
                <div class="row" id="newtaxi-list">
<!--                    <div class="col-12 col-sm-6 col-md-4 col-lg-4">-->
    <!-- Header with "tion" text -->

    <!-- Car Card -->
<!--    <div class="bg-white p-2 rounded-lg overflow-hidden mb-4 shadow">-->
        <!-- Car Image -->
<!--        <div class="w-auto p-2">-->
<!--            <img src="https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8Y2FyfGVufDB8fDB8fHww"-->
<!--                alt="White sports car" class="w-full h-full object-cover">-->
<!--        </div>-->

        <!-- Car Details -->
<!--        <div class="p-2">-->
            <!-- Car Name -->
<!--            <h2 class="text-2xl font-bold text-gray-800">Jeep Cherokee</h2>-->

            <!-- Car Type -->
<!--            <p class="text-gray-500 mb-2">SUV</p>-->

            <!-- Car Features -->
<!--            <div class="flex items-center justify-between mb-2">-->
<!--                <div class="flex items-center">-->
<!--                    <div class="rounded p-1">-->
<!--                        <i class="fas fa-suitcase-rolling fs-5 text-theme"></i>-->
<!--                    </div>-->
<!--                    <span>5 bags</span>-->
<!--                </div>-->

<!--                <div class="flex items-center">-->
<!--                    <div class="bg-white text-theme">-->
<!--                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"-->
<!--                            stroke="currentColor">-->
<!--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"-->
<!--                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />-->
<!--                        </svg>-->
<!--                    </div>-->
<!--                    <span>6 People</span>-->
<!--                </div>-->
<!--            </div>-->

            <!-- Quantity Selector -->
<!--            <div class="quantity-control">-->
<!--                <button class="quantity-btn">−</button>-->
<!--                <span class="quantity">2</span>-->
<!--                <button class="quantity-btn">+</button>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!--                    <div class="col-12 col-sm-6 col-md-4 col-lg-4">-->
    <!-- Header with "tion" text -->

    <!-- Car Card -->
<!--    <div class="bg-white p-2 rounded-lg overflow-hidden mb-4 shadow">-->
        <!-- Car Image -->
<!--        <div class="w-auto p-2">-->
<!--            <img src="https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8Y2FyfGVufDB8fDB8fHww"-->
<!--                alt="White sports car" class="w-full h-full object-cover">-->
<!--        </div>-->

        <!-- Car Details -->
<!--        <div class="p-2">-->
            <!-- Car Name -->
<!--            <h2 class="text-2xl font-bold text-gray-800">Jeep Cherokee</h2>-->

            <!-- Car Type -->
<!--            <p class="text-gray-500 mb-2">SUV</p>-->

            <!-- Car Features -->
<!--            <div class="flex items-center justify-between mb-2">-->
<!--                <div class="flex items-center">-->
<!--                    <div class="rounded p-1">-->
<!--                        <i class="fas fa-suitcase-rolling fs-5 text-theme"></i>-->
<!--                    </div>-->
<!--                    <span>5 bags</span>-->
<!--                </div>-->

<!--                <div class="flex items-center">-->
<!--                    <div class="bg-white text-theme">-->
<!--                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"-->
<!--                            stroke="currentColor">-->
<!--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"-->
<!--                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />-->
<!--                        </svg>-->
<!--                    </div>-->
<!--                    <span>6 People</span>-->
<!--                </div>-->
<!--            </div>-->

            <!-- Quantity Selector -->
<!--            <div class="quantity-control">-->
<!--                <button class="quantity-btn">−</button>-->
<!--                <span class="quantity">2</span>-->
<!--                <button class="quantity-btn">+</button>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!--                    <div class="col-12 col-sm-6 col-md-4 col-lg-4">-->
    <!-- Header with "tion" text -->

    <!-- Car Card -->
<!--    <div class="bg-white p-2 rounded-lg overflow-hidden mb-4 shadow">-->
        <!-- Car Image -->
<!--        <div class="w-auto p-2">-->
<!--            <img src="https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8Y2FyfGVufDB8fDB8fHww"-->
<!--                alt="White sports car" class="w-full h-full object-cover">-->
<!--        </div>-->

        <!-- Car Details -->
<!--        <div class="p-2">-->
            <!-- Car Name -->
<!--            <h2 class="text-2xl font-bold text-gray-800">Jeep Cherokee</h2>-->

            <!-- Car Type -->
<!--            <p class="text-gray-500 mb-2">SUV</p>-->

            <!-- Car Features -->
<!--            <div class="flex items-center justify-between mb-2">-->
<!--                <div class="flex items-center">-->
<!--                    <div class="rounded p-1">-->
<!--                        <i class="fas fa-suitcase-rolling fs-5 text-theme"></i>-->
<!--                    </div>-->
<!--                    <span>5 bags</span>-->
<!--                </div>-->

<!--                <div class="flex items-center">-->
<!--                    <div class="bg-white text-theme">-->
<!--                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"-->
<!--                            stroke="currentColor">-->
<!--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"-->
<!--                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />-->
<!--                        </svg>-->
<!--                    </div>-->
<!--                    <span>6 People</span>-->
<!--                </div>-->
<!--            </div>-->

            <!-- Quantity Selector -->
<!--            <div class="quantity-control">-->
<!--                <button class="quantity-btn">−</button>-->
<!--                <span class="quantity">2</span>-->
<!--                <button class="quantity-btn">+</button>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
                </div>
            </section>
            <section class="addon-section">
                <div class="d-flex align-items-center justify-content-between mt-5 mb-4">
                    <h3 class="text-s sm:text-2xl section-title">Add ons</h3>
                    <a href="#" class="btn-theme">
                        View More
                        <i class="fa-arrow-right mx-2 fas"></i>
                    </a>
                </div>
                <div class="container-fluid">
    <div class="row d-flex" id="service-list">
        <!--<div class="card-container col-12 col-md-4 col-sm-6 mb-0 mb-sm-0 mt-sm-0 mt-2">-->
        <!--    <div class="card-body addon-card">-->
        <!--        <div class="flex">-->
                    <!-- Image section -->
        <!--            <div class="mr-4">-->
        <!--                <img src="https://plus.unsplash.com/premium_photo-1676999306178-60a9d07079bf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8Ymlla3xlbnwwfHwwfHx8MA%3D%3D"-->
        <!--                    alt="Bike Riding" class="card-image">-->
        <!--            </div>-->

                    <!-- Content section -->
        <!--            <div class="flex flex-col justify-between">-->
        <!--                <div>-->
        <!--                    <h3 class="title">Bike Riding</h3>-->
        <!--                    <p class="subtitle">Enjoy seaside cruising<br>while on trip</p>-->
        <!--                </div>-->

        <!--                <div>-->
        <!--                    <div class="flex items-baseline">-->
        <!--                        <span class="price">$400</span>-->
        <!--                        <span class="per-day">/day</span>-->
        <!--                    </div>-->

        <!--                    <div class="quantity-control">-->
        <!--                        <button class="quantity-btn">−</button>-->
        <!--                        <span class="quantity">2</span>-->
        <!--                        <button class="quantity-btn">+</button>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->

            <!-- Date selector -->
        <!--    <div class="date-selector px-4 pb-3">-->
        <!--        <div class="date-btn">21</div>-->
        <!--        <div class="date-btn">22</div>-->
        <!--        <div class="date-btn">23</div>-->
        <!--        <div class="date-btn">24</div>-->
        <!--        <div class="date-btn">25</div>-->
        <!--        <div class="date-btn">26</div>-->
        <!--        <div class="date-btn">27</div>-->
        <!--    </div>-->
        <!--</div>-->
        <!--<div class="card-container col-12 col-md-4 col-sm-6 mb-0 mb-sm-0 mt-sm-0 mt-2">-->
        <!--    <div class="card-body addon-card">-->
        <!--        <div class="flex">-->
                    <!-- Image section -->
        <!--            <div class="mr-4">-->
        <!--                <img src="https://images.unsplash.com/photo-1580636319416-bb90117ead47?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8Ymlla3xlbnwwfHwwfHx8MA%3D%3D"-->
        <!--                    alt="Bike Riding" class="card-image">-->
        <!--            </div>-->

                    <!-- Content section -->
        <!--            <div class="flex flex-col justify-between">-->
        <!--                <div>-->
        <!--                    <h3 class="title">Bike Riding</h3>-->
        <!--                    <p class="subtitle">Enjoy seaside cruising<br>while on trip</p>-->
        <!--                </div>-->

        <!--                <div>-->
        <!--                    <div class="flex items-baseline">-->
        <!--                        <span class="price">$400</span>-->
        <!--                        <span class="per-day">/day</span>-->
        <!--                    </div>-->

        <!--                    <div class="quantity-control">-->
        <!--                        <button class="quantity-btn">−</button>-->
        <!--                        <span class="quantity">2</span>-->
        <!--                        <button class="quantity-btn">+</button>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->

            <!-- Date selector -->
        <!--    <div class="date-selector px-4 pb-3">-->
        <!--        <div class="date-btn">21</div>-->
        <!--        <div class="date-btn">22</div>-->
        <!--        <div class="date-btn">23</div>-->
        <!--        <div class="date-btn">24</div>-->
        <!--        <div class="date-btn">25</div>-->
        <!--        <div class="date-btn">26</div>-->
        <!--        <div class="date-btn">27</div>-->
        <!--    </div>-->
        <!--</div>-->
        <!--<div class="card-container col-12 col-md-4 col-sm-6 mb-0 mb-sm-0 mt-sm-0 mt-2">-->
        <!--    <div class="card-body addon-card">-->
        <!--        <div class="flex">-->
                    <!-- Image section -->
        <!--            <div class="mr-4">-->
        <!--                <img src="https://plus.unsplash.com/premium_photo-1673637205535-9d5f386bfb7a?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8Ymlla3xlbnwwfHwwfHx8MA%3D%3D"-->
        <!--                    alt="Bike Riding" class="card-image">-->
        <!--            </div>-->

                    <!-- Content section -->
        <!--            <div class="flex flex-col justify-between">-->
        <!--                <div>-->
        <!--                    <h3 class="title">Bike Riding</h3>-->
        <!--                    <p class="subtitle">Enjoy seaside cruising<br>while on trip</p>-->
        <!--                </div>-->

        <!--                <div>-->
        <!--                    <div class="flex items-baseline">-->
        <!--                        <span class="price">$400</span>-->
        <!--                        <span class="per-day">/day</span>-->
        <!--                    </div>-->

        <!--                    <div class="quantity-control">-->
        <!--                        <button class="quantity-btn">−</button>-->
        <!--                        <span class="quantity">2</span>-->
        <!--                        <button class="quantity-btn">+</button>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->

            <!-- Date selector -->
        <!--    <div class="date-selector px-4 pb-3">-->
        <!--        <div class="date-btn">21</div>-->
        <!--        <div class="date-btn">22</div>-->
        <!--        <div class="date-btn">23</div>-->
        <!--        <div class="date-btn">24</div>-->
        <!--        <div class="date-btn">25</div>-->
        <!--        <div class="date-btn">26</div>-->
        <!--        <div class="date-btn">27</div>-->
        <!--    </div>-->
        <!--</div>-->
    </div>
    <!--<div class="per-service">-->
    <!--    <div class="table-responsive" id="service-per-service">-->
    <!--    </div>-->
    <!--  </div>-->
</div>

            </section>
        </div>
        <div class="m-lg-0 col-lg-4 col-md-10 offset-md-1 col-sm-10 offset-sm-1">
            <div class="mt-5 mt-sm-0 booking-summary">
    <h3 class="m-0 text-white fs-4">Booking Summary</h3>
    <div class="bg-white summary-details">
        <div class="detail-row">
            <div class="detail-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="w-100">
                <p class="detail-label">Duration:</p>
                <p class="detail-value"><?php 
                echo $_REQUEST['duration'];?></p>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-icon">
                <i class="far fa-calendar-check"></i>
            </div>
            <div class="w-100">
                <p class="detail-label">Selected Date:</p>
                <p class="detail-value"><?php 
                echo $_REQUEST['travel_date'];?></p>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="w-100">
                <p class="detail-label">No. Of Guest:</p>
                <p class="detail-value">2 Adults <span class="secondary-text">2 Child</span></p>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-icon">
                <i class="fas fa-car"></i>
            </div>
            <div class="w-100">
                <p class="detail-label">Car Selected:</p>
                <p class="detail-value">2 Sedan <span class="secondary-text">X 4 Seats</span></p>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-icon">
                <i class="fas fa-plane"></i>
            </div>
            <div class="w-100">
                <p class="detail-label">Flight:</p>
                <p class="detail-value">Indigo Airlines <span class="secondary-text">6E 2069</span></p>
                <div class="text-capitalize secondary-text">11:00am - 3:00 pm</div>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-icon">
                <i class="fa-solid fa-signs-post"></i>
            </div>
            <div class="w-100">
                <p class="detail-label">Add Ons:</p>
                <p class="detail-value">Bike <span class="secondary-text">x 2</span></p>
            </div>
        </div>

        <div class="pt-3 border-top detail-row">
            <div class="detail-icon">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div class="w-100">
                <p class="detail-label">Total Budget:</p>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="total-price">$ 2,30,345</div>
                </div>
                <div class="save-text">Book Now and Save $18.20</div>
            </div>
        </div>

        <div class="d-flex flex-column gap-3 mt-4">
            <button class="btn btn-lg btn-theme" id="book-now-btn">
                Book Now
            </button>

            <button id="openModalBtn" class="btn btn-link">Book Now Pay Later</button>
        </div>

        <div class="terms-text">
            By proceeding, I acknowledge that I have read and agree to Websites <a href="#" class="terms-link">Terms
                and
                Conditions</a> and <a href="#" class="terms-link">Privacy
                Statement</a>.
        </div>
    </div>
</div>
<script src="assets/trip-assets/js/script.js"></script>
            <input type="checkbox" id="modal-toggle" class="peer hidden" />
<div class="hidden peer-checked:flex modal-section">
    <div class="flex bg-white rounded-xl w-full max-w-4xl overflow-hidden">
        <!-- left content- Form -->
        <div class="relative space-y-6 bg-white shadow-lg p-6 w-full md:w-1/2">
            <!-- close button -->
            <label for="modal-toggle" class="cursor-pointer close-btn" aria-label="Close form">
                <i class="fa-solid fa-xmark"></i>
            </label>
            <!-- inputs to change tab -->
            <input type="radio" name="form-step" id="step1" class="peer/step1 hidden" checked />
            <input type="radio" name="form-step" id="agent" class="peer/agent hidden" />
            <input type="radio" name="form-step" id="step2" class="peer/step2 hidden" />
            <!-- USER FORM -->
            <div class="hidden peer-checked/step1:block space-y-4">
                <h2 class="text-center text-uppercase heading-theme">Book Now</h2>
                <form id="userBookingForm" novalidate>
                    <div class="space-y-4">
                        <div>
                            <label for="fullName" class="block mb-1 font-bold text-black">Full Name</label>
                            <div class="relative">
                                <i class="booking-modal-icon fa-solid fa-user" aria-hidden="true"></i>
                                <input type="text" id="fullName" name="fullName" placeholder="Your full name"
                                    class="py-2 pr-4 pl-10 rounded input" required />
                            </div>
                        </div>
                        <div>
                            <span class="block flex items-center gap-2 mb-1 font-bold text-gray-700">
                                <i class="fa-mars-stroke-right text-theme text-lg fa-solid"
                                    aria-hidden="true"></i>Gender
                            </span>
                            <div class="flex gap-3">
                                <label class="font-medium accent-purple-800">
                                    <input type="radio" name="gender" id="male" value="male" required /> Male
                                </label>
                                <label class="font-medium accent-purple-800">
                                    <input type="radio" name="gender" id="female" value="female" required /> Female
                                </label>
                                <label class="font-medium accent-purple-800">
                                    <input type="radio" name="gender" id="other" value="other" required /> Other
                                </label>
                            </div>
                        </div>
                        <div>
                            <label for="userEmail" class="block mb-1 font-bold text-black">Email</label>
                            <div class="relative">
                                <i class="booking-modal-icon fa-solid fa-envelope" aria-hidden="true"></i>
                                <input type="email" id="userEmail" name="email" placeholder="your@email.com"
                                    class="py-2 pr-4 pl-10 rounded input" required />
                            </div>
                        </div>
                        <div>
                            <label for="phoneNumber" class="block mb-1 font-bold text-black">Phone Number</label>
                            <div class="flex border border-gray-300 rounded overflow-hidden">
                                <select id="countryCode" name="countryCode" class="bg-white px-2 py-2 border-r"
                                    required>
                                    <option value="+91">+91</option>
                                    <option value="+1">+1</option>
                                    <option value="+44">+44</option>
                                </select>
                                <input type="tel" id="phoneNumber" name="phoneNumber" placeholder="Phone Number"
                                    pattern="[0-9]{10}" class="px-3 py-2 outline-none w-full" required />
                            </div>
                        </div>
                        <div class="flex justify-between gap-4 w-full">
                            <label for="agent" class="link-label">
                                <i class="mr-1 fa-solid fa-user-tie" aria-hidden="true"></i>Agent?
                            </label>
                            <label for="step2" class="rounded-2 btn-theme">
                                Next <i class="fa-arrow-right ml-2 fa-solid" aria-hidden="true"></i>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <!-- AGENT FORM -->
            <div class="hidden peer-checked/agent:block space-y-4">
                <h2 class="text-center text-uppercase heading-theme">
                    Agent Login
                </h2>
                <form id="agentLoginForm" novalidate>
                    <div class="space-y-4">
                        <div>
                            <label for="agentEmail" class="block mb-1 font-bold text-black text-sm">Email</label>
                            <div class="relative">
                                <i class="booking-modal-icon fa-solid fa-envelope" aria-hidden="true"></i>
                                <input type="email" id="agentEmail" name="agentEmail" placeholder="agent@email.com"
                                    class="py-2 pr-4 pl-10 rounded input" required />
                            </div>
                        </div>
                        <div>
                            <label for="agentPassword" class="block mb-1 font-bold text-black text-sm">Password</label>
                            <div class="relative">
                                <i class="booking-modal-icon fa-solid fa-lock" aria-hidden="true"></i>
                                <input type="password" id="agentPassword" name="agentPassword"
                                    placeholder="Your password" minlength="8" class="py-2 pr-4 pl-10 rounded input"
                                    required />
                            </div>
                        </div>
                        <label for="step2" class="p-2 rounded-2 w-full text-center btn-theme">
                            Next <i class="fa-arrow-right ml-2 fa-solid" aria-hidden="true"></i>
                        </label>
                        <div class="flex items-center gap-2 text-gray-400 text-sm">
                            <hr class="flex-grow border-gray-300" /> or
                            <hr class="flex-grow border-gray-300" />
                        </div>
                        <button type="button"
                            class="flex justify-center items-center gap-2 hover:bg-gray-100 py-2 border rounded w-full">
                            <img src="https://www.svgrepo.com/show/355037/google.svg" class="w-5 h-5"
                                alt="Google icon" />
                            Continue with Google
                        </button>
                        <label for="step1" class="text-center link-label">
                            <i class="fa-arrow-left mr-1 fa-solid" aria-hidden="true"></i> User?
                        </label>
                    </div>
                </form>
            </div>
            <!-- OTP FORM -->
            <div class="hidden peer-checked/step2:block space-y-4">
                <h6 class="text-center text-uppercase heading-theme">OTP Verification</h6>
                <form id="otpVerificationForm" novalidate>
                    <div class="space-y-4">
                        <label for="otpInput" class="block font-bold text-black">Enter OTP</label>
                        <input type="text" id="otpInput" name="otp" maxlength="6" pattern="[0-9]{6}"
                            class="px-4 py-2 rounded text-center tracking-widest input" placeholder="123456"
                            inputmode="numeric" required />
                        <div class="text-right">
                            <button type="button" class="link-label">
                                <i class="mr-1 fa-clock-rotate-left fa-solid" aria-hidden="true"></i>Resend OTP
                            </button>
                        </div>
                        <div class="flex justify-between gap-4 w-full">
                            <button type="button" id="back-btn"
                                class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded w-full font-semibold text-black text-center cursor-pointer">
                                <i class="fa-arrow-left mr-2 fa-solid" aria-hidden="true"></i>Back
                            </button>
                            <button type="submit" class="rounded-2 w-50 btn-theme">
                                <i class="mr-2 fa-solid fa-paper-plane" aria-hidden="true"></i>Register
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Right Content- Image -->
        <div class="hidden md:block relative md:w-1/2">
            <img src="https://navbharattours.com/wp-content/uploads/The-Benefits-of-Group-Tours-Why-Traveling-Together-Is-Better.png"
                alt="Group of travelers enjoying a tour together" class="z-0 w-full h-full object-cover" />
            <div
                class="z-10 absolute inset-0 flex flex-col justify-center items-center bg-black bg-opacity-50 p-8 text-white text-center">
                <h3 class="border-start-0 text-white section-title">Let the Journey Begin!, Travel in Style & Comfort
                </h3>
                <p>Book premium journeys made just for you.</p>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

<script src="assets/trip-assets/plugins/bootstrap/js/bootstrap.min.js"></script>

<script src="assets/trip-assets/plugins/slick-carousel/slick/slick.min.js"></script>

<!--<script src="assets/trip-assets/js/script.js"></script>-->

<script>
  var inclusive = [];
  var exclusive = [];


  function sendOTP() {
    let mobileNumber = document.getElementById("mobileNumber").value;

    if (mobileNumber.length !== 10 || isNaN(mobileNumber)) {
      showError("Please enter a valid 10-digit mobile number.");
      return;
    }

    hideError();

    $.ajax({
      url: 'ajax/send_otp.php',
      type: 'POST',
      data: {
        mobileNumber: mobileNumber
      },
      dataType: "json",
      success: function(response) {
        if (response.error) {
          showError(response.error);
        } else {
          document.getElementById("mobileNumber").setAttribute("disabled", true);
          document.getElementById("mobileSection").classList.add("d-none");
          document.getElementById("otpSection").classList.remove("d-none");
          startCountdown();
        }
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
        showError("Failed to send OTP. Please try again.");
      }
    });
  }

  function verifyOTP() {
    let otpInputs = document.querySelectorAll(".otp-input");
    let otp = "";

    otpInputs.forEach(input => otp += input.value);

    // Validate OTP length
    if (otp.length !== 6 || isNaN(otp)) {
      showError("Please enter a valid 6-digit OTP.");
      return;
    }

    hideError();

    $.ajax({
      url: 'ajax/verify_otp.php',
      type: 'POST',
      data: {
        otp: otp,
        mobileNumber: document.getElementById("mobileNumber").value
      },
      dataType: "json",
      success: function(response) {
        if (response.error) {
          showError(response.error);
        } else {
          // showSuccess("OTP verified successfully. Redirecting...");
          setTimeout(() => {
            location.reload();
          }, 4000); // Reload after 4 seconds
        }
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
        showError("Failed to verify OTP. Please try again.");
      }
    });
  }

  function startCountdown() {
    let time = 60;
    let countdownEl = document.getElementById("countdown");
    countdownEl.innerText = time;
    document.getElementById("resendOtp").classList.add("text-muted");

    let interval = setInterval(() => {
      time--;
      countdownEl.innerText = time;
      if (time === 0) {
        clearInterval(interval);
        document.getElementById("resendOtp").classList.remove("text-muted");
      }
    }, 1000);
  }

  function resendOTP() {
    document.getElementById("resendOtp").classList.add("text-muted");
    startCountdown();
  }

  function moveToNext(input, index) {
    let next = document.querySelectorAll(".otp-input")[index + 1];
    if (input.value && next) {
      next.focus();
    }
  }

  function switchTab(element) {
    document.querySelectorAll('.tab-button').forEach(button => {
      button.classList.remove('active', 'btn-primary');
      button.classList.add('btn-outline-primary');
    });

    element.classList.add('active', 'btn-primary');
    element.classList.remove('btn-outline-primary');
  }

  function showError(message) {
    let errorAlert = document.getElementById("errorAlert");
    errorAlert.innerText = message;
    errorAlert.classList.remove("d-none");
  }

  function hideError() {
    let errorAlert = document.getElementById("errorAlert");
    errorAlert.classList.add("d-none");
  }
  $(document).ready(function() {


    $(".nav-link").click(function(e) {
      e.preventDefault();
      var targetSection = $(this).data("target");
      console.log("Target section:", targetSection);

      if (!targetSection) {
        console.error("Error: Target section is undefined or empty");
        return;
      }

      var $target = $("." + targetSection);
      if ($target.length) {
        window.scrollTo(0, $target.offset().top - 20);
      } else {
        console.error("Error: Target section #" + targetSection + " not found in the DOM.");
      }
    });


    var initialDuration = $('#duration').val();


    function checkUrlAndFetchData() {
      const params = getUrlParams();
      if (params.package_id && params.package_name && params.package_category) {
          //alert(params.package_name);
          //alert(params.package_id);
        $("#package_name").text(params.package_name);
        $("#traveling_from").val(params.traveling_from).trigger('change');
        $("#destination").val(params.destination).trigger('change');
        $("#tour_start_date").val(params.travel_date);
        $("#pid").val(params.package_id);
        $("#duration").val(params.duration);

        
        fetchOverviewWithImage(params.package_id);
        
        itinerary_list();
        //alert("yee");
        setTimeout(function() {
            
          hotel_list();
        }, 10);
        taxi_list();
        service_list(params.package_id);
        document.querySelector('.dynamic_package').textContent = document.getElementById('duration').value;
        
        fetchTransport(params.package_id);
        fetchFlight(params.package_id, params.traveling_from, params.destination);
        
        // addExclusive(serviceList);
        inclusive = [];
        addInclusive("Hotel: " + params.package_category);
      } else {
        $("#traveling_from").val(params.traveling_from).trigger('change');
        $("#destination").val(params.destination).trigger('change');
        $("#tour_start_date").val(params.travel_date);
        $("#duration").val(params.duration);
        fetchPackages(params.duration, params.traveling_from, params.destination, params.travel_date);
      }
    }

    checkUrlAndFetchData();

    $('#searchBtn').click(function() {
      var duration = $('#duration').val();
      const newUrl = new URL(window.location.href);
      let guestName = $('input[name="guest_name"]').val();
      $("#guest_name").text(guestName);
      var traveling_from = $("#traveling_from").val();
      var destination = $("#destination").val();
      var travel_date = $("#tour_start_date").val();
      newUrl.searchParams.set('duration', duration);
      newUrl.searchParams.set('traveling_from', traveling_from);
      newUrl.searchParams.set('destination', destination);
      newUrl.searchParams.set('travel_date', travel_date);

      window.history.pushState({}, '', newUrl);
      fetchPackages(duration, traveling_from, destination, travel_date);
    });

    $('#closeCustomModal').click(function() {

      console.log('Close button clicked');
      $('#customModalOverlay').fadeOut();
      $('#customModal').fadeOut();
    });
    $('#closeCustomModalFooter').click(function() {
      console.log('Close footer button clicked');
      $('#customModalOverlay').fadeOut();
      $('#customModal').fadeOut();
    });
    $('#submitPackageSelection').click(function() {
      console.log('Select Package button clicked');
      $('#customModalOverlay').fadeOut();
      $('#customModal').fadeOut();
    });



    function fetchPackages(duration, traveling_from, destination, travel_date) {
      $.ajax({
        url: 'ajax/package_list.php',
        type: 'POST',
        data: {
          duration: duration,
          traveling_from: traveling_from,
          destination: destination,
          travel_date: travel_date
        },
        success: function(data) {
          $('#package_list').html(data);
          $('#customModalOverlay').fadeIn();
          $('#customModal').fadeIn();
        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
        }
      });
    }


    // Define transport capacity limits
    const transportCapacity = {
      tempo: 20,
      coach: 40,
      fortuner: 15,
      cryista: 7,
      innova: 7,
      zyalo_ertiga: 6,
      eco: 4
    };

    // Function to update transport count
    // function updateCount(button, change) {

    //   calculateTotal(transportPrice, change);
    // }


    // Ensure only one transport is selected at a time
    // Handle transport unchecking (reset quantity & price)
    $(document).on('click', '.check', function() {
      if (!$(this).is(':checked')) {
        let transportPrice = parseFloat($(this).val()) || 0;
        let currentQuantity = parseInt($(this).closest('.transport-col').find('input[type="text"]').val()) || 0;

        // Properly subtract the price based on quantity before resetting
        let totalReduction = transportPrice * currentQuantity;

        $(this).closest('.transport-col').find('input[type="text"]').val(0);

        calculateTotal(-totalReduction, 1);
      }
    });


    // Handle transport unchecking (reset quantity & price)
    $(document).on('click', '.check', function() {
      if (!$(this).is(':checked')) {
        let transportPrice = parseFloat($(this).val()) || 0;
        let currentQuantity = parseInt($(this).closest('.transport-col').find('input[type="text"]').val()) || 0;
        $(this).closest('.transport-col').find('input[type="text"]').val(0);
        calculateTotal(-transportPrice * currentQuantity, 1);
      }
    });

    // Handle quantity increase/decrease
    // $(document).on('click', '.transport-col button', function() {
    //   updateCount(this, $(this).text() === '+' ? 1 : -1);
    // });
  });

  document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const selectedTourType = urlParams.get('tour-type'); // Ensure correct parameter name
    const selectedDuration = urlParams.get('duration');

    console.log('Duration:', selectedDuration);
    console.log('Tour Type:', selectedTourType);

    // Set the selected tour type
    if (selectedTourType) {
      // Find the radio button by value instead of id
      const radioButton = document.querySelector(`input[name="tour-type"][value="${selectedTourType}"]`);
      console.log('Tour Type:', radioButton);

      if (radioButton) {
        radioButton.checked = true;
      } else {
        console.warn('No matching radio button found for:', selectedTourType);
      }
    }

    // Set the selected duration
    if (selectedDuration) {
      const decodedDuration = decodeURIComponent(selectedDuration);
      const durationDropdown = document.getElementById('duration');
      if (durationDropdown) {
        const optionToSelect = Array.from(durationDropdown.options).find(option => option.value.trim() === decodedDuration.trim());
        if (optionToSelect) {
          optionToSelect.selected = true;
        } else {
          console.warn('No matching option found for duration:', decodedDuration);
        }
      }
    }
  });

  function getUrlParams() {
    const params = new URLSearchParams(window.location.search);
    return {
      duration: params.get('duration'),
      package_id: params.get('package_id'),
      package_name: params.get('package_name'),
      package_category: params.get('package_category'),
      destination: params.get('destination'),
      traveling_from: params.get('traveling_from'),
      "travel_date": params.get('travel_date'),
    };
  }

  function fetchFlight(package_id, traveling_from, destination) {
    $.ajax({
      url: 'ajax/flight_list.php',
      type: 'POST',
      data: {
        package_id: package_id,
        traveling_from: traveling_from,
        destination: destination
      },
      success: function(data) {
        $('.flight-section').html(data);
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  }

  function hideFlightSection() {
    $('.flight-section').hide();
    $('.flight-listing').hide();

    return true;
  }
  $(document).on('click', '.search-btn', function() {
    var from = $('#flight_from').val(); // Fetch 'From' value
    var to = $('#flight_destination').val(); // Fetch 'To' value
    var depart = $('#departure_date').val(); // Fetch Departure Date
    var arrival = $('#return_date').val(); // Fetch Travellers & Cabin

    $.ajax({
      url: 'ajax/flight_list_search.php',
      type: 'POST',
      data: {
        from: from,
        to: to,
        depart: depart,
        arrival: arrival
      },
      success: function(data) {
        if (data.trim() !== "") {
          $('.flight-listing').html(data); // Show flights if available
        } else {
          $('.flight-listing').html('<p class="text-danger">No flights available for the selected criteria.</p>');
        }
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  });


  function setPackageId(package_id, packageName, destination, traveling_from) {
    $('input[name="package_id"]').val(package_id)
    $("#package_name").text(packageName);
    $("#traveling_from").val(traveling_from).trigger('change');
    $("#destination").val(destination).trigger('change');



    document.querySelector('.dynamic_package').textContent = document.getElementById('duration').value;

    const newUrl = new URL(window.location.href);
    newUrl.searchParams.set('package_id', package_id);
    newUrl.searchParams.set('package_name', packageName);
    newUrl.searchParams.set('destination', destination ? destination : ''); // Handle NULL values
    newUrl.searchParams.set('traveling_from', traveling_from ? traveling_from : ''); // Handle NULL values

    window.history.pushState({}, '', newUrl);

    setTimeout(function() {
      itinerary_list();
      fetchOverviewWithImage(package_id);
      fetchTransport(package_id);
      fetchFlight(package_id, traveling_from, destination);
    }, 10);

  }


  function setCategory(category, package_id) {
    if ($('input[name="package_id"]').val() == package_id) {
      $('input[name="category"]').val(category)
      const newUrl = new URL(window.location.href);
      newUrl.searchParams.set('package_category', category);
      window.history.pushState({}, '', newUrl);
      inclusive = [];
      addInclusive("Hotel: " + category);
      setTimeout(function() {
        hotel_list();
      }, 10);
      calculateTotal(0, 0);
    } else {
      alert("Please select package name")
    }
  }
  // Fetch Overview and Images for Selected Package
  function fetchOverviewWithImage(packageId) {
     // alert("packageId");
    $.ajax({
      url: 'ajax/fetchOverviewWithImage.php',
      type: 'POST',
      data: {
        package_id: packageId
      },
      dataType: 'json',
      success: function(response) {
          //alert(response);
         // alert("packageId");
        if (response.success) {
          // Update the Overview Section
          $('.overview-section').html(response.overviewHtml);
        
          // Update the Gallery Images Section
          $('.gallery-images').html(response.galleryHtml);
        } else {
          console.error('Error:', response.message);
        }
      },
      error: function(xhr, status, error) {
        console.error('Error fetching overview:', error);
      }
    });
  }

  function fetchTransport(packageId) {
    $.ajax({
      url: 'ajax/transport-list.php',
      type: 'POST',
      data: {
        package_id: packageId
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          $('.select-transport').html(response.transportHtml);
        } else {
          console.error('Error:', response.message);
          $('.select-transport').html(`<p class="error">${response.message}</p>`);
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', error);
        $('.select-transport').html('<p class="error">Failed to fetch transport options. Please try again.</p>');
      }
    });
  }








  function calculateTourEndDate() {
    const startDate = $('input[name="tour_start_date"]').val();
    const string = $("#duration").val();
    const pattern = /(\d+)\s*Nights?\s*(\d+)\s*Days?/i;
    const matches = string.match(pattern);

    nights = parseInt(matches[1], 10);
    const startDateTime = new Date(startDate);
    startDateTime.setDate(startDateTime.getDate() + nights);
    const endDate = startDateTime.toISOString().split('T')[0];
    $('input[name="tour_end_date"]').val(endDate);
  }

  function itinerary_list() {
    let tour_date = $('input[name="tour_start_date"]').val()

    let package_id = $('input[name="package_id"]').val(); // Declare package_id first
        //alert(tour_date);
        //alert(package_id);
    if (!package_id) {
      const params = getUrlParams();
      package_id = params.package_id; // Assign it instead of re-declaring with let
    }

    

    $.ajax({
      url: 'ajax/itinerary_list.php',
      type: 'POST',
      data: {
        package_id: package_id,
        tour_date: tour_date
      },
      success: function(data) {
         // alert(data);
        let dataArr = data.split("98230948klasd908809230894")
        $('#itinerary-list').html(dataArr[0]);
        $('#fixed-service').html(dataArr[1]);
        //  if (package_id) {
      //service_list(package_id);
    //  }
        setTimeout(function() {
          calculateTotal(0, 0);
        }, 2)
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  }

  function service_list(package_id) {
    let tour_date = $('input[name="tour_start_date"]').val();
    //alert(package_id);
    //let days = parseInt($("#duration").val().match(/\d+/)[0]);
    let days = "6 Nights 7 Days";
   // alert(days);
    if ($('input[name="tour_start_date"]').val() != '' &&
      days != "") {
      $.ajax({
        url: 'ajax/newservice_list.php',
        type: 'POST',
        data: {
          days: days,
          tour_date: tour_date,
          package_id: package_id
        },
        success: function(data) {
            //alert(data);
          let dataArr = data.split("98230948klasd908809230894")
          $('#service-list').html(dataArr[0]);
          $('#service-per-service').html(dataArr[1]);
        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
        }
      });
    }
  }

  function package_other_details(package_id, category) {
    $.ajax({
      url: 'ajax/package_other_details.php',
      type: 'POST',
      data: {
        package_id: package_id,
        category: category
      },
      success: function(data) {
        $('#package-other-details').html(data);
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
        $('#package-other-details').html(error);
      }
    });
  }

  function hotel_list() {
    let tour_date = $('input[name="tour_start_date"]').val()
    const params = new URLSearchParams(window.location.search);
    let package_id = params.get('package_id') || $('input[name="package_id"]').val();
    let category = params.get('package_category') || $('input[name="category"]').val();

// alert(tour_date);
// alert(package_id);
// alert(category);
    package_other_details(package_id, category)
    $.ajax({
      url: 'ajax/newhotel_list.php',
      type: 'POST',
      data: {
        package_id: package_id,
        tour_date: tour_date,
        category: category
      },
      success: function(data) {
        console.log(data);
        $('#hotel-list').html(data);
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  }
  function taxi_list() {
    let tour_date = $('input[name="tour_start_date"]').val()
    const params = new URLSearchParams(window.location.search);
    let package_id = params.get('package_id') || $('input[name="package_id"]').val();
    let category = params.get('package_category') || $('input[name="category"]').val();

// alert(tour_date);
// alert(package_id);
// alert(category);
    package_other_details(package_id, category)
    $.ajax({
      url: 'ajax/newtaxi_list.php',
      type: 'POST',
      data: {
        package_id: package_id,
        tour_date: tour_date,
        category: category
      },
      success: function(data) {
        console.log(data);
        $('#newtaxi-list').html(data);
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  }

  function handleIncrement() {
    const input = this.parentElement.querySelector('.quantity');
    input.value = parseInt(input.value) + 1;
    console.log('Updated Value:', input.value, 'Name:', input.name);
    console.log('Updated Value:', input.value, 'Name:', input.name);

    updateSummary(input.name, input.value);
    calculateTotal(0, 0);
  }

  function handleDecrement() {
    const input = this.parentElement.querySelector('.quantity');
    if (parseInt(input.value) > 0) {
      input.value = parseInt(input.value) - 1;
      console.log('Updated Value:', input.value, 'Name:', input.name);
      updateSummary(input.name, input.value);
      calculateTotal(0, 0);
    }
  }

  document.addEventListener('click', function(event) {
    if (event.target.classList.contains('increment')) {
      handleIncrement.call(event.target);
    }
  });

  document.addEventListener('click', function(event) {
    if (event.target.classList.contains('decrement')) {
      handleDecrement.call(event.target);
    }
  });

  function updateSummary(fullName, value) {
    const match = fullName.match(/\[(.*?)\]/);
    if (!match) return;
    const name = match[1].trim();
    const mapping = {
      "TWIN": "twin",
      "CWB": "cwb",
      "CNB": "cnb",
      "TRIPLE": "triple",
      "SINGLE": "single",
      "QUAD SHARING": "quad-sharing"
    };

    if (mapping[name.toUpperCase()]) {
      const element = document.getElementById(mapping[name.toUpperCase()]);
      const parent = element.closest('.single');
      element.textContent = value;
      if (value > 0) {
        parent.classList.remove('d-none');
      } else {
        parent.classList.add('d-none');
      }
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    function updateMaxPersons() {
      const selectedTransport = $(this).find('option:selected').data('trans');
      $(this).closest('.transport-row').find('.max-persons').text(selectedTransport);
      $(this).closest('.transport-row').find('.num-persons-select').empty();
      for (let i = 0; i <= 5; i++) {
        $(this).closest('.transport-row').find('.num-persons-select').append(`<option value="${i}">${i}</option>`);
      }
    }

    function removeAllBelowElement() {
      $('.transport-row:not(:first)').remove();
    }

    // Initial setup for first row
    // $('.transportation-select').each(updateMaxPersons);

    // Add more transportation option
    // $('#addMoreTransport').click(function(e) {
    //   e.preventDefault();
    //   const newRow = $('.transport-row:first').clone();
    //   newRow.find('.transportation-select').val('Select Transport');
    //   newRow.find('.num-persons-select').val('Select Person');
    //   newRow.find('.max-persons').empty();
    //   newRow.insertAfter('.transport-row:last');
    //   newRow.find('.transportation-select').each(updateMaxPersons);
    // });

    // Event delegation for dynamically added elements
    // $('.table').on('change', '.transportation-select', updateMaxPersons);
    // $('.table').on('change', '.transportation-select:first', removeAllBelowElement);

    //bike section hide and show
    const bikeCheckbox = document.getElementById('bike');
    const bikeDetailsSection = document.getElementById('enter-bike-details-section');
    const bikeDetails = document.getElementById('enter-bike-details');
    bikeDetailsSection.style.display = 'none';
    bikeDetails.style.display = 'none';
    bikeCheckbox.addEventListener('change', function() {
      if (this.checked) {
        bikeDetailsSection.style.display = 'block';
        bikeDetails.style.display = 'block';
      } else {
        bikeDetailsSection.style.display = 'none';
        bikeDetails.style.display = 'none';
      }
    });

  });

  function calculateTotal(pricePerUnit, change) {
    console.log('inclusive', inclusive);
    var permit_amount = 0;
    var guide_amount = 0;
    const packageDetails = document.querySelectorAll('#package-other-details .col-md');
    const targetTableBody = document.querySelector('#final_quotation tbody');
    console.log('packageDetails', packageDetails);
    let totalAmount = 0;
    let total_per_person = 0;
    const existingRows = targetTableBody.querySelectorAll('tr:not(:first-child)');
    existingRows.forEach(row => row.remove());
    const rowData = {
      items: [],
      totalMember: 0
    };
    const transportDetails = document.querySelectorAll('.transport-main .col-md');
    const transportData = {
      items: [],
      totalMember: 0
    };
    transportDetails.forEach(detail => {
      const label = detail.querySelector('.form-label').textContent;
      const price = parseFloat(detail.querySelector('input').dataset.amount);
      const quantity = parseInt(detail.querySelector('input').value);
      const total = price * quantity;
      //totalPax = totalPax + quantity;
      if (quantity > 0) {
        const newRowData = {
          label: label,
          price: price,
          total: total,
          quantity: quantity
        };
        transportData.items.push(newRowData);
      }
    });

    packageDetails.forEach(detail => {
      const label = detail.querySelector('.form-label').textContent;
      const price = parseFloat(detail.querySelector('input').dataset.amount);
      const quantity = parseInt(detail.querySelector('input').value);
      const total = price * quantity;
      //totalPax = totalPax + quantity;
      let h_pax = quantity;

      switch (label.trim()) {

        case 'TWIN':
          h_pax = (quantity * 2);
          break;
        case 'TRIPLE':
          h_pax = (quantity * 3);
          break;
        case 'QUAD SHARING':
          h_pax = (quantity * 4);
          break;
        default:
          h_pax = quantity;
          break;

      }
      if (quantity > 0) {
        const newRowData = {
          label: label,
          price: price,
          h_pax: h_pax,
          total: total,
          quantity: quantity
        };
        rowData.items.push(newRowData);
        rowData.totalMember += h_pax;

      }
    });
    //Extra Services
    //permit
    let totalPax = rowData.totalMember;
    let permitElement = document.getElementById("permit");
    if (permitElement) {
      if (permitElement.checked) {
        permit_amount = permitElement.getAttribute("data-permit");
        permit_amount = parseInt(permit_amount * totalPax);
        total_per_person = total_per_person + parseInt((permit_amount / totalPax));
      }
    }
    //guide
    let guideElement = document.getElementById("guide");
    if (guideElement) {
      if (guideElement.checked) {
        guide_amount = guideElement.getAttribute("data-guide");
        total_per_person = total_per_person + parseInt((guide_amount / totalPax));
      }
    }

    //services
    const serviceDetails = {};
    document.querySelectorAll('#service-list input[type="checkbox"]').forEach(function(checkbox) {
      if (checkbox.checked) {
        const serviceName = checkbox.closest('tr').querySelector('label').textContent.trim();

        const amount = parseFloat(checkbox.getAttribute('amount-cumulative') || checkbox.getAttribute('amount-per-person'));
        const date = checkbox.value;
        let service_type = ""
        if (checkbox.getAttribute('amount-cumulative')) {
          service_type = "Cumulative"
        } else if (checkbox.getAttribute('amount-per-person')) {
          service_type = "Per Person"
        } else if (checkbox.getAttribute('amount-per-service')) {
          service_type = "Per Service"
        }

        if (serviceDetails.hasOwnProperty(serviceName)) {
          serviceDetails[serviceName].quantity += 1;
        } else {
          serviceDetails[serviceName] = {
            amount: amount,
            quantity: 1,
            total: amount,
            type: service_type
          };
        }
      } else {
        const serviceName = checkbox.closest('tr').querySelector('label').textContent.trim();
        addExclusive(serviceName);
      }
    });

    for (const serviceName in serviceDetails) {
      if (serviceDetails.hasOwnProperty(serviceName)) {
        const {
          amount,
          quantity,
          type
        } = serviceDetails[serviceName];

        if (type == "Cumulative") {
          total_per_person = total_per_person + ((amount * quantity) / totalPax);
        } else if (type == "Per Person") {
          total_per_person = total_per_person + (amount * quantity);
        }
        addInclusive(serviceName);
      }
    }


    // Service Per Service
    document.querySelectorAll('.per-service-input input[type="number"]').forEach(input => {
      const label = input.closest('.single').querySelector('.per-service-label label').textContent.trim();
      const amount = parseFloat(input.getAttribute('amount-per-service')) || 0; // Ensure a valid number
      const quantity = parseInt(input.value) || 0; // Ensure a valid number
      const pax = totalPax || 1;

      if (quantity > 0) {
        addInclusive(label); // Add to the inclusive list
        total_per_person += (amount * quantity) / pax; // Calculate price per person safely
        console.log('total_per_person', total_per_person);
      } else {
        addExclusive(label); // Remove from inclusive list
      }
    });




    // let totalPaxVal = parseInt($('#summary-no-of-pax').text()) || 0;
    // let previousTotalPrice = parseFloat($('#summary-calculated-price').text().replace('₹', '')) || 0;


    // totalAmount = previousTotalPrice + (pricePerUnit * change);


    // total_per_person = totalPaxVal > 0 ? (totalAmount / totalPaxVal).toFixed(2) : 0;
    // $('#summary-calculated-price').text(`₹${newTotalPrice.toFixed(2)}`);
    // $('#per-person-calculated-price').text(`₹${perPersonPrice}`);


    // const driverTableBody = document.querySelector('#driver_list tbody');
    // const driverDetails = <?= $json_vehicle ?>;
    // // const existingDriver = driverTableBody.querySelectorAll('tr');
    // existingDriver.forEach(row => row.remove());
    // // const transportationSelects = document.querySelectorAll('.transportation-select');
    // var trans_inclusive = '';
    // transportationSelects.forEach(select => {
    //   const detailId = 'detail_' + select.value.replace(' / ', '_');

    //   if (document.getElementById(detailId)) {
    //     const label = select.value;
    //     selectedOption = select.options[select.selectedIndex];
    //     const trans = selectedOption.getAttribute('data-trans');

    //     const amount = parseFloat(document.getElementById(detailId).value);
    //     const quantity = 1; // Quantity is always 1
    //     const total = amount * quantity;
    //     // Append to the table
    //     total_per_person = total_per_person + ((amount * quantity) / totalPax)
    //     trans_inclusive = trans_inclusive + label + "-" + trans + ", ";

    //     let driver_name = driverDetails[label].driver_name;
    //     let mobile = driverDetails[label].mobile;
    //     const driverRow = document.createElement('tr');
    //     driverRow.innerHTML = `
    //         <td>${label}</td>
    //         <td>${quantity}</td>
    //        <!-- <td>${driver_name}</td>
    //         <td>${mobile}</td>-->
    //         <td>Pending</td>
    //         <td>Pending</td>
    //     `;
    //     driverTableBody.appendChild(driverRow);

    //   }
    // });

    // if (trans_inclusive != '') {
    //   addInclusive("Transportation: " + trans_inclusive.replace(/, $/, ''));
    // }
    // if ($('input[name="category"]').val()) {
    //   addInclusive("Hotel: " + $('input[name="category"]').val());
    // }





    // let input = $(button).siblings('input');
    // let checkbox = $(button).closest('.transport-col').find('input[type="checkbox"]');

    // let totalPax = parseInt($('#summary-no-of-pax').text()) || 0;
    // if (totalPax === 0) {
    //   alert("⚠️ Please select the number of guests first!");
    //   return;
    // }

    // if (!checkbox.is(':checked')) {
    //   alert("⚠️ Please select a transport option first!");
    //   return;
    // }

    // let currentValue = parseInt(input.val()) || 0;
    // let transportType = checkbox.attr('name').replace('transport[', '').replace(']', '');
    // let transportPrice = parseFloat(checkbox.val()) || 0;
    // let newValue = currentValue + change;
    // if (newValue < 0) newValue = 0;
    // if (newValue > transportCapacity[transportType]) {
    //   alert(`🚨 Max capacity for ${transportType} is ${transportCapacity[transportType]}.`);
    //   return;
    // }

    // input.val(newValue);

    // // If count becomes 0, uncheck the transport checkbox automatically
    // if (newValue === 0) {
    //   checkbox.prop('checked', false);
    // }

    // Get Bike Details
    const numberOfBike = document.querySelector('input[name="number_of_bike"]')?.value || null;
    const mechanic = document.querySelector('select[name="mechanic"]')?.value || null;
    const marshal = document.querySelector('select[name="marshal"]')?.value || null;
    const fuel = document.querySelector('select[name="fuel"]')?.value || null;
    const backup = document.querySelector('select[name="backup"]')?.value || null;


    // Retrieve the values of the inputs
    const numberOfBikePrice = parseInt(numberOfBike) * parseInt(<?php echo Bike ?>);
    const mechanicPrice = mechanic == 'Yes' ? parseInt(<?php echo Mechanic ?>) : 0;
    const marshalPrice = marshal == 'Yes' ? parseInt(<?php echo Marshal ?>) : 0;
    const fuelPrice = fuel == 'Yes' ? parseInt(<?php echo Fuel ?>) : 0;
    const backupPrice = backup == 'Yes' ? parseInt(<?php echo Backup ?>) : 0;
    //document.getElementById('bike').checked
    const total_bike_price = (numberOfBikePrice + mechanicPrice + marshalPrice + fuelPrice + backupPrice)

    transportData.items.forEach(function(item) {
      let price = item.price + total_per_person;
      let h_pax = item.quantity;
      let total = price * item.quantity;
      let per_person_pr = item.price;

      switch (item.label.trim()) {
        case 'coach':

          total = price * item.quantity;
          per_person_pr = total / h_pax;
          break;
        case 'tempo':

          total = price * item.quantity;
          per_person_pr = total / h_pax;
          break;
        case 'fortuner':

          total = price * item.quantity;
          per_person_pr = total / h_pax;
          break;
        case 'innova':

          total = price * item.quantity;
          per_person_pr = total / h_pax;
          break;
        case 'zyalo_ertiga':

          total = price * item.quantity;
          per_person_pr = total / h_pax;
          break;
        case 'eco':

          total = price * item.quantity;
          per_person_pr = total / h_pax;
          break;
      }

      const newRow = document.createElement('tr');
      //     newRow.innerHTML = `
      //   <td>${item.label}</td>
      //   <td>${round(per_person_pr)}</td>
      //   <td>${h_pax}</td>
      //   <td>${round(total)}</td>
      // `;
      //     targetTableBody.appendChild(newRow);

      console.log('before', totalAmount);
      totalAmount += total;
      console.log('after', totalAmount);
    });

    console.log('rowData.items', rowData.items);
    rowData.items.forEach(function(item) {
      let price = item.price + total_per_person;
      let h_pax = item.quantity;
      let total = price * item.quantity;
      let per_person_pr = item.price;
      if ("TWIN" == item.label.trim()) {
        price = item.price + (total_per_person * 2)
        h_pax = item.quantity * 2;
        total = price * item.quantity;
        per_person_pr = (total / h_pax);
      } else if ("TRIPLE" == item.label.trim()) {
        price = item.price + (total_per_person * 3)
        h_pax = item.quantity * 3;
        total = price * item.quantity;
        per_person_pr = (total / h_pax);
      } else if ("QUAD SHARING" == item.label.trim()) {
        price = item.price + (total_per_person * 4)
        h_pax = item.quantity * 4;
        total = price * item.quantity;
        per_person_pr = (total / h_pax);
      }
      const newRow = document.createElement('tr');
      newRow.innerHTML = `
                <td>${item.label}</td>
                <td>${round(per_person_pr)}</td>
                <td>${h_pax}</td>
                <td>${round(total)}</td>
            `;
      targetTableBody.appendChild(newRow);
      console.log('before', totalAmount);

      totalAmount += total;
      console.log('before', totalAmount);

    });
    //totalMember


    // jeett prajapati (running)
    // if (document.getElementById('bike').checked) {
    //   const newRow = document.createElement('tr');
    //   newRow.innerHTML = `
    //             <td>Bike</td>
    //             <td>${(total_bike_price/ numberOfBike).toFixed(2)}</td>
    //             <td>${ numberOfBike}</td>
    //             <td>${total_bike_price.toFixed(2)}</td>
    //         `;
    //   targetTableBody.appendChild(newRow);
    //   totalAmount += total_bike_price;
    // }






    // Hotel List
    /*
    const hotelList = document.getElementById('hotel-list');
    hotelList.querySelectorAll('tr').forEach(row => {

      const nightInput = row.querySelector('input[name="hotel_night[]"]');
      const amountInput = row.querySelector('input[name="hotel_amount[]"]');
      const nameInput = row.querySelector('input[name="hotel_name[]"]');

      const night = nightInput.value;
      const amount = amountInput.value;
      const name = nameInput.value;
      const total = amount * night;
      totalAmount = totalAmount + total;
      const newRow = document.createElement('tr');
      newRow.innerHTML = `
            <td>${name}</td>
            <td>${amount}</td>
            <td>${night}</td>
            <td>${total}</td>
        `;
      targetTableBody.appendChild(newRow);
    });
*/
    // Add total rows
    var finalPrice = totalAmount;
    var igstPrice = round((totalAmount * 0.025));
    var sgstPrice = round((totalAmount * 0.025));
    finalPrice = round((finalPrice + igstPrice + sgstPrice));

    //Transportation
    // Update total amount based on change (increment/decrement)

    // let previousTotalPrice = parseFloat($('#summary-calculated-price').text().replace('₹', '')) || 0;

    // if (previousTotalPrice > 0) {
    //   // Adjust final price dynamically based on increment or decrement
    //   finalPrice = previousTotalPrice + (change > 0 ? pricePerUnit : -pricePerUnit);
    // }
    // Update final price based on change

    // Prevent negative total price


    const totalRows = `
        <tr>
            <td></td>
            <td colspan="2">Total Amount Excluding GST</td>
            <td>${totalAmount}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2">IGST 2.5%</td>
            <td>${igstPrice}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2">SGST 2.5%</td>
            <td>${sgstPrice}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2" class="dark-col"><strong>Total Amount Including GST</strong></td>
            <td>${finalPrice}</td>
        </tr>
    `;
    $('input[name="total_amount"]').val(finalPrice);
    $('input[name="without_gst"]').val(totalAmount);
    $('input[name="total_pax"]').val(totalPax);
    calculateTourEndDate();
    targetTableBody.insertAdjacentHTML('beforeend', totalRows);

    var exclusiveList = document.getElementById('exclusive');
    exclusiveList.innerHTML = '';
    exclusive.forEach(function(value) {
      var li = document.createElement('li');
      li.className = 'add-items';
      li.textContent = value;
      exclusiveList.appendChild(li);
    });


    var inclusiveList = document.getElementById('inclusive');

    inclusiveList.innerHTML = '';
    console.log('inclusiveList', inclusive);
    inclusive.forEach(function(value) {
      var li = document.createElement('li');
      li.className = 'add-items';
      li.textContent = value;
      inclusiveList.appendChild(li);
    });

    $('input[name="exclusive"]').val(JSON.stringify(exclusive));
    $('input[name="inclusive"]').val(JSON.stringify(inclusive));


    document.getElementById('summary-duration').innerHTML = document.getElementById('duration').value;

    document.getElementById('summary-travel-date').innerHTML = $('input[name="tour_start_date"]').val();
    document.getElementById('summary-no-of-pax').innerHTML = totalPax;
    document.getElementById('summary-calculated-price').innerHTML = '₹' + finalPrice;
    document.getElementById('per-person-calculated-price').innerHTML = '₹' + round(parseInt(finalPrice / totalPax));
  }

  function round(number) {
    return Math.round(number * 100) / 100;
  }

  function addExclusive(value) {
    if (!exclusive.includes(value)) {
      exclusive.push(value);
    }
    var index = inclusive.indexOf(value);
    if (index !== -1) {
      inclusive.splice(index, 1);
    }

  }

  function addInclusive(value) {
    if (!inclusive.includes(value)) {
      inclusive.push(value);
    }
    // alert(inclusive);
    var index = exclusive.indexOf(value);
    if (index !== -1) {
      exclusive.splice(index, 1);
    }
  }


  // calculateTotal();
  /*
document.addEventListener('input', function(event) {
    if (event.target.classList.contains('quantity')) {
        calculateTotal();
    }
});
*/
</script>

</body>

</html>