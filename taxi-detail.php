<?php
session_start();
require_once './config/config.php';
require_once 'includes/agent_header.php';
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
<link href="assets/css/custom-package.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

<div class="customize-page-content">
  <div class="main-content">
    <div class="left-content">
      <div class="main-banner-section">
        <div class="block">
          <div class="left-part">
            <!-- Mega Search Module-->
            <div class="mega-search">
              <div class="group-radio-selection">
                <div class="select-tour">
                  <input type="radio" name="tour-type" id="group-tour" value="Group Tour" />
                  <label for="group-tour">Group Tour</label>
                </div>
                <div class="select-tour">
                  <input type="radio" name="tour-type" id="customize" value="Customize Journey" />
                  <label for="customize">Customize Journey</label>
                </div>
                <div class="select-tour">
                  <input type="radio" name="tour-type" id="taxi" value="Taxi" />
                  <label for="taxi">Taxi</label>
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
              <div class="group-tour-form">
                <div class="select-tour">
                  <input type="text" placeholder="Guest Name" name="guest_name" class="form-control">
                </div>
                <!-- Group Tour Form -->
                <div class="single tour-type">
                  <select name="duration" id="duration" required>
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

                <div class="single select-destination">
                  <select id="destination" name="destination">
                    <option value="">Choose destination...</option>
                    <?php foreach ($destinations as $destination): ?>
                      <option value="<?= $destination['id']; ?>" <?= ($edit && $data['destination'] == $destination['id']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($destination['name']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="single calendar">
                  <input id="calendar" type="date" name="tour_start_date" onChange="return itinerary_list()" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="single">
                  <button type="button" class="search" id="searchBtn">Search</button>
                </div>
              </div>
            </div>
          </div>
          <!-- Add rooming detail -->
          <div class="row mb-3 select-rooms" id="package-other-details">
          </div>
        </div>
      </div>
      <!-- TOC Section -->
      <div class="toc-section">
        <ul>
          <li><a href="#" class="nav-link" data-target="overview-section">Overview</a></li>
          <li><a href="#" class="nav-link" data-target="itinerary-section">Itinerary</a></li>
          <li><a href="#" class="nav-link" data-target="hotel-details-layout">Cars</a></li>
          <li><a href="#" class="nav-link" data-target="extra-services">Extra Services</a></li>
          <li><a href="#" class="nav-link" data-target="per-service">Per Service</a></li>
          <li><a href="#" class="nav-link" data-target="inclusive">Inclusions</a></li>
          <li><a href="#" class="nav-link" data-target="exclusive">Exclusions</a></li>
        </ul>
      </div>
      <!-- Title and Gallery Section -->
      <div class="gallery-section">
        <div class="gallery-header">
          <div class="gallery-title">
            <h1 id="package_name"></h1>
          </div>
          <div class="rating-with-pkg">

            <p><span class="dynamic_package"></span> | Customizable Package</p>

            <div class="rating">4.5/5 - 245 Reviews</div>
          </div>
        </div>
        <div class="gallery-images">

        </div>
      </div>
      <!-- Overview section -->
      <div class="overview-section">
      </div>

      <!-- Itinerary section -->
      <div class="itinerary-section">
        <h2>Itinerary</h2>
        <div class="table-structure">
          <div class="table-responsive">
            <table>
              <thead class="table-dark">
                <tr>
                  <th>Day</th>
                  <th>Date</th>
                  <th>Plan</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0" id="itinerary-list">

              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- select transportation -->
      <div class="hotel-details-layout">
        <h2>Car List</h2>
        <div class="car-list" id="car-list">

        </div>
      </div>
      <!-- Extra Services layout -->
      <div class="extra-services" id="fixed-service">
      </div>
      <!-- Services layout -->
      <div class="other-services">
        <div class="table-responsive" id="service-list">

        </div>
      </div>
      <!-- service per service -->
      <div class="per-service">
        <div class="table-responsive" id="service-per-service">
        </div>
      </div>

      <!-- Inclusion and Exclusion -->
      <div class="inclusion-exclusion">
        <div class="exclusion-col colm">
          <h2>Exclusions</h2>
          <ol id="exclusive" class="exclusive">
          </ol>
        </div>
        <div class="inclusion-col colm">
          <h2>Inclusions</h2>
          <ol id="inclusive" class="inclusive">
          </ol>
        </div>
      </div>

      <!-- Final Quotation -->
      <div class="final-quotation d-none">
        <h2>Final Quotation</h2>
        <div class="row mb-3">
          <div class="table-responsive">
            <table class="table" id="final_quotation">
              <thead class="quotation-head">
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
    <div class="right-content">
      <div class="booking-summary">
        <div class="card">
          <div class="head">
            <h3>Booking Summary</h3>
          </div>
          <div class="summary-detail">
            <div class="single">
              <div class="title">Guest Name:</div>
              <div class="desc" id="guest_name"></div>
            </div>
            <div class="single">
              <div class="title">Duration:</div>
              <div class="desc" id="summary-duration"></div>
            </div>
            <div class="single">
              <div class="title">Travel Date:</div>
              <div class="desc" id="summary-travel-date"></div>
            </div>
            <div class="car_details" id="car_details">
            </div>

            <div class="single">
              <div class="title">Total No. of Pax:</div>
              <div class="desc" id="summary-no-of-pax"></div>
            </div>
            <div class="single">
              <div class="title">Calculated Price:</div>
              <div class="desc"><strong id="summary-calculated-price"></strong></div>
            </div>
            <div class="single">
              <div class="title">Per Person Price:</div>
              <div class="desc"><strong id="per-person-calculated-price"></strong></div>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="summary-detail" style="border-radius: 0 0 12px 12px">
            <div class="single full-width">
              <div class="title"><label class="form-label">Your Budget</label></div>
              <div class="desc" id="summary-duration"><input type="number" name="your_budget" class="form-control"></div>
            </div>
            <div class="single">
              <button type="submit" class="btn btn-primary mt-5 ms-5" data-bs-toggle="modal" data-bs-target="#loginModal" style="padding: 10px 20px; border-radius: 6px;">Generate Quote</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


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
              <th class="text-white">Number of pax</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0" id="package_list" required>

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
<input type="hidden" name="package_id">
<input type="hidden" name="category">
<input type="hidden" name="total_amount">
<input type="hidden" name="without_gst">
<input type="hidden" name="total_pax">
<input type="hidden" name="tour_end_date">
<input type="hidden" name="exclusive">
<input type="hidden" name="inclusive">


</div>
</div>

<script>
  var inclusive = [];
  var exclusive = [];


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
      if (params.package_id && params.package_name) {

        $("#package_name").text(params.package_name);
        // $("#traveling_from").val(params.traveling_from).trigger('change');
        $("#destination").val(params.destination).trigger('change');
        $("#tour_start_date").val(params.travel_date);
        $("#duration").val(params.duration);

        document.querySelector('.dynamic_package').textContent = document.getElementById('duration').value;
        itinerary_list();
        fetchOverviewWithImage(params.package_id);
        fetchCar(params.package_id);
        // addExclusive(serviceList);
        inclusive = [];
      } else {
        // $("#traveling_from").val(params.traveling_from).trigger('change');
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
        url: 'ajax/taxi_list.php',
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



    const urlParams = new URLSearchParams(window.location.search);
    let numberPax = Number(urlParams.get("number_pax")) || 0; // Get 'number_pax' from URL
    let totalPassengers = 0; // Track total selected passengers
    let totalPrice = 0; // Track total price

    function updateSummary() {
      $("#summary-no-of-pax").text(totalPassengers); // Update number of pax
      $("#summary-calculated-price").text(`${totalPrice.toFixed(2)}`); // Update price
    }

    function selectCar(id, name, passenger, bag, price) {
      let carList = $("#car_details");
      let existingCar = $("#car-" + id);
      let passengerCount = Number(passenger);
      let carPrice = Number(price);

      if (isNaN(passengerCount) || passengerCount <= 0) {
        alert("Invalid passenger count!");
        return;
      }

      // if (totalPassengers + passengerCount > numberPax) {
      //   alert("Cannot add this car! Required seats are already covered.");
      //   return;
      // }

      if (existingCar.length > 0) {
        alert("This car is already selected.");
        return;
      } else {
        let carHtml = `
                <div class="single" id="car-${id}">
                    <div class="title">${name}</div>
                    <div class="desc">
                        Passengers: ${passenger}
                        <button class="btn btn-danger btn-sm mt-2" onclick="removeCar(${id}, ${passenger}, ${price})">Remove</button>
                        </div>
                        </div>
                        `;
        carList.append(carHtml);
        totalPassengers += passengerCount;
        totalPrice += carPrice;
      }

      updateSummary(); // Update passenger count and price summary
    }

    function removeCar(id, passenger, price) {
      let passengerCount = Number(passenger);
      let carPrice = Number(price);

      $("#car-" + id).remove();
      totalPassengers -= passengerCount;
      totalPrice -= carPrice;

      updateSummary(); // Update summary after removal
    }

    window.selectCar = selectCar;
    window.removeCar = removeCar;



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

  function fetchCar(packageId) {
    $.ajax({
      url: 'ajax/car_list.php',
      type: 'POST',
      data: {
        taxi_id: packageId
      },
      success: function(data) {
        $('.car-list').html(data);
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
      fetchCar(package_id);
    }, 10);

  }




  // Fetch Overview and Images for Selected Package
  function fetchOverviewWithImage(packageId) {
    $.ajax({
      url: 'ajax/fetchTaxiOverviewImage.php',
      type: 'POST',
      data: {
        taxi_id: packageId
      },
      dataType: 'json',
      success: function(response) {
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

    if (!package_id) {
      const params = getUrlParams();
      package_id = params.package_id; // Assign it instead of re-declaring with let
    }

    if (package_id) {
      service_list(package_id);
    }

    $.ajax({
      url: 'ajax/taxi_itinerary_list.php',
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
          calculateTotal(0, 0);
        }, 2)
      },
      error: function(xhr, status, error) {
        console.error('Error:', error);
      }
    });
  }

  function setCategory(pax) {
    // if ($('input[name="package_id"]').val() == package_id) {
    //   $('input[name="category"]').val(category)
    const newUrl = new URL(window.location.href);
    newUrl.searchParams.set('number_pax', pax);
    window.history.pushState({}, '', newUrl);
    // } else {
    //   alert("Please select package name")
    // }
  }

  function service_list(package_id) {
    let tour_date = $('input[name="tour_start_date"]').val()
    let days = parseInt($("#duration").val().match(/\d+/)[0]);

    if ($('input[name="tour_start_date"]').val() != '' &&
      $("#duration").val().match(/\d+/)[0] != "") {
      $.ajax({
        url: 'ajax/taxi_service_list.php',
        type: 'POST',
        data: {
          days: days,
          tour_date: tour_date,
          taxi_id: package_id
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

  // function package_other_details(package_id) {
  //   $.ajax({
  //     url: 'ajax/taxi_package_other_details.php',
  //     type: 'POST',
  //     data: {
  //       package_id: package_id,
  //     },
  //     success: function(data) {
  //       $('#package-other-details').html(data);
  //     },
  //     error: function(xhr, status, error) {
  //       console.error('Error:', error);
  //       $('#package-other-details').html(error);
  //     }
  //   });
  // }

  function hotel_list() {
    let tour_date = $('input[name="tour_start_date"]').val()
    const params = new URLSearchParams(window.location.search);
    let package_id = params.get('package_id') || $('input[name="package_id"]').val();
    let category = params.get('package_category') || $('input[name="category"]').val();


    // package_other_details(package_id, category)
    $.ajax({
      url: 'ajax/hotel_list.php',
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
<?php include  '../includes/agent_footer.php'; ?>