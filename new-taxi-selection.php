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
        <h1 class="hero-title text-center">Taxi Selection</h1>
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
                    <div class=" col-sm-12 col-md-8 offset-md-2 col-lg-3 offset-lg-0">
                        <label class="form-label">From</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-map-marker-alt text-secondary"></i>
                            </span>
                            <select class="form-select" id="inputGroupSelect01">
                                <option selected>bangkok</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>

                        </div>
                    </div>
                    <div class=" col-sm-12 col-md-8 offset-md-2 col-lg-2 offset-lg-0">
                        <label class="form-label">To</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-map-marker-alt text-secondary"></i>
                            </span>
                            <select class="form-select" id="inputGroupSelect01">
                                <option selected>bangkok</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>

                        </div>
                    </div>
                    <div class="col-md col-sm-12 col-md-8 offset-md-2 col-lg-2 offset-lg-0">
                        <label class="form-label">Date</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-calendar text-theme"></i>
                            </span>
                            <input type="text" id="datepicker-end" class="date" readonly="readonly">
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
<div class="py-8 max-w-7xl">

    <div class="relative mb-10">
        <div class="scroll-btn prev" id="prevVehicle"><i class="fas fa-chevron-left"></i></div>
        <div class="scroll-btn next" id="nextVehicle"><i class="fas fa-chevron-right"></i></div>

        <div class="scroll-container" id="scrollContainer">
            <div class="flex space-x-4" id="vehiclesContainer">
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 mb-10" id="driversContainer">
    </div>

    <div class="flex justify-end mt-6">
        <button id="continueBtn" class="continue-btn bg-theme" disabled>Continue</button>
    </div>


</div>
<script src="assets/trip-assets/js/taxi-selection.js"></script>