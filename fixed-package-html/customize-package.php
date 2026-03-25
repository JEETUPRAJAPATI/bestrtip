<?php
session_start();
require_once './../config/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$edit = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data_to_store = array_filter($_POST);
  $db = getDbInstance();
  $db->where('mobile', $data_to_store['mobile']);
  $data = $db->getOne("agents");
  if (isset($data['id']) && !empty($data['id'])) {
    $_SESSION['user_logged_in'] = TRUE;
    $_SESSION['admin_type'] = $data['type'];
    $_SESSION['user_id'] = $data['id'];
    $_SESSION['full_name'] = $data['full_name'];
    $_SESSION['user_name'] = $data['user_name'];
  } else {
    $db = getDbInstance();
    // $saveData['password'] = password_hash($data_to_store['password'], PASSWORD_DEFAULT);
    $saveData['status'] = 'Active';
    $saveData['email_id'] = $data_to_store['email'];
    $saveData['full_name'] = $data_to_store['name'];
    $saveData['mobile'] = $data_to_store['mobile'];
    $saveData['type'] = 'User';
    // print_r($saveData);
    $last_id = $db->insert('agents', $saveData);
    // echo ' llllllllllll '.$last_id;

    $_SESSION['user_logged_in'] = TRUE;
    $_SESSION['admin_type'] = 'User';
    $_SESSION['user_id'] = $last_id;
    $_SESSION['full_name'] = $data_to_store['name'];
    $_SESSION['user_name'] = null;
  }

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
  if (headers_sent($file, $line)) {
    die("Headers already sent in $file on line $line");
  }
  $_SESSION['success'] = "The query has been generated successfully.";
  header("Location: https://agent.go2ladakh.in/agent_query_edit.php?ID=" . encryptId($inserted_id));
  exit;
}


// exit;
$db = getDbInstance();
$vehicles = $db->get("vehicles", null, 'driver_name, vehicle_number, mobile, vehicle_type');
$vehicleData = [];
foreach ($vehicles as $vehicle) {
  $vehicleData[$vehicle['vehicle_type']] = $vehicle;
}
$json_vehicle = json_encode($vehicleData);
// print_r($json_vehicle);exit;
?>


<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fixed Package</title>
  <link rel="profile" href="https://gmpg.org/xfn/11" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&family=Mulish:wght@300;400;600&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
  <link href="css/style.min.css" rel="stylesheet" />
  <link href="css/page.min.css" rel="stylesheet" />

  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
  <!-- Fonts -->

  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/core.css" />
  <link rel="stylesheet" href="../assets/css/front.min.css" />
  <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" />


  <link href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css' rel='stylesheet'>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    .searching-main-box .search-panel .search-form .row .submitButton {
      border: none;
      outline: none;
      font-size: 18px;
      border: solid 2px #005078;
      background-color: #005078;
      color: #fff;
      padding: 10px 30px;
      font-family: "Manrope", sans-serif;
      font-weight: 600;
      font-optical-sizing: auto;
      font-style: normal;
      border-radius: 10px;
      cursor: pointer;
      margin-left: auto;
    }

    .verification-popup {
      top: 0;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(0, 0, 0, 0.8);
      position: absolute;
      z-index: 101;
      padding-left: 10px;
      padding-right: 10px;
      box-sizing: border-box;
      display: none;
      font-family: "Mulish", sans-serif;
    }

    .verification-popup.show {
      display: block;
    }

    .verification-popup .highlighter-box {
      max-width: 500px;
      min-height: 300px;
      background: #fff;
      position: relative;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      border-radius: 20px;
      color: #000;
    }

    .verification-popup .highlighter-box .user-type-btn {
      margin-bottom: 30px;
    }

    .verification-popup .highlighter-box .user-type-btn .block {
      margin: 0 auto;
      max-width: 240px;
      border-radius: 50px;
      height: 35px;
      border: solid 2px #696cff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      overflow: hidden;
    }

    .verification-popup .highlighter-box .user-type-btn .block .guest-user {
      width: 50%;
      text-align: center;
      font-size: 14px;
      height: 100%;
      line-height: 30px;
      cursor: pointer;
      font-weight: 600;
    }

    .verification-popup .highlighter-box .user-type-btn .block .agent-login {
      width: 50%;
      text-align: center;
      font-size: 14px;
      line-height: 30px;
      font-weight: 600;
      height: 100%;
      cursor: pointer;
    }

    .verification-popup .highlighter-box .user-type-btn .block .guest-user.active,
    .verification-popup .highlighter-box .user-type-btn .block .agent-login.active {
      background: #696cff;
      color: #fff;
    }

    .verification-popup .highlighter-box .close-btn {
      width: 20px;
      height: 20px;
      position: absolute;
      right: 10px;
      top: 20px;
      cursor: pointer;
    }

    .verification-popup .highlighter-box .close-btn::before,
    .verification-popup .highlighter-box .close-btn::after {
      width: 15px;
      height: 2px;
      background: #000;
      position: absolute;
      content: '';
      transform: rotate(45deg);
      top: 10px;
    }

    .verification-popup .highlighter-box .close-btn::after {
      width: 15px;
      height: 2px;
      background: #000;
      position: absolute;
      content: '';
      transform: rotate(-45deg);
    }

    .verification-popup .highlighter-box h2 {
      text-align: center;
      font-size: 28px;
      padding: 30px 20px;
      padding-bottom: 10px;
      color: #000;
    }

    .verification-popup .highlighter-box form {
      margin: 0;
      padding: 0;
    }

    .verification-popup .highlighter-box .single {
      padding: 0 20px;
      margin-bottom: 24px;
    }

    .verification-popup .highlighter-box .single label {
      display: block;
      font-family: 'Mulish', sans-serif;
      font-size: 14px;
      text-transform: uppercase;
      margin-bottom: 5px;
      color: #000;
    }

    .verification-popup .highlighter-box .single input {
      width: 100%;
      height: 40px;
      padding: 2px 5px;
      font-size: 16px;
      font-family: 'Mulish', sans-serif;
      outline: none;
      border: none;
      border: solid 1px #ccc;
      color: #000;
    }

    .verification-popup .highlighter-box .single.btn {
      text-align: left;
      padding-bottom: 30px;
    }

    .verification-popup .highlighter-box .single.btn button {
      background: #696cff;
      border: solid 2px #696cff;
      color: #fff;
      box-shadow: 0 0.125rem 0.25rem 0 rgba(105, 108, 255, 0.4);
      padding: 10px 20px;
      cursor: pointer;
      text-transform: uppercase;
      font-size: 14px;
      font-family: 'Mulish', sans-serif;
    }
  </style>

</head>

<body>
  <div class="App">
    <div class="app-container">
      <header>
        <div class="block">
          <div class="logo">
            <a href="/"><img src="./images/logo.png" alt="Go2Ladakh "></a>
          </div>
          <div class="toggle-btn visible-xs-ib">
            <span></span>
            <span></span>
            <span></span>
          </div>
          <div class="trans-bg" id="transBg"></div>
          <div class="nav" id="nav">
            <div class="close-btn" id="closeBtn"></div>
            <nav>
              <ul>
                <li><a href="/" class="">Home</a></li>
                <li class="menu-item-has-children"><a href="#" class="">Packages</a>
                  <ul class="multi-col">
                    <li>
                      <h3>Tours by Themes</h3>
                      <ul>
                        <li><a href="/trekking-in-ladakh/">Trekking in Ladakh</a></li>
                        <li><a href="/leh-ladakh-bike-trip/">Leh Ladakh Bike Trip</a></li>
                        <li><a href="/ladakh-road-trips/">Leh Ladakh Road Trip</a></li>
                      </ul>
                    </li>
                    <li>
                      <h3>Tours by Season</h3>
                      <ul>
                        <li><a href="/summer-packages/">Summer Packages</a></li>
                        <li><a href="/winter-packages/">Winter Packages</a></li>
                      </ul>
                    </li>
                    <li>
                      <h3>Top Destination</h3>
                      <ul>
                        <li><a href="/leh-ladakh-tour-packages/">Leh Ladakh Tour Packages</a></li>
                        <li><a href="/kashmir-tour-packages/">Kashmir Tour Packages</a></li>
                        <li><a href="/himachal-tour-packages/">Himachal Tour Packages</a></li>
                        <li><a href="/bhutan-tour-packages/">Bhutan Tour Packages</a></li>
                        <li><a href="/north-east-tour-packages/">Northeast Tour Packages</a></li>
                      </ul>
                    </li>
                    <li>
                      <h3>Tours by Style</h3>
                      <ul>
                        <li><a href="/group-tours/">Group Tours</a></li>
                        <li><a href="/tailor-made-tours/">Tailor Made Tours</a></li>
                      </ul>
                    </li>
                  </ul>
                </li>
                <li class="menu-item-has-children"><a href="/ladakh-travel-guide" class="">Travel Guide</a>
                  <ul class="multi-col multi-col-3">
                    <li>
                      <h3><a href="/places-to-visit-in-ladakh/">Places to Visit in Ladakh</a></h3>
                      <ul>
                        <li><a href="/place-detail/pangong-lake/">Pangong Lake</a></li>
                        <li><a href="/place-detail/nubra-valley/">Nubra Valley</a></li>
                        <li><a href="/place-detail/leh-palace/">Leh Palace</a></li>
                        <li><a href="/place-detail/alchi-monastery/">Alchi Monastery</a></li>
                        <li><a href="/place-detail/diskit-monastery/">Diskit Monastery</a></li>
                        <li><a href="/place-detail/lamayuru-monastery/">Lamayuru Monastery</a></li>
                        <li><a href="/place-detail/thiksey-monastery/">Thiksey Monastery</a></li>
                        <li><a href="/place-detail/tso-moriri/">TSO Moriri</a></li>
                        <li><a href="/place-detail/zanskar-valley/">Zanskar Valley</a></li>
                        <li><a href="/place-detail/lakes-in-ladakh/">Lakes in Ladakh</a></li>
                        <li><a href="/blogs/hemis-national-park/">Hemis National Park</a></li>
                      </ul>
                    </li>
                    <li>
                      <h3><a href="/things-to-do-in-ladakh/">Things to Do in Ladakh</a></h3>
                      <ul>
                        <li><a href="/things-to-do-in-ladakh/ladakh-sightseeing/">Ladakh Sightseeing</a></li>
                        <li><a href="/things-to-do-in-ladakh/shopping-in-ladakh/">Shopping in Ladakh</a></li>
                        <li><a href="/things-to-do-in-ladakh/trekking-in-ladakh/">Trekking in Ladakh</a></li>
                        <li><a href="/things-to-do-in-ladakh/river-rafting-in-ladakh/">River Rafting in Ladakh</a></li>
                        <li><a href="/things-to-do-in-ladakh/festivals-in-ladakh/">Festival in Ladakh</a></li>
                        <li><a href="/things-to-do-in-ladakh/meditation-in-ladakh/">Meditation in Ladakh</a></li>
                        <li><a href="/things-to-do-in-ladakh/marathon-in-ladakh/">Ladakh Marathon 2019</a></li>
                        <li><a href="/things-to-do-in-ladakh/local-food/">Ladakh Local Food</a></li>
                        <li><a href="/things-to-do-in-ladakh/local-homestays-in-ladakh/">Local Home Visit in Ladakh</a></li>
                      </ul>
                    </li>
                    <li>
                      <h3>Ladakh Travel Information</h3>
                      <ul>
                        <li><a href="/guide-detail/best-time-to-visit-ladakh/">Best Time to Visit Ladakh</a></li>
                        <li><a href="/guide-detail/transport-in-ladakh/">Transport in Ladakh</a></li>
                        <li><a href="/guide-detail/how-to-reach-ladakh/">How to Reach ladakh</a></li>
                        <li><a href="/guide-detail/best-hotels-in-ladakh/">Best Hotels in ladakh</a></li>
                        <li><a href="/guide-detail/restaurants-in-ladakh/">Restaurant in ladakh</a></li>
                        <li><a href="/guide-detail/shopping-in-ladakh/">Shopping in Ladakh</a></li>
                        <li><a href="/guide-detail/things-to-carry-in-ladakh/">Things to Carry</a></li>
                      </ul>
                    </li>
                  </ul>

                </li>
                <li class="menu-item-has-children"><a href="/hotels" class="">Hotels</a>
                  <ul>
                    <li><a href="/city/hotels-in-leh/">Hotels in Leh</a></li>
                    <li><a href="/city/hotels-in-pangong-lake/">Hotels in Pangong</a></li>
                    <li><a href="/city/hotels-in-nubra-valley/">Hotels in Nubra Valley</a></li>
                    <li><a href="/city/hotels-in-kargil/">Hotels in Kargil</a></li>
                    <li><a href="/city/hotels-in-tsomoriri-lake/">Hotels in Tsomoriri Lake</a></li>
                    <li><a href="/city/hotels-in-sarchu/">Hotels in Sarchu</a></li>
                    <li><a href="/city/hotels-in-jispa/">Hotels in Jispa</a></li>
                    <li><a href="/city/hotels-in-alchi/">Hotels in Alchi</a></li>
                  </ul>
                </li>
                <li><a href="/blogs/" class="">Blogs</a></li>
                <li><a href="/promotion/" class="">Best Deals 2021</a></li>
              </ul>
            </nav>
          </div>
          <div class="talk-expert">
            <a href="tel:9205363933">+91 92053 63933</a>
            <a class="remove-phone-icon" href="https://api.whatsapp.com/send?phone=919205363933"><img src="https://go2ladakh.in/img/images/whatsapp.svg" alt="Go2Ladakh " width="25"></a>
          </div>
        </div>
      </header>

      <div class="page-content" style="min-height: 0px !important;padding-top: 80px !important;">
        <div class="searching-main-box">
          <div class="top-header">
            <ul>
              <li><a href="https://agent.go2ladakh.in/fixed-package-html">Group Tour</a></li>
              <li class="selected"><a href="#">Customize Package</a></li>
              <li><a href="#">Flight</a></li>
              <li><a href="#">Hotel</a></li>
              <li><a href="#">Taxi & Bike</a></li>
            </ul>
          </div>
        </div>
      </div>



      <style type="text/css">
        table {
          page-break-inside: auto
        }

        tr {
          page-break-inside: avoid;
          page-break-after: auto
        }

        thead {
          display: table-header-group
        }

        tfoot {
          display: table-footer-group
        }
      </style>

      <div class="layout-page" style="margin-top: -100px !important;">
        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="front-body-content">
              <form method="post" id="main-form">
                <div class="block">
                  <div class="left-part">
                    <div class="card">
                      <h1>Quick Booking</h1>

                      <div class="row mb-3">
                        <div class="col-md">
                          <label class="form-label">Guest Name</label>
                          <input type="text" class="form-control" name="name" placeholder="" required>
                        </div>



                        <div class="col-md">
                          <label class="form-label">Select Duration</label>
                          <div class="input-group">
                            <label class="input-group-text">Options</label>
                            <select class="form-select" name="duration" id="duration" required>
                              <option value="" disabled selected>Choose...</option>
                              <option value="1 Nights 2 Days" <?php echo ($edit &&  $data['duration'] == "1 Nights 2 Days") ? 'selected' : '' ?>>1 Nights 2 Days</option>
                              <option value="2 Nights 3 Days" <?php echo ($edit &&  $data['duration'] == "2 Nights 3 Days") ? 'selected' : '' ?>>2 Nights 3 Days</option>
                              <option value="3 Nights 4 Days" <?php echo ($edit &&  $data['duration'] == "3 Nights 4 Days") ? 'selected' : '' ?>>3 Nights 4 Days</option>
                              <option value="4 Nights 5 Days" <?php echo ($edit &&  $data['duration'] == "4 Nights 5 Days") ? 'selected' : '' ?>>4 Nights 5 Days</option>
                              <option value="5 Nights 6 Days" <?php echo ($edit &&  $data['duration'] == "5 Nights 6 Days") ? 'selected' : '' ?>>5 Nights 6 Days</option>
                              <option value="6 Nights 7 Days" <?php echo ($edit &&  $data['duration'] == "6 Nights 7 Days") ? 'selected' : '' ?>>6 Nights 7 Days</option>
                              <option value="7 Nights 8 Days" <?php echo ($edit &&  $data['duration'] == "7 Nights 8 Days") ? 'selected' : '' ?>>7 Nights 8 Days</option>
                              <option value="8 Nights 9 Days" <?php echo ($edit &&  $data['duration'] == "8 Nights 9 Days") ? 'selected' : '' ?>>8 Nights 9 Days</option>
                              <option value="9 Nights 10 Days" <?php echo ($edit &&  $data['duration'] == "9 Nights 10 Days") ? 'selected' : '' ?>>9 Nights 10 Days</option>
                              <option value="10 Nights 11 Days" <?php echo ($edit &&  $data['duration'] == "10 Nights 11 Days") ? 'selected' : '' ?>>10 Nights 11 Days</option>
                              <option value="11 Nights 12 Days" <?php echo ($edit &&  $data['duration'] == "11 Nights 12 Days") ? 'selected' : '' ?>>11 Nights 12 Days</option>
                              <option value="12 Nights 13 Days" <?php echo ($edit &&  $data['duration'] == "12 Nights 13 Days") ? 'selected' : '' ?>>12 Nights 13 Days</option>
                              <option value="13 Nights 14 Days" <?php echo ($edit &&  $data['duration'] == "13 Nights 14 Days") ? 'selected' : '' ?>>13 Nights 14 Days</option>
                              <option value="14 Nights 15 Days" <?php echo ($edit &&  $data['duration'] == "14 Nights 15 Days") ? 'selected' : '' ?>>14 Nights 15 Days</option>
                              <option value="15 Nights 16 Days" <?php echo ($edit &&  $data['duration'] == "15 Nights 16 Days") ? 'selected' : '' ?>>15 Nights 16 Days</option>
                              <option value="16 Nights 17 Days" <?php echo ($edit &&  $data['duration'] == "16 Nights 17 Days") ? 'selected' : '' ?>>16 Nights 17 Days</option>
                              <option value="17 Nights 18 Days" <?php echo ($edit &&  $data['duration'] == "17 Nights 18 Days") ? 'selected' : '' ?>>17 Nights 18 Days</option>
                              <option value=">18 Nights 19 Days" <?php echo ($edit &&  $data['duration'] == ">18 Nights 19 Days") ? 'selected' : '' ?>>18 Nights 19 Days</option>
                              <option value="19 Nights 20 Days" <?php echo ($edit &&  $data['duration'] == "19 Nights 20 Days") ? 'selected' : '' ?>>19 Nights 20 Days</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md">
                          <label class="form-label">Select Date</label>
                          <input class="form-control" type="date" name="tour_start_date" onChange="return itinerary_list()" value="<?= date('Y-m-d') ?>">
                        </div>
                      </div>


                      <h3 class="mt-3 mb-3">Select Package</h3>
                      <div class="row mb-3">
                        <div class="table-responsive">
                          <table class="table table-bordered">
                            <thead class="table-dark">
                              <tr>
                                <th class="text-white px-2" style="max-width: 84px">Select Package</th>
                                <th class="text-white px-2" style="max-width: 84px">Package Code</th>
                                <th class="text-white px-2">Package Name</th>
                                <th class="text-white px-2">Duration</th>
                                <th class="text-white px-2">Hotel Category</th>
                              </tr>
                            </thead>
                            <tbody class="table-border-bottom-0" id="package_list" required>

                            </tbody>
                          </table>
                        </div>
                      </div>
                      <input type="hidden" name="package_id">
                      <input type="hidden" name="category">
                      <input type="hidden" name="total_amount">
                      <input type="hidden" name="without_gst">
                      <input type="hidden" name="total_pax">
                      <input type="hidden" name="tour_end_date">
                      <input type="hidden" name="exclusive">
                      <input type="hidden" name="inclusive">

                      <div class="row mb-3" id="package-other-details">

                      </div>

                      <?php $transportations = setTransportation(); ?>
                      <h3 class="mt-3 mb-3">Select Transportation</h3>
                      <div class="row mb-3">
                        <div class="table-responsive">
                          <table class="table table-bordered">
                            <thead class="table-dark">
                              <tr>
                                <th class="text-white">Select Transport</th>

                                <th class="text-white">Remarks</th>
                              </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                              <tr class="transport-row">
                                <td>
                                  <select name="transport['name'][]" class="form-select transportation-select" onChange="return calculateTotal();" required>
                                    <option value="" disabled selected>Select Transport</option>
                                    <?php foreach ($transportations as $name => $val) : ?>
                                      <option value="<?php echo $name; ?>" data-trans="<?php echo $val; ?>"><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                  </select>
                                </td>

                                <td>Maximum <span class="max-persons"></span> Persons</td>
                              </tr>
                              <tr>
                                <td colspan="3" style="text-align:right;"><a href="#" id="addMoreTransport">Add More</a></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <div class="row mb-3" id="fixed-service">

                      </div>


                      <div class="row mb-3">
                        <div class="table-responsive" id="service-list">

                        </div>
                      </div>

                      <div class="row mb-3">
                        <div class="table-responsive" id="service-per-service">

                        </div>
                      </div>

                      <div class="row mb-3 align-items-top" style="display:none">
                        <div class="col-md-3">
                          <div class="form-check mt-b">
                            <input class="form-check-input" type="checkbox" id="bike">
                            <label class="form-check-label" for="bike">Bike </label>
                          </div>
                        </div>
                        <div class="col-md-9">
                          <div class="row">


                          </div>
                        </div>
                      </div>
                      <h3 class="mt-3 mb-3" id="enter-bike-details">Enter Bike Details</h3>
                      <div class="row mb-3" id="enter-bike-details-section">
                        <div class="table-responsive">
                          <table class="table table-bordered">
                            <tbody class="table-border-bottom-0">
                              <tr>
                                <td>No. of Bike</td>
                                <td colspan="2">
                                  <input type="number" name="number_of_bike" onChange="return calculateTotal();" class="form-control phone-mask">
                                </td>
                              </tr>
                              <tr>
                                <td>Mechanic</td>
                                <td colspan="2">
                                  <select class="form-select" name="mechanic" onChange="return calculateTotal();">
                                    <option>No</option>
                                    <option>Yes</option>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td>Marshal with Bike</td>
                                <td colspan="2">
                                  <select class="form-select" name="marshal" onChange="return calculateTotal();">
                                    <option>No</option>
                                    <option>Yes</option>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td>Fuel</td>
                                <td colspan="2">
                                  <select class="form-select" name="fuel" onChange="return calculateTotal();">
                                    <option>No</option>
                                    <option>Yes</option>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <td>Backup</td>
                                <td colspan="2">
                                  <select class="form-select" name="backup" onChange="return calculateTotal();">
                                    <option>No</option>
                                    <option>Yes</option>
                                  </select>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <h3 class="mt-3 mb-3">Itinerary</h3>
                      <div class="row mb-3">
                        <div class="table-responsive">
                          <table class="table table-bordered">
                            <thead class="table-dark">
                              <tr>
                                <th class="text-white">Day</th>
                                <th class="text-white">Date</th>
                                <th class="text-white">Day</th>
                                <th class="text-white">Plan #001</th>
                              </tr>
                            </thead>
                            <tbody class="table-border-bottom-0" id="itinerary-list">

                            </tbody>
                          </table>
                        </div>
                      </div>

                      <h3 class="mt-3 mb-3">Hotel Details</h3>
                      <div class="row mb-3">
                        <div class="table-responsive text-nowrap">
                          <table class="table table-bordered">
                            <thead class="table-dark">
                              <tr>
                                <th class="text-white px-2" style="max-width: 84px">Day</th>
                                <th class="text-white px-2" style="max-width: 84px">Hotel Name</th>
                                <th class="text-white px-2">Check in Date</th>
                                <th class="text-white px-2">Check out Date</th>
                                <th class="text-white px-2">Night</th>
                                <th class="text-white px-2">Location</th>
                                <th class="text-white px-2">Hotel status</th>
                              </tr>
                            </thead>
                            <tbody class="table-border-bottom-0" id="hotel-list">

                            </tbody>
                          </table>
                        </div>
                      </div>

                      <h3 class="mt-3 mb-3">Transport</h3>
                      <div class="row mb-3">
                        <div class="table-responsive">
                          <table class="table table-bordered" id="driver_list">
                            <thead class="table-dark">
                              <tr>
                                <th class="text-white">Vehicle Type</th>
                                <th class="text-white">No. of Vehicle</th>
                                <th class="text-white">Driver Name</th>
                                <th class="text-white">Mobile No.</th>
                              </tr>
                            </thead>
                            <tbody class=" table-border-bottom-0">

                            </tbody>
                          </table>
                        </div>
                      </div>
                      <h3 class="mt-3 mb-3">Exclusive / Inclusive</h3>

                      <div class="row mb-3">
                        <div class="col">
                          <h3 class="mt-3 mb-3 text-center" style="background-color: #233446; padding: 10px;   color: white;">Exclusive</h3>
                          <ul class="list-group" id="exclusive">
                          </ul>
                        </div>
                        <div class="col">
                          <h3 class="mt-3 mb-3 text-center" style="background-color: #233446; padding: 10px;  color: white;">Inclusive</h3>
                          <ul class="list-group" id="inclusive">
                          </ul>
                        </div>
                      </div>

                      <!--<h3 class="mt-3 mb-3">Emergency Contact Details</h3>
                <div class="row mb-3">
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead class="table-dark">
                        <tr>
                          <th class="text-white">Hotel Operation</th>
                          <th class="text-white">Transport</th>
                          <th class="text-white">Airport Manager</th>
                          <th class="text-white">Support Team</th>
                        </tr>
                      </thead>
                      <tbody class="table-border-bottom-0">
                        <tr>
                          <td>9999999999</td>
                          <td>9999999999</td>
                          <td>9999999999</td>
                          <td>9999999999</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>-->
                      <h3 class="mt-3 mb-3">Final Quotation</h3>
                      <div class="row mb-3">
                        <div class="table-responsive">
                          <table class="table table-bordered" id="final_quotation">
                            <thead class="table-dark">
                              <tr>
                                <th colspan="4" class="text-white">Your query 01133 Details Quotation in INR</th>
                              </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                              <tr>
                                <td class="dark-col"><strong>Plan</strong></td>
                                <td class="dark-col"><strong>Amount</strong></td>
                                <td class="dark-col"><strong>Pax</strong></td>
                                <td class="dark-col"><strong>Total amount</strong></td>
                              </tr>


                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="right-part" style="position:fixed;right:0px">
                    <div class="booking-summary">
                      <div class="card">
                        <div class="head">
                          <h3>Booking Summary</h3>
                        </div>
                        <div class="summary-detail">
                          <div class="row mb-3">
                            <div class="col-md text-bold"><strong>Duration:</strong></div>
                            <div class="col-md" id="summary-duration">-</div>
                          </div>
                          <div class="row mb-3">
                            <div class="col-md"><strong>Travel Date:</strong></div>
                            <div class="col-md" id="summary-travel-date">-</div>
                          </div>
                          <div class="row mb-3">
                            <div class="col-md"><strong>Total No. of Pax:</strong></div>
                            <div class="col-md" id="summary-no-of-pax">-</div>
                          </div>
                          <div class="row mb-3">
                            <div class="col-md"><strong>Calculated Price:</strong></div>
                            <div class="col-md"><strong id="summary-calculated-price"></strong></div>
                          </div>
                          <div class="row">
                            <div class="col-md"><strong>Per Person Price:</strong></div>
                            <div class="col-md"><strong id="per-person-calculated-price"></strong></div>
                          </div>
                        </div>
                      </div>
                      <div class="card" style="margin-top: 10px;height: 50px ! IMPORTANT;">

                        <div class="summary-detail">
                          <div class="row mb-3">
                            <div class="col-md text-bold"><label class="form-label"><strong>Your Budget</strong></label></div>
                            <div class="col-md" id="summary-duration"><input type="number" name="your_budget" class="form-control"></div>
                          </div>

                        </div>
                      </div>
                    </div>
                    <input type="hidden" id="main-email" name="email" value="">
                    <input type="hidden" id="main-mobile" name="mobile" value="">
                    <div class="row get-quote-btn" style="margin-top: 10px;">
                      <button class="clickable-text btn btn-primary" id="generate-quote" type="button">Generate Quote</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>


    </div>
  </div>


  <!-- OTP Verification Popup -->
  <div class="verification-popup">
    <div class="highlighter-box">
      <div class="close-btn" id="close-btn"></div>
      <h2>Mobile/Email Verification</h2>
      <div class="user-type-btn">
        <div class="block">
          <div class="guest-user active">Guest Login</div>
          <div class="agent-login">Agent Login</div>
        </div>
      </div>
      <div class="single">
        <label>Email ID</label>
        <input type="email" id="popup-email" placeholder="abc@abc.com" required />
      </div>
      <div class="single">
        <label>Mobile Number</label>
        <input type="number" id="popup-mobile" placeholder="9999999999" required />
      </div>
      <div class="single btn">
        <button type="button" id="send-otp">Send OTP</button>
      </div>

      <div class="single btn" id="otp-section" style="display: none;">
        <label>OTP</label>
        <input type="text" id="popup-otp" placeholder="Enter OTP" required />
        <button type="button" id="verify-otp" style="margin-top:20px;">Verify OTP</button>
      </div>

    </div>
  </div>




  <script>
    let generatedOtp = null;
    const form = document.getElementById('main-form');

    // Open popup
    document.getElementById('generate-quote').addEventListener('click', function() {
      if (form.checkValidity()) {
        document.querySelector('.verification-popup').classList.add('show')
      } else {
        alert('Please complete all the details')
        form.reportValidity();
      }
      // document.querySelector('.verification-popup').style.display = 'block';
      // document.querySelector('.verification-popup').classList.add('show')
    });

    // Close popup
    document.getElementById('close-btn').addEventListener('click', function() {
      // document.querySelector('.verification-popup').style.display = 'none';
      document.querySelector('.verification-popup').classList.remove('show');
    });

    // Send OTP
    document.getElementById('send-otp').addEventListener('click', function() {
      const email = document.getElementById('popup-email').value.trim();
      const mobile = document.getElementById('popup-mobile').value.trim();

      if (!email || !mobile) {
        alert('Please fill out both Email and Mobile Number.');
        return;
      }

      // Generate random 4-digit OTP
      generatedOtp = Math.floor(1000 + Math.random() * 9000);

      // Simulate backend OTP sending
      fetch('ajaxCustomizePackage.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            email,
            mobile,
            otp: btoa(generatedOtp)
          }),
        })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            alert('OTP sent successfully!');
            document.getElementById('otp-section').style.display = 'block';
          } else {
            alert('Failed to send OTP. Try again.');
          }
        })
        .catch((error) => {
          console.error('Error:', error);
          alert('An error occurred while sending OTP.');
        });
    });

    // Verify OTP
    document.getElementById('verify-otp').addEventListener('click', function() {
      const userOtp = document.getElementById('popup-otp').value.trim();

      if (userOtp == generatedOtp) {
        alert('OTP verified successfully!');

        // Add Email and Mobile to the Main Form
        const email = document.getElementById('popup-email').value.trim();
        const mobile = document.getElementById('popup-mobile').value.trim();

        document.getElementById('main-email').value = email;
        document.getElementById('main-mobile').value = mobile;

        // Close popup
        document.querySelector('.verification-popup').style.display = 'none';

        // Submit the Main Form
        document.getElementById('main-form').submit();
      } else {
        alert('Invalid OTP. Please try again.');
      }
    });
  </script>


  <script>
    var inclusive = [];
    var exclusive = [];
    $(document).ready(function() {
      $('#duration').change(function() {
        var duration = $('#duration').val();
        service_list();
        $.ajax({
          url: '../ajax/package_list.php',
          type: 'POST',
          data: {
            duration: duration
          },
          success: function(data) {
            $('#package_list').html(data);

          },
          error: function(xhr, status, error) {
            console.error('Error:', error);
          }
        });
      });

    });


    function setPackageId(package_id) {
      $('input[name="package_id"]').val(package_id)
      setTimeout(function() {
        itinerary_list();
      }, 10);

    }

    function setCategory(category, package_id) {
      if ($('input[name="package_id"]').val() == package_id) {
        $('input[name="category"]').val(category)
        setTimeout(function() {
          hotel_list();
        }, 10);
      } else {
        alert("Please select package name")
      }
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
      let package_id = $('input[name="package_id"]').val()

      service_list();
      $.ajax({
        url: '../ajax/itinerary_list.php',
        type: 'POST',
        data: {
          package_id: package_id,
          tour_date: tour_date
        },
        success: function(data) {
          let dataArr = data.split("98230948klasd908809230894")
          $('#itinerary-list').html(dataArr[0]);
          $('#fixed-service').html(dataArr[1]);

          setTimeout(function() {
            calculateTotal();
          }, 2)
        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
        }
      });
    }

    function service_list() {
      let tour_date = $('input[name="tour_start_date"]').val()
      let days = parseInt($("#duration").val().match(/\d+/)[0]);

      if ($('input[name="tour_start_date"]').val() != '' &&
        $("#duration").val().match(/\d+/)[0] != "") {
        $.ajax({
          url: '../ajax/service_list.php',
          type: 'POST',
          data: {
            days: days,
            tour_date: tour_date
          },
          success: function(data) {
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
        url: '../ajax/package_other_details.php',
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
      let package_id = $('input[name="package_id"]').val()
      let category = $('input[name="category"]').val()

      package_other_details(package_id, category)
      $.ajax({
        url: '../ajax/hotel_list.php',
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


    function handleIncrement() {
      const input = this.parentElement.querySelector('.quantity');
      input.value = parseInt(input.value) + 1;

      calculateTotal();
    }

    function handleDecrement() {
      const input = this.parentElement.querySelector('.quantity');
      if (parseInt(input.value) > 0) {
        input.value = parseInt(input.value) - 1;

        calculateTotal();
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

    document.addEventListener('DOMContentLoaded', function() {
      // Function to update maximum number of persons
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
      $('#addMoreTransport').click(function(e) {
        e.preventDefault();
        const newRow = $('.transport-row:first').clone();
        newRow.find('.transportation-select').val('Select Transport');
        newRow.find('.num-persons-select').val('Select Person');
        newRow.find('.max-persons').empty();
        newRow.insertAfter('.transport-row:last');
        newRow.find('.transportation-select').each(updateMaxPersons);
      });

      // Event delegation for dynamically added elements
      $('.table').on('change', '.transportation-select', updateMaxPersons);
      $('.table').on('change', '.transportation-select:first', removeAllBelowElement);

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

    function calculateTotal() {
      inclusive = [];
      exclusive = [];
      const packageDetails = document.querySelectorAll('#package-other-details .col-md');
      const targetTableBody = document.querySelector('#final_quotation tbody');

      let totalAmount = 0;
      let total_per_person = 0;
      const existingRows = targetTableBody.querySelectorAll('tr:not(:first-child)');
      existingRows.forEach(row => row.remove());
      const rowData = {
        items: [],
        totalMember: 0
      };

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
      const perService = document.getElementById('service-per-service');
      perService.querySelectorAll('.col-md-4').forEach(row => {
        const input = row.querySelector('input[type="number"]');
        const label = row.querySelector('.form-check-label').textContent.trim();
        const amount = input.getAttribute('amount-per-service')
        const quantity = parseInt(input.value) || 0;
        if (quantity > 0) {
          addInclusive(label);
          total_per_person = total_per_person + ((amount * quantity) / totalPax);
        } else {
          addExclusive(label);
        }
      });


      //Transportation
      const driverTableBody = document.querySelector('#driver_list tbody');
      const driverDetails = <?= $json_vehicle ?>;
      const existingDriver = driverTableBody.querySelectorAll('tr');
      existingDriver.forEach(row => row.remove());
      const transportationSelects = document.querySelectorAll('.transportation-select');
      var trans_inclusive = '';
      transportationSelects.forEach(select => {
        const detailId = 'detail_' + select.value.replace(' / ', '_');

        if (document.getElementById(detailId)) {
          const label = select.value;
          console.log('llllllll', label);
          console.log(' ddddddddd', driverDetails, driverDetails[label])
          selectedOption = select.options[select.selectedIndex];
          const trans = selectedOption.getAttribute('data-trans');

          const amount = parseFloat(document.getElementById(detailId).value);
          const quantity = 1; // Quantity is always 1
          const total = amount * quantity;
          // Append to the table
          total_per_person = total_per_person + ((amount * quantity) / totalPax)
          trans_inclusive = trans_inclusive + label + "-" + trans + ", ";

          let driver_name = driverDetails[label].driver_name;
          let mobile = driverDetails[label].mobile;
          const driverRow = document.createElement('tr');
          driverRow.innerHTML = `
            <td>${label}</td>
            <td>${quantity}</td>
           <!-- <td>${driver_name}</td>
            <td>${mobile}</td>-->
            <td>Pending</td>
            <td>Pending</td>
        `;
          driverTableBody.appendChild(driverRow);

        }
      });

      if (trans_inclusive != '') {
        addInclusive("Transportation: " + trans_inclusive.replace(/, $/, ''));
      }
      if ($('input[name="category"]').val()) {
        addInclusive("Hotel: " + $('input[name="category"]').val());
      }

      // Get Bike Details
      const numberOfBike = document.querySelector('input[name="number_of_bike"]').value;
      const mechanic = document.querySelector('select[name="mechanic"]').value;
      const marshal = document.querySelector('select[name="marshal"]').value;
      const fuel = document.querySelector('select[name="fuel"]').value;
      const backup = document.querySelector('select[name="backup"]').value;

      // Retrieve the values of the inputs
      const numberOfBikePrice = parseInt(numberOfBike) * parseInt(<?php echo Bike ?>);
      const mechanicPrice = mechanic == 'Yes' ? parseInt(<?php echo Mechanic ?>) : 0;
      const marshalPrice = marshal == 'Yes' ? parseInt(<?php echo Marshal ?>) : 0;
      const fuelPrice = fuel == 'Yes' ? parseInt(<?php echo Fuel ?>) : 0;
      const backupPrice = backup == 'Yes' ? parseInt(<?php echo Backup ?>) : 0;
      //document.getElementById('bike').checked
      const total_bike_price = (numberOfBikePrice + mechanicPrice + marshalPrice + fuelPrice + backupPrice)

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
        totalAmount += total;
      });
      //totalMember
      if (document.getElementById('bike').checked) {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
                <td>Bike</td>
                <td>${(total_bike_price/ numberOfBike).toFixed(2)}</td>
                <td>${ numberOfBike}</td>
                <td>${total_bike_price.toFixed(2)}</td>
            `;
        targetTableBody.appendChild(newRow);
        totalAmount += total_bike_price;
      }

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
        li.className = 'list-group-item';
        li.textContent = value;
        exclusiveList.appendChild(li);
      });


      var inclusiveList = document.getElementById('inclusive');
      inclusiveList.innerHTML = '';
      inclusive.forEach(function(value) {
        var li = document.createElement('li');
        li.className = 'list-group-item';
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
      var index = exclusive.indexOf(value);
      if (index !== -1) {
        exclusive.splice(index, 1);
      }
    }


    //calculateTotal();
    /*
document.addEventListener('input', function(event) {
    if (event.target.classList.contains('quantity')) {
        calculateTotal();
    }
});
*/
  </script>


  <footer>
    <div class="main-footer">
      <div class="block">
        <div class="content-block">
          <h3>Tours by Themes</h3>
          <ul>
            <li><a href="https://www.go2ladakh.in/trekking-in-ladakh/">Trekking in Ladakh</a></li>
            <li><a href="https://www.go2ladakh.in/leh-ladakh-bike-trip/">Leh Ladakh Bike Trip</a></li>
            <li><a href="https://www.go2ladakh.in/ladakh-road-trips/">Leh Ladakh Road Trip</a></li>
          </ul>
          <h3 class="delhi-office">Top Destination</h3>
          <ul>
            <li><a href="https://www.go2ladakh.in/leh-ladakh-tour-packages/">Leh Ladakh Tour packages</a></li>
            <li><a href="https://www.go2ladakh.in/kashmir-tour-packages/">Kashmir Tour packages</a></li>
            <li><a href="https://www.go2ladakh.in/himachal-tour-packages/">Himachal tour packages</a></li>
            <li><a href="https://www.go2ladakh.in/bhutan-tour-packages/">Bhutan tour Packages</a></li>
            <li><a href="https://www.go2ladakh.in/north-east-tour-packages/">Northeast Tour packages</a></li>
            <li><a href="https://www.go2ladakh.in/ladakh-honeymoon-packages/">Ladakh Honeymoon Packages</a></li>
            <li><a href="https://www.go2ladakh.in/lahaul-spiti-tour-packages/">Spiti Valley Tour Packages</a></li>
            <li><a href="https://www.go2ladakh.in/manali-family-tour-packages/">Manali Family Packages</a></li>
            <li><a href="https://www.go2ladakh.in/manali-honeymoon-packages/">Manali Honeymoon Packages</a></li>
            <li><a href="https://www.go2ladakh.in/manali-tour-packages/">Manali Tour Packages</a></li>
            <li><a href="https://www.go2ladakh.in/ladakh-family-tour-packages/">Ladakh Family Packages</a></li>
            <li><a href="https://www.go2ladakh.in/kashmir-honeymoon-packages/">Kashmir Honeymoon Packages</a></li>
            <li><a href="https://www.go2ladakh.in/kashmir-tour-packages-for-family/">Kashmir Tour Packages</a></li>
          </ul>
        </div>

        <div class="content-block">
          <h3>Tours by Season</h3>
          <ul>
            <li><a href="https://www.go2ladakh.in/summer-packages/">Summer Packages</a></li>
            <li><a href="https://www.go2ladakh.in/winter-packages/">Winter Packages</a></li>
          </ul>

          <h3 class="delhi-office">Tours by Style</h3>
          <ul>
            <li><a href="https://www.go2ladakh.in/group-tours/">Group Tours</a></li>
            <li><a href="https://www.go2ladakh.in/tailor-made-tours/">Tailor-made Tours</a></li>
          </ul>
        </div>

        <div class="content-block">
          <h3>Ladakh Travel Guide</h3>
          <ul>
            <li><a href="https://www.go2ladakh.in/ladakh-travel-guide/>">Ladakh Travel Information</a></li>
            <li><a href="https://www.go2ladakh.in/places-to-visit-in-ladakh/">Places to Visit in Ladakh</a></li>
            <li><a href="https://www.go2ladakh.in/things-to-do-in-ladakh/">Things to Do in Ladakh</a></li>
          </ul>
          <h3>Useful Resources</h3>
          <ul>
            <li><a href="https://www.go2ladakh.in/blogs/">Blog</a></li>
            <li><a href="http://go2ladakh.in/img/images/2020-ladakh-brochure.pdf" target="_blank">Ladakh Brochure</a></li>
            <li><a href="http://go2ladakh.in/img/images/ladakh-bike-trip-2020.pdf" target="_blank">Ladakh Bike Trip Brochure</a></li>
          </ul>
        </div>
        <div class="content-block">
          <h3>Go2Ladakh</h3>
          <ul>
            <li><a href="https://www.go2ladakh.in/about-us/">About Us</a></li>
            <li><a href="https://www.go2ladakh.in/contact-us/">Contact Us</a></li>
            <li><a href="https://www.go2ladakh.in/terms-and-conditions/">Terms &amp; Conditions</a></li>
            <li><a href="https://www.go2ladakh.in/covid19-tnc/">COVID 19 travel advisory 2021</a></li>
            <li><a href="https://www.go2ladakh.in/privacy-policy/">Privacy Policy</a></li>
          </ul>

          <h3>Follow Us</h3>
          <a class="fb" href="https://www.facebook.com/Go2ladakh.in/"></a>
          <a class="twitter" href="https://twitter.com/go2ladakh"></a>
          <a class="youtube" href="https://www.youtube.com/channel/UCTxa6dbNX033qr3JHFLhiyA?view_as=subscriber"></a>
          <a class="instagram" href="https://in.pinterest.com/go2ladakhin/"></a>
          <a class="pinterest" href="https://www.instagram.com/go2ladakh.in/?hl=en"></a>
        </div>
      </div>
    </div>
    <div class="copyright">
      Copyright@ Go2ladakh. All Rights Reserved.
    </div>
  </footer>

  <!--<script src="js/common-ui.js"></script>-->
</body>

</html>