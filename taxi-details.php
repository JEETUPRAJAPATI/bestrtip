<?php
session_start();
require_once './config/config.php';
require_once 'includes/agent_header.php';
// is_agent_login();
$edit = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data_to_store = array_filter($_POST);
  $save_data = [];
  $save_data["traveling_from"] = $data_to_store['traveling_from'];
  $save_data["duration"] = $data_to_store['duration'];
  $save_data["destination"] = $data_to_store['destination'];
  $save_data["tour_start_date"] = $data_to_store['tour_start_date'];
  $save_data["passenger"] = $data_to_store['passenger'];
//   $save_data["your_budget"] = $data_to_store['your_budget'];
//   $save_data["cumulative"] = json_encode($data_to_store['cumulative'] ?? []);
//   $save_data["per_person"] = json_encode($data_to_store['per_person'] ?? []);
//   $save_data["per_service"] = json_encode($data_to_store['per_service'] ?? []);
//   $save_data["person"] = json_encode($data_to_store['person'] ?? []);
//   $save_data["transport"] = json_encode($data_to_store['transport'] ?? []);
//   $save_data["permit"] = $data_to_store['permit'] ?? "off";
//   $save_data["guide"] = $data_to_store['guide'] ?? "off";
//   $save_data["created_by"] = $_SESSION['user_id'];
//   $save_data["updated_by"] = $_SESSION['user_id'];
//   $save_data["inclusive"] = !empty($data_to_store['inclusive']) ? $data_to_store['inclusive'] : json_encode([]);
//   $save_data["exclusive"] = !empty($data_to_store['exclusive']) ? $data_to_store['exclusive'] : json_encode([]);
//   $save_data["total_amount"] = $data_to_store['total_amount'];
//   $save_data["without_gst"] = $data_to_store['without_gst'];
//   $save_data["total_pax"] = $data_to_store['total_pax'];
//   $save_data["tour_end_date"] = $data_to_store['tour_end_date'];

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
$destinations = $db->get("destination", null, 'id, name');
$traveling_from = $db->get("traveling_from", null, 'id, name');
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

  <!-- bootstrap.min css -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <!-- Icon Font Css -->
  <link rel="stylesheet" href="assets/trip-assets/plugins/themify/css/themify-icons.css">
  <link rel="stylesheet" href="assets/trip-assets/plugins/fontawesome/css/all.css">
  <!-- <link rel="stylesheet" href="plugins/magnific-popup/dist/magnific-popup.css"> -->
  <!-- <link rel="stylesheet" href="plugins/modal-video/modal-video.css"> -->
  <link rel="stylesheet" href="assets/trip-assets/plugins/animate-css/animate.css">
  <!-- Slick Slider  CSS -->
  <link rel="stylesheet" href="assets/trip-assets/plugins/slick-carousel/slick/slick.css">
  <link rel="stylesheet" href="assets/trip-assets/plugins/slick-carousel/slick/slick-theme.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="assets/trip-assets/js/taxi-itenary.js"></script>
  <script src="assets/trip-assets/js/taxi-selection.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>
  <!-- Main Stylesheet -->
  <link rel="stylesheet" href="assets/trip-assets/css/style.css">

</head>

<body>
<div class="bg-gray-100">
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
        <h1 class="hero-title text-center">Taxi Details</h1>
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
                <form action="taxi-details.php" method="get">
                <div class="row mt-2 g-1 search-form-controls">
                    <div class=" col-sm-12 col-md-8 offset-md-2 col-lg-3 offset-lg-0">
                        <label class="form-label">Duration</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-map-marker-alt text-secondary"></i>
                            </span>
                            
                            <select class="form-select" name="duration" id="duration" required>
                    <option value="" disabled selected>Choose...</option>
                    <option value="1 Nights 2 Days" <?php echo (  $_REQUEST['duration'] == "1 Nights 2 Days") ? 'selected' : '' ?>>1 Nights 2 Days</option>
                    <option value="2 Nights 3 Days" <?php echo ( $_REQUEST['duration'] == "2 Nights 3 Days") ? 'selected' : '' ?>>2 Nights 3 Days</option>
                    <option value="3 Nights 4 Days" <?php echo ( $_REQUEST['duration'] == "3 Nights 4 Days") ? 'selected' : '' ?>>3 Nights 4 Days</option>
                    <option value="4 Nights 5 Days" <?php echo (  $_REQUEST['duration'] == "4 Nights 5 Days") ? 'selected' : '' ?>>4 Nights 5 Days</option>
                    <option value="5 Nights 6 Days" <?php echo ( $_REQUEST['duration'] == "5 Nights 6 Days") ? 'selected' : '' ?>>5 Nights 6 Days</option>
                    <option value="6 Nights 7 Days" <?php echo (  $_REQUEST['duration'] == "6 Nights 7 Days") ? 'selected' : '' ?>>6 Nights 7 Days</option>
                    <option value="7 Nights 8 Days" <?php echo (  $_REQUEST['duration'] == "7 Nights 8 Days") ? 'selected' : '' ?>>7 Nights 8 Days</option>
                    <option value="8 Nights 9 Days" <?php echo (  $_REQUEST['duration'] == "8 Nights 9 Days") ? 'selected' : '' ?>>8 Nights 9 Days</option>
                    <option value="9 Nights 10 Days" <?php echo ( $_REQUEST['duration'] == "9 Nights 10 Days") ? 'selected' : '' ?>>9 Nights 10 Days</option>
                    <option value="10 Nights 11 Days" <?php echo ( $_REQUEST['duration'] == "10 Nights 11 Days") ? 'selected' : '' ?>>10 Nights 11 Days</option>
                    <option value="11 Nights 12 Days" <?php echo (  $_REQUEST['duration'] == "11 Nights 12 Days") ? 'selected' : '' ?>>11 Nights 12 Days</option>
                    <option value="12 Nights 13 Days" <?php echo ( $_REQUEST['duration'] == "12 Nights 13 Days") ? 'selected' : '' ?>>12 Nights 13 Days</option>
                    <option value="13 Nights 14 Days" <?php echo ( $_REQUEST['duration'] == "13 Nights 14 Days") ? 'selected' : '' ?>>13 Nights 14 Days</option>
                    <option value="14 Nights 15 Days" <?php echo (  $_REQUEST['duration'] == "14 Nights 15 Days") ? 'selected' : '' ?>>14 Nights 15 Days</option>
                    <option value="15 Nights 16 Days" <?php echo ( $_REQUEST['duration'] == "15 Nights 16 Days") ? 'selected' : '' ?>>15 Nights 16 Days</option>
                    <option value="16 Nights 17 Days" <?php echo ( $_REQUEST['duration'] == "16 Nights 17 Days") ? 'selected' : '' ?>>16 Nights 17 Days</option>
                    <option value="17 Nights 18 Days" <?php echo (  $_REQUEST['duration'] == "17 Nights 18 Days") ? 'selected' : '' ?>>17 Nights 18 Days</option>
                    <option value=">18 Nights 19 Days" <?php echo (  $_REQUEST['duration'] == ">18 Nights 19 Days") ? 'selected' : '' ?>>18 Nights 19 Days</option>
                    <option value="19 Nights 20 Days" <?php echo ( $_REQUEST['duration'] == "19 Nights 20 Days") ? 'selected' : '' ?>>19 Nights 20 Days</option>
                  </select>
                        </div>
                    </div>
                    <div class=" col-sm-12 col-md-8 offset-md-2 col-lg-3 offset-lg-0">
                        <label class="form-label">From</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-map-marker-alt text-secondary"></i>
                            </span>
                           <select id="traveling_from" name="traveling_from">
                    <option value="">From</option>
                    <?php foreach ($traveling_from as $location): ?>
                      <option value="<?= $location['id']; ?>" <?= ( $_REQUEST['traveling_from'] == $location['id']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($location['name']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>

                        </div>
                    </div>
                    <div class=" col-sm-12 col-md-8 offset-md-2 col-lg-2 offset-lg-0">
                        <label class="form-label">To</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-map-marker-alt text-secondary"></i>
                            </span>
                            <select id="destination" name="destination">
                                <option value="">To</option>
                                <?php foreach ($destinations as $destination): ?>
                                  <option value="<?= $destination['id']; ?>" <?= ( $_REQUEST['destination'] == $destination['id']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($destination['name']); ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>

                        </div>
                    </div>
                    <div class="col-md col-sm-12 col-md-8 offset-md-2 col-lg-2 offset-lg-0">
                        <label class="form-label">Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-calendar text-theme"></i>
                            </span>
                            <!--<input type="text" id="datepicker-end" class="date" readonly="readonly">-->
                            <input id="calendar" type="date" name="tour_start_date" onChange="return itinerary_list()" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="col-md col-sm-12 col-md-8 offset-md-2 col-lg-3 offset-lg-0">
                        <label class="form-label">passenger</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-user text-secondary"></i>
                            </span>
                            <input type="text" class="form-control bg-transparent" value="2 adults, 2 children"
                                readonly>
                            <span class="input-group-text bg-white">
                                <i class="fas fa-chevron-down text-secondary"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-8 offset-md-2 col-lg-auto offset-lg-0">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-theme text-white w-100 text-center">
                            <i class="fas fa-search me-1"></i>
                            Search
                        </button>
                    </div>
                </div>

                <!-- Room Selector -->
                
                    <div class="row w-100 room-selector justify-content-center justify-content-sm-around">
                        <div class="room-card ms-2">
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


                        <div class="room-card ms-2">
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

                        <div class="room-card ms-2">
                            <div class="room-type triple w-100">
                                <i>
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
                                Extra Adult
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

                        <div class="room-card ms-2">
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
                  
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('mobile-menu-button').addEventListener('click', function () {
        const menu = document.getElementById('mobile-menu');
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
        } else {
            menu.classList.add('hidden');
        }
    });

</script>

</div>
<div class="container-fluid bg-gray-100">
    <div class="row">
        <div class="col-lg-9">
            <h3 class="section-title mb-4">Itinerary</h3>
<div class="container bg-white rounded-4 border-2 ps-3 ms-2">

    <!-- Itinerary Container -->
    <div class="itinerary-container row" id="itinerary">
        <?php
// session_start();
// require_once '../config/config.php';
// require_once BASE_PATH . '/includes/auth_validate.php';
$db = getDbInstance();
//echo $_REQUEST['destination'];
$db->where('destination', $_REQUEST['destination']);
$db->where('traveling_from', $_REQUEST['traveling_from']);
$db->where('duration', $_REQUEST['duration']);
$taxi = $db->getOne("taxi");
//print_r($taxi);
$taxi_id = $taxi['id'];
//die;
$db = getDbInstance();
$db->where('taxi_id', $taxi_id);
$db->where('itineary', ['TWIN Fixed', 'CWB Fixed', 'CNB Fixed', 'TRIPLE Fixed', 'SINGLE Fixed', 'QUAD SHARING Fixed'], "NOT IN");
$results = $db->get("taxi_details");
//print_r($results);
$tour_date =  date('d-m-Y', strtotime($_REQUEST['tour_date']));
foreach ($results as $key => $result):

?>

<input type="hidden" name="taxi_id" id="pid" value="<?php echo $taxi_id;?>">
    <div class="itinerary-day col-12 completed-day bg-gray-100 text-muted">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <h3 class="section-title me-3 fs-5">Select Hotel</h3>
                        <div class="d-flex gap-2">
                            <button class="btn rounded-3 rating-btn text-theme hover:bg-purple-900 hover:text-white"
                                onclick="selectRating(this)" disabled>
                                <i class="fas fa-star"></i> 2+
                            </button>
                            <button class="btn rounded-3 rating-btn text-theme hover:bg-purple-900 hover:text-white"
                                onclick="selectRating(this)" disabled>
                                <i class="fas fa-star"></i> 3+
                            </button>
                            <button class="btn rounded-3 rating-btn text-theme hover:bg-purple-900 hover:text-white"
                                onclick="selectRating(this)" disabled>
                                <i class="fas fa-star"></i> 4+
                            </button>
                            <button class="btn rounded-3 rating-btn text-theme hover:bg-purple-900 hover:text-white"
                                onclick="selectRating(this)" disabled>
                                <i class="fas fa-star"></i> 5
                            </button>
                        </div>
                    </div>
                    <!-- Alpine.js for Carousel Functionality -->

<div class="card rounded-lg overflow-hidden shadow-md  p-0">
    <div class="p-3">
        <div class="flex flex-col md:flex-row">
            <!-- Carousel Section -->
            <div class="hotel-card-img relative mb-1 sm:mb-0">
                <div x-data="{ 
                    activeSlide: 0, 
                   slides: [
    'https://plus.unsplash.com/premium_photo-1661929519129-7a76946c1d38?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OXx8aG90ZWx8ZW58MHx8MHx8fDA%3D',
    'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTh8fGhvdGVsfGVufDB8fDB8fHww',
    'https://images.unsplash.com/photo-1660557989725-f511e9fa6267?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8aG90ZWwlMjBsb2JieXxlbnwwfHwwfHx8MA%3D%3D'
]
                }" class="relative w-full h-48 sm:w-48 sm:h-48">

                    <div class="overflow-hidden relative w-full h-full">
                        <template x-for="(slide, index) in slides" :key="index">
                            <img :src="slide"
                                class="absolute top-0 left-0 w-full h-full object-cover rounded-md transition-opacity duration-500 ease-in-out"
                                :class="activeSlide === index ? 'opacity-100' : 'opacity-0'" x-cloak>
                        </template>
                    </div>

                    <!-- Indicators -->
                    <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-2">
                        <template x-for="(slide, index) in slides" :key="index">
                            <button @click="activeSlide = index"
                                class="w-3 h-3 rounded-full bg-gray-400 hover:bg-gray-600"
                                :class="{'bg-gray-900': activeSlide === index}"></button>
                        </template>
                    </div>

                    <!-- Navigation Arrows -->
                    <button @click="activeSlide = (activeSlide === 0 ? slides.length - 1 : activeSlide - 1)"
                        class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-30 text-white p-1 rounded-full text-xs">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button @click="activeSlide = (activeSlide === slides.length - 1 ? 0 : activeSlide + 1)"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-30 text-white p-1 rounded-full text-xs">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <div class="w-full md:w-1/2 md:pl-4 md:mt-0">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-800">
                            The Grand Paris Hotel
                        </h3>
                        <p class="text-theme text-base  fs-5">
                            $150
                        </p>
                        <span class="text-yellow-500 ">★★★</span>
                    </div>
                </div>

                <p class="text-gray-600 text-sm flex items-center mb-1">
                    <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>
                    Paris, France
                </p>

                <!-- Price -->

                <div class="flex flex-wrap text-sm text-gray-600 space-y-1 md:space-y-0">
                    <div class="pt-1 flex items-center w-full md:w-auto mr-4">
                        <i class="fas fa-utensils mr-2 text-gray-500"></i>
                        Breakfast Included
                    </div>
                    <div class="pt-1 flex items-center w-full md:w-auto mr-4">
                        <i class="fas fa-clock mr-2 text-gray-500"></i>
                        3 days 2 nights
                    </div>
                    <div class="pt-1 flex items-center w-full md:w-auto mr-4">
                        <i class="fas fa-bed mr-2 text-gray-500"></i>
                        2 beds
                    </div>
                    <div class="pt-1 flex items-center w-full md:w-auto">
                        <i class="fas fa-door-open mr-2 text-gray-500"></i>
                        3 rooms
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="flex justify-end mb-1 me-4">
        <button
            class=" text-theme py-1 me-3 rounded-md hover:text-purple-900 transition duration-300 ease-in-out hover:translate-x-2">
            <i class=" fa-solid fa-pencil"></i>
            Change Hotel
        </button>
        <button class="btn-theme text-white px-4 py-1 rounded-md hover:bg-purple-700">
            Select
        </button>
    </div>
    <!-- Hotel Status -->
    <div class="flex bg-green-100">
        <div class="w-1/2 py-2 px-4 text-gray-600">Hotel Status</div>
        <div class="w-1/2 py-2 px-4 text-right text-purple-800">In Enquiry</div>
    </div>
</div>
                    <!-- Hotel card would be included here -->
                </div>
                <div class="col-md-6 mt-4 p-5">
                    <div class="">
                        <h2 class="fs-2 fw-bold">Day <?= $key + 1 ?> <?= date('l', strtotime($tour_date)) ?></h2>
                        <h3 class="mt-2 fs-3 font-semibold"><?= $tour_date ?></h3>
                        <span class="badge bg-secondary">Completed</span>
                    </div>
                    <h3 class="fs-4 mb-2 font-medium">
                        Tower of London Tour
                    </h3>
                    <p class="text-base font-weight-semibold">
                       <?= $result['itineary'] ?>
                    </p>
                </div>
            </div>
        </div>
<?php
    $tour_date = addOneDay($tour_date);
endforeach;
?>
      
    </div>

    <!-- Review Button -->
    <div class="text-end py-4">
        <button id="reviewBtn" class="btn btn-theme rounded-1 btn-lg">
            <i class="fas fa-star me-2"></i>Rate Your Experience
        </button>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-theme ">
                <h5 class="modal-title text-white" id="reviewModalLabel">Rate Your Experience</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    <!-- Star Rating -->
                    <div class="mb-4 text-center">
                        <h6 class="mb-3">How would you rate your experience?</h6>
                        <div class="star-rating">
                            <i class="far fa-star fa-2x star-item" data-rating="1"></i>
                            <i class="far fa-star fa-2x star-item" data-rating="2"></i>
                            <i class="far fa-star fa-2x star-item" data-rating="3"></i>
                            <i class="far fa-star fa-2x star-item" data-rating="4"></i>
                            <i class="far fa-star fa-2x star-item" data-rating="5"></i>
                            <input type="hidden" name="rating" id="rating-value" value="0">
                        </div>
                    </div>

                    <!-- Issue Category -->
                    <div class="mb-3">
                        <label for="issueCategory" class="form-label">If you experienced any issues, please select the
                            category:</label>
                        <select class="form-select" id="issueCategory" name="issueCategory">
                            <option value="">Select an issue (if applicable)</option>
                            <option value="timing">Timing Issues</option>
                            <option value="food">Food/Refreshments</option>
                            <option value="transportation">Transportation</option>
                            <option value="accommodation">Accommodation</option>
                            <option value="guide">Tour Guide</option>
                            <option value="information">Information Provided</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <!-- Comments -->
                    <div class="mb-3">
                        <label for="reviewComments" class="form-label">Please share your experience or any issues in
                            detail:</label>
                        <textarea class="form-control" id="reviewComments" name="comments" rows="4"
                            placeholder="Tell us about your experience..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-theme rounded-1" id="submitReview">Submit Review</button>
            </div>
        </div>
    </div>
</div>
            <div class="mb-4  mt-5 d-flex justify-content-between align-items-center">
                <h3 class="section-title sm:text-2xl">Select taxi</h3>
                <a href="#" class=" btn-theme ">
                    View More
                    <i class="fas fa-arrow-right mx-2"></i>
                </a>
            </div>

            <div class="row" id="newtaxi-list">

            </div>
            <div class="mb-4  mt-5 d-flex justify-content-between align-items-center">
                <h3 class="section-title sm:text-2xl">Add Ons</h3>
                <a href="#" class=" btn-theme ">
                    View More
                    <i class="fas fa-arrow-right mx-2"></i>
                </a>
            </div>

            <div class="container-fluid">
    <div class="row d-flex" id="service-list">
       
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Add active class to first date button
    document.addEventListener('DOMContentLoaded', function () {
        const dateButtons = document.querySelectorAll('.date-btn');
       // dateButtons[0].classList.add('active');

        // Add click event to all date buttons
        dateButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Remove active class from all buttons
                dateButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
            });
        });
    });
</script>
        </div>
        <div class="col-lg-3 ms-0">
            <div class="card shadow-sm rounded-lg bg-white w-full max-w-md relative p-6">
    <div class="timeline-line"></div>
    <div class="timeline-dot dot-pickup"></div>
    <div class="timeline-dot dot-dropoff"></div>

    <div class="mb-8 ml-10">
        <h2 class="text-theme font-medium text-xl mb-1">Pick-up</h2>
        <p class="text-gray-500 mb-0">10:00:00, March 31, 2025</p>
        <p class="text-gray-500">Orlando International Airport (MCO)</p>
    </div>

    <div class="ml-10">
        <h2 class="text-theme font-medium text-xl mb-1">Drop-off</h2>
        <p class="text-gray-500 mb-0">10:00:00, March 31, 2025</p>
        <p class="text-gray-500">Orlando International Airport (MCO)</p>
    </div>
</div>
<div class="taxi-price-overview mt-5">
    <!-- Header -->
    <div class="booking-summary mt-5 mt-sm-0">
        <h3 class="m-0 text-white fs-4">Price Details</h3>
    </div>

    <!-- Card Body -->
    <div class="price-body">
        <!-- Car Rental Fee Section -->
        <h2 class="pb-2 font-semibold fs-4 mb-3">Hi, Guest</h2>
        <div class="flex items-start mb-4">
            <span class="mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"
                    class="text-gray-600">
                    <path
                        d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7z" />
                    <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1z" />
                </svg>
            </span>
            <div>
                <span class="text-gray-500 text-lg">Car Rental Fee:</span>
                <div class="text-gray-700 text-2xl font-medium">MYR 781.63</div>
                <div class="text-gray-500 text-sm">Approx. MYR 260.54×3 Days</div>
            </div>
        </div>

        <!-- Bullet Points -->
        <div class="mt-6 ps-4">
            <div class="list-item">

                <div class="text-gray-500">Unlimited Mileage</div>
            </div>

            <div class="list-item">
                <div class="text-gray-500">Full to Full</div>
            </div>

            <div class="list-item">
                <div class="text-gray-500">Taxes and fees (including airport tax, customer facility fee, tourism
                    tax,
                    and sales tax)</div>
            </div>

            <div class="list-item">
                <div class="text-gray-500">Basic Rental Fee</div>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Total Budget -->
        <div class="flex items-start justify-between">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"
                    class="text-gray-600 mr-2">
                    <path
                        d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z" />
                    <path
                        d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0zM7 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z" />
                </svg>
                <span class="text-gray-600 text-lg font-medium">Total Budget:</span>
            </div>
            <div class="text-right">
                <div class="text-gray-700 text-2xl font-medium">MYR 858.12</div>
                <div class="text-gray-500 text-sm">Prepay Online MYR 111.93</div>
                <div class="text-gray-500 text-sm">Pay At Pick-Up Approx. MYR 746.20</div>
            </div>
        </div>

        <!-- Button and Footer -->
        <button class="book-now-btn mt-6">Book Now</button>
        <div class="footer-text text-gray-700">
            By proceeding, I acknowledge that I have read and agree to Websites <a href="#" class="footer-link">Terms
                and Conditions</a> and <a href="#" class="footer-link">Privacy Statement</a>.
        </div>
    </div>
</div>
        </div>
    </div>
</div>
<input id="calendar" type="hidden" name="tour_start_date" onChange="return itinerary_list()" value="<?= $_REQUEST['tour_start_date']; ?>">
<script>
      function taxi_list() {
    let tour_date = $('input[name="tour_start_date"]').val()
    const params = new URLSearchParams(window.location.search);
    let package_id = params.get('taxi_id') || $('input[name="taxi_id"]').val();
    //let category = params.get('package_category') || $('input[name="category"]').val();

// alert(tour_date);
// alert(package_id);
// alert(category);
   // package_other_details(package_id, category)
    $.ajax({
      url: 'ajax/newtaxi_list.php',
      type: 'POST',
      data: {
        package_id: package_id,
        tour_date: tour_date
      },
      success: function(data) {
       // console.log(data);
        $('#newtaxi-list').html(data);
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  }
function service_list() {
    let tour_date = $('input[name="tour_start_date"]').val();
     const params = new URLSearchParams(window.location.search);
    let package_id = params.get('taxi_id') || $('input[name="taxi_id"]').val();
    //alert(package_id);
    //let days = parseInt($("#duration").val().match(/\d+/)[0]);
    let days = params.get('duration');
   // alert(days);
    if ($('input[name="tour_start_date"]').val() != '' &&
      days != "") {
      $.ajax({
        url: 'ajax/newtaxiservice_list.php',
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
$(document).ready(function() {
    taxi_list();
    service_list();
});
</script>