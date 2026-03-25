<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>CRM for sales management</title>
  <meta name="description" content="" />
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="assets/vendor/css/core.css" />
  <link rel="stylesheet" href="assets/css/style.min.css" />
  <link rel="stylesheet" href="assets/css/front.min.css" />
  <link rel="stylesheet" href="assets/vendor/css/theme-default.css" />

  <!-- success and error popup style -->
  <style>
    /* Basic styling for popups */
    .popup {
      display: none;
      /* Initially hidden */
      position: fixed;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .success {
      background-color: #4CAF50;
      color: white;
    }

    .error {
      background-color: #f44336;
      color: white;
    }

    .hiddenCategory {
      display: none;
    }
  </style>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/4.0.14/css/froala_editor.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/4.0.14/js/froala_editor.pkgd.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      $(".toggle-options").click(function(event) {
        event.stopPropagation(); // Prevents the click event from propagating to the document

        // Close any open dropdowns
        $(".dropdown-menu").not($(this).next(".dropdown-menu")).hide();

        // Toggle the current dropdown
        $(this).next(".dropdown-menu").toggle();
      });

      // Close the dropdown if clicked outside
      $(document).click(function(event) {
        if (!$(event.target).closest('.dropdown').length) {
          $('.dropdown-menu').hide(); // Hide the dropdown if clicked outside
        }
      });
    });
  </script>
</head>
<?php
$current_url =  $_SERVER['REQUEST_URI'];
$filename = basename(parse_url($current_url, PHP_URL_PATH));
// die($filename);
is_admin_login();
?>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->

      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo bg-dark">
          <a href="#" class="app-brand-link">
            <span class="app-brand-logo demo">
              <img src="assets/img/logo.png" alt="logo" />
            </span>
          </a>
        </div>

        <div class="menu-inner-shadow"></div>
        <ul class="menu-inner py-1 bg-dark pt-3">
          <!-- Forms -->
          <li class="menu-item active open">
            <ul class="menu-sub">

              <li class="menu-item <?php echo ($filename == 'package.php') ? 'active' : '' ?>">
                <a href="package.php" class="menu-link">
                  <div data-i18n="Basic Inputs">View Package</div>
                </a>
              </li>
              <li class="menu-item <?php echo ($filename === 'booking.php') ? 'active' : ''; ?>">
                <a href="booking.php" class="menu-link">
                  <div data-i18n="Input groups">All Booking Summary</div>
                </a>
              </li>

              <li class="menu-item <?php echo  strpos($filename, 'query.php') !== false ? 'active' : '' ?>">
                <a href="query.php" class="menu-link">
                  <div data-i18n="Input groups">All Query Summary</div>
                </a>
              </li>
              <li class="menu-item <?php echo  strpos($filename, 'invoice.php') !== false ? 'active' : '' ?>">
                <a href="invoice.php" class="menu-link">
                  <div data-i18n="Input groups">All Invoices</div>
                </a>
              </li>
              <li class="menu-item <?php echo  strpos($filename, 'agent.php') !== false ? 'active' : '' ?>">
                <a href="agent.php" class="menu-link">
                  <div data-i18n="Input groups">Agent Information</div>
                </a>
              </li>

              <li class="menu-item <?php echo  strpos($filename, 'hotel.php') !== false ? 'active' : '' ?>">
                <a href="hotel.php" class="menu-link">
                  <div data-i18n="Input groups">View Hotel Information</div>
                </a>
              </li>
              <li class="menu-item <?php echo strpos($filename, 'hotel') !== false ? 'active open' : ''; ?>">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                  <div data-i18n="Input groups">Locations</div>
                </a>
                <ul class="menu-sub">
                  <li class="menu-item <?php echo strpos($filename, 'hotel.php') !== false ? 'active' : ''; ?>">
                    <a href="location-list.php" class="menu-link">
                      <div data-i18n="Location List">Location List</div>
                    </a>
                  </li>
                  <li class="menu-item <?php echo strpos($filename, 'add_hotel.php') !== false ? 'active' : ''; ?>">
                    <a href="add-location.php" class="menu-link">
                      <div data-i18n="Add Location">Add Location</div>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="menu-item <?php echo  strpos($filename, 'vehicle.php') !== false ? 'active' : '' ?>">
                <a href="vehicle.php" class="menu-link">
                  <div data-i18n="Input groups">View Vehicle Details</div>
                </a>
              </li>

              <li class="menu-item <?php echo  strpos($filename, 'guide.php') !== false ? 'active' : '' ?>">
                <a href="guide.php" class="menu-link">
                  <div data-i18n="Input groups">View Guide details</div>
                </a>
              </li>
              <li class="menu-item <?php echo strpos($filename, 'service.php') !== false ? 'active' : '' ?>">
                <a href="service.php" class="menu-link">
                  <div data-i18n="Input groups">View Service details</div>
                </a>
              </li>
              <li class="menu-item <?php echo strpos($filename, 'Destination') !== false ? 'active open' : ''; ?>">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                  <div data-i18n="Input groups">Destination</div>
                </a>
                <ul class="menu-sub">
                  <li class="menu-item <?php echo strpos($filename, 'hotel.php') !== false ? 'active' : ''; ?>">
                    <a href="view-destination.php" class="menu-link">
                      <div data-i18n="View Destination">View Destination</div>
                    </a>
                  </li>
                  <li class="menu-item <?php echo strpos($filename, 'add_hotel.php') !== false ? 'active' : ''; ?>">
                    <a href="add-destination.php" class="menu-link">
                      <div data-i18n="Add Location">Add Destination</div>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="menu-item <?php echo strpos($filename, 'add-from.php') !== false ? 'active open' : ''; ?>">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                  <div data-i18n="Input groups">From</div>
                </a>
                <ul class="menu-sub">
                  <li class="menu-item <?php echo strpos($filename, 'view-from.php') !== false ? 'active' : ''; ?>">
                    <a href="view-from.php" class="menu-link">
                      <div data-i18n="View From">View From</div>
                    </a>
                  </li>
                  <li class="menu-item <?php echo strpos($filename, 'add-from.php') !== false ? 'active' : ''; ?>">
                    <a href="add-from.php" class="menu-link">
                      <div data-i18n="Add From">Add From</div>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="menu-item <?php echo strpos($filename, 'add-category.php') !== false ? 'active open' : ''; ?>">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                  <div data-i18n="Input groups">Category</div>
                </a>
                <ul class="menu-sub">
                  <li class="menu-item <?php echo strpos($filename, 'view-category.php') !== false ? 'active' : ''; ?>">
                    <a href="view-category.php" class="menu-link">
                      <div data-i18n="View Category">View Category</div>
                    </a>
                  </li>
                  <li class="menu-item <?php echo strpos($filename, 'add-category.php') !== false ? 'active' : ''; ?>">
                    <a href="add-category.php" class="menu-link">
                      <div data-i18n="Add Category">Add Category</div>
                    </a>
                  </li>
                </ul>
              </li>


              <li class="menu-item <?php echo strpos($filename, 'view-flight-destination.php') !== false ? 'active' : '' ?>">
                <a href="view-flight-destination.php" class="menu-link">
                  <div data-i18n="Input groups">View Flight Destination</div>
                </a>
              </li>

              <li class="menu-item <?php echo ($filename === 'flight-booking.php') ? 'active' : ''; ?>">
                <a href="flight-booking.php" class="menu-link">
                  <div data-i18n="Input groups">Airline Booking</div>
                </a>
              </li>

              <li class="menu-item <?php echo ($filename === 'view-flight-booking.php') ? 'active' : ''; ?>">
                <a href="view-flight-booking.php" class="menu-link">
                  <div data-i18n="Input groups">View Airline Booking</div>
                </a>
              </li>


              <li class="menu-item <?php echo strpos($filename, 'add-fixed-package.php') !== false ? 'active' : '' ?>">
                <a href="add-fixed-package.php" class="menu-link">
                  <div data-i18n="Input groups">Add Fixed Package</div>
                </a>
              </li>

              <li class="menu-item <?php echo strpos($filename, 'view-fixed-package.php') !== false ? 'active' : '' ?>">
                <a href="view-fixed-package.php" class="menu-link">
                  <div data-i18n="Input groups">View Fixed Package</div>
                </a>
              </li>

              <li class="menu-item <?php echo ($filename === 'view-taxi-booking.php') ? 'active' : ''; ?>">
                <a href="view-taxi-booking.php" class="menu-link">
                  <div data-i18n="Input groups">View Taxi</div>
                </a>
              </li>
              <li class="menu-item <?php echo ($filename === 'view-car.php') ? 'active' : ''; ?>">
                <a href="view-car.php" class="menu-link">
                  <div data-i18n="Input groups">View Car</div>
                </a>
              </li>

              <li class="menu-item <?php echo strpos($filename, 'add-blog.php') !== false ? 'active open' : ''; ?>">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                  <div data-i18n="Input groups">Blog</div>
                </a>
                <ul class="menu-sub">
                  <li class="menu-item <?php echo strpos($filename, 'view-blog.php') !== false ? 'active' : ''; ?>">
                    <a href="view-blog.php" class="menu-link">
                      <div data-i18n="View Blog">View Blog</div>
                    </a>
                  </li>
                  <li class="menu-item <?php echo strpos($filename, 'add-blog.php') !== false ? 'active' : ''; ?>">
                    <a href="add-blog.php" class="menu-link">
                      <div data-i18n="Add Blog">Add Blog</div>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="menu-item <?php echo strpos($filename, 'add-blog.php') !== false ? 'active open' : ''; ?>">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                  <div data-i18n="Input groups">Finance</div>
                </a>
                <ul class="menu-sub">
                  <li class="menu-item <?php echo strpos($filename, 'overview.php') !== false ? 'active' : ''; ?>">
                    <a href="overview.php" class="menu-link">
                      <div data-i18n="Overview">Overview </div>
                    </a>
                  </li>
                  <li class="menu-item <?php echo strpos($filename, 'sales.php') !== false ? 'active' : ''; ?>">
                    <a href="sales.php" class="menu-link">
                      <div data-i18n="Sales">Sales</div>
                    </a>
                  </li>
                  <li class="menu-item <?php echo strpos($filename, 'expense.php') !== false ? 'active' : ''; ?>">
                    <a href="expense.php" class="menu-link">
                      <div data-i18n="Expense ">Expense</div>
                    </a>
                  </li>

                  <li class="menu-item <?php echo strpos($filename, 'vendor.php') !== false ? 'active' : ''; ?>">
                    <a href="vendor.php" class="menu-link">
                      <div data-i18n="Vendor">Vendor</div>
                    </a>
                  </li>
                </ul>
              </li>



              <li class="menu-item <?php echo strpos($filename, 'add-blog.php') !== false ? 'active open' : ''; ?>">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                  <div data-i18n="Input groups">Reservation</div>
                </a>
                <ul class="menu-sub">
                  <li class="menu-item <?php echo strpos($filename, 'property_list.php') !== false ? 'active' : ''; ?>">
                    <a href="property_list.php" class="menu-link">
                      <div data-i18n="Overview">Property Management </div>
                    </a>
                  </li>
                  <li class="menu-item <?php echo strpos($filename, 'purchase_list.php') !== false ? 'active' : ''; ?>">
                    <a href="purchase_list.php" class="menu-link">
                      <div data-i18n="Overview">Purchase Management</div>
                    </a>
                  </li>
                  <li class="menu-item <?php echo strpos($filename, 'property_booking_list.php') !== false ? 'active' : ''; ?>">
                    <a href="property_booking_list.php" class="menu-link">
                      <div data-i18n="Sales">Booking Management</div>
                    </a>
                  </li>
                  <li class="menu-item <?php echo strpos($filename, 'reservation_detail.php') !== false ? 'active' : ''; ?>">
                    <a href="reservation_detail.php" class="menu-link">
                      <div data-i18n="Expense ">Reservation Details</div>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="menu-item">
                <a href="logout.php" class="menu-link">
                  <div data-i18n="Input groups">Logout</div>
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </aside>
      <!-- / Menu -->
      <script>
        document.addEventListener("DOMContentLoaded", function() {
          // Find all toggles
          const toggles = document.querySelectorAll('.menu-toggle');

          toggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
              e.preventDefault();

              const parent = toggle.closest('.menu-item');
              const submenu = parent.querySelector('.menu-sub');

              // Toggle open class on parent
              parent.classList.toggle('open');

              // Toggle display of submenu
              if (submenu) {
                if (submenu.style.display === "block") {
                  submenu.style.display = "none";
                } else {
                  submenu.style.display = "block";
                }
              }
            });
          });
        });
      </script>