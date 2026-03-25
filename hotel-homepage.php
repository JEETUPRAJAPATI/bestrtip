<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="description" content="Travilio Ladakh,business,company,agency,modern,bootstrap4,tech,software">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="theme-name" content="Travilio Ladakh" />
  <title>Travel Website</title>

  <!-- Favicon -->
  <link rel="shortcut icon" type="image/x-icon" href="assets/trip-assets/images/favicon.ico" />

  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <!-- Icon Font Css -->
  <link rel="stylesheet" href="assets/trip-assets/plugins/themify/css/themify-icons.css" />
  <link rel="stylesheet" href="assets/trip-assets/plugins/fontawesome/css/all.min.css" >
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" /> -->

  <link rel="stylesheet" href="assets/trip-assets/plugins/animate-css/animate.css" />
  <!-- Slick Slider  CSS -->
  <link rel="stylesheet" href="assets/trip-assets/plugins/slick-carousel/slick/slick.css" />
  <link rel="stylesheet" href="assets/trip-assets/plugins/slick-carousel/slick/slick-theme.css" />
  <link rel="stylesheet" href="assets/trip-assets/css/style.css">
  <script src="assets/trip-assets/plugins/jquery/jquery.js"></script>
  <script src="assets/trip-assets/plugins/slick-carousel/slick/slick.min.js"></script>

</head>
<header class="navbar top-navigation">
	<div class="container-fluid">
		<div class="row align-items-center">
			<div class="col-4">
				<div class="d-flex align-items-center justify-content-start">
					<div class="btn close-btn" onclick="toggleHotelSidebar()">
						<i class="text-2xl fa-solid fa-bars"></i>
					</div>
					<!-- Logo -->
					<a href="#" class="logo">
						<img src="https://placehold.co/150x90" alt="Logo" />
					</a>
				</div>
			</div>
			<div class="col-8 justify-end">
				<!-- Hamburger (mobile) -->
				<input type="checkbox" id="menu-toggle" class="menu-toggle" />
				
				<!-- Desktop Navigation -->
				<nav class="nav-links">
					<div class="dropdown">
						<button class="dropbtn">
							<div class="country-nav d-flex">
								<img src="https://flagcdn.com/in.svg" class="flag" />
								<span class="arrow">&#9662;</span>
							</div>
						</button>
						<div class="dropdown-content">
							<a href="#"><img src="https://flagcdn.com/in.svg" />India</a>
							<a href="#"><img src="https://flagcdn.com/gb.svg" />UK</a>
							<a href="#"><img src="https://flagcdn.com/ca.svg" />Canada</a>
							<button onclick="openModal()">View
								More</button>
						</div>
					</div>
					<div class="dropdown">
						<button class="dropbtn">
							USD
							<span class="arrow">&#9662;</span>
						</button>
						<div class="dropdown-content">
							<a href="#"><img src="https://flagcdn.com/in.svg" />Rupees</a>
							<a href="#"><img src="https://flagcdn.com/gb.svg" />Dollar</a>
							<a href="#"><img src="https://flagcdn.com/ca.svg" />Canada</a>
							<a href="#"><img src="https://flagcdn.com/us.svg" class="flag" />US Dollar (USD)</a>
							<button onclick="openCurrencyModal()">View More</button>
						</div>
					</div>
					<a href="#">Go to app</a>
					<a href="#">Help</a>
					<a href="#">Recently viewed</a>
					<a href="#">Login</a>
					<button href="#" class="btn btn-theme">Sign Up</button>
				</nav>
				<!-- Mobile Sidebar -->
				<div class="mobile-menu">
					<div class="top">
						<label for="menu-toggle" class="close-icon">
							<svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" stroke-width="2"
								viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
								<path d="M6 18L18 6M6 6l12 12" />
							</svg>
						</label>
					</div>

					<div class="dropdown">
						<button class="btn btn-link dropbtn">
							<div class="country-nav d-flex">
								<img src="https://flagcdn.com/in.svg" class="flag" />
								<span class="arrow">&#9662;</span>
							</div>
						</button>
						<div class="dropdown-content">
							<a href="#"><img src="https://flagcdn.com/in.svg" />India</a>
							<a href="#"><img src="https://flagcdn.com/gb.svg" />UK</a>
							<a href="#"><img src="https://flagcdn.com/ca.svg" />Canada</a>
							<button class="btn btn-theme" onclick="openModal()">View
								More</button>
						</div>
					</div>
					<div class="dropdown">
						<button class="btn btn-info dropbtn">
							USD
							<span class="arrow">&#9662;</span>
						</button>
						<div class="dropdown-content">
							<a href="#"><img src="https://flagcdn.com/in.svg" />Rupees</a>
							<a href="#"><img src="https://flagcdn.com/gb.svg" />Dollar</a>
							<a href="#"><img src="https://flagcdn.com/ca.svg" />Canada</a>
							<a href="#"><img src="https://flagcdn.com/us.svg" class="flag" />US Dollar (USD)</a>
							<button class="btn btn-theme" onclick="openCurrencyModal()">View More</button>
						</div>
					</div>
					<a href="#">Go to app</a>
					<a href="#">Help</a>
					<a href="#">Recently viewed</a>
					<a href="#">Login</a>
					<button href="#" class="btn btn-theme">Sign Up</button>
				</div>
			</div>
		</div>
	</div>
</header>
<div id="countryModal" class="modal">
	<div class="modal-container">
		<button onclick="closeModal()" class="close-btn">&times;</button>
		<h2 class="modal-title">Select a Country</h2>

		<div class="country-list">
			<div class="country-item">
				<img src="https://flagcdn.com/in.svg" class="flag" />
				<span>India</span>
			</div>
			<div class="country-item">
				<img src="https://flagcdn.com/gb.svg" class="flag" />
				<span>UK</span>
			</div>
			<div class="country-item">
				<img src="https://flagcdn.com/ca.svg" class="flag" />
				<span>Canada</span>
			</div>
			<div class="country-item">
				<img src="https://flagcdn.com/us.svg" class="flag" />
				<span>USA</span>
			</div>
			<div class="country-item">
				<img src="https://flagcdn.com/de.svg" class="flag" />
				<span>Germany</span>
			</div>
			<div class="country-item">
				<img src="https://flagcdn.com/fr.svg" class="flag" />
				<span>France</span>
			</div>
			<!-- Add more as needed -->
		</div>
	</div>
</div>
<div id="currencyModal" class="modal">
	<div class="modal-container">
		<button onclick="closeCurrencyModal()" class="close-btn">&times;</button>
		<h2 class="modal-title">Select a Currency</h2>
		<div class="country-list">
			<div class="country-item"><img src="https://flagcdn.com/in.svg" class="flag" /> <span>Indian Rupee
					(INR)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/us.svg" class="flag" /> <span>US Dollar (USD)</span>
			</div>
			<div class="country-item"><img src="https://flagcdn.com/gb.svg" class="flag" /> <span>British Pound
					(GBP)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/eu.svg" class="flag" /> <span>Euro (EUR)</span>
			</div>
			<div class="country-item"><img src="https://flagcdn.com/jp.svg" class="flag" /> <span>Japanese Yen
					(JPY)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/ca.svg" class="flag" /> <span>Canadian Dollar
					(CAD)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/au.svg" class="flag" /> <span>Australian Dollar
					(AUD)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/ch.svg" class="flag" /> <span>Swiss Franc
					(CHF)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/cn.svg" class="flag" /> <span>Chinese Yuan
					(CNY)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/se.svg" class="flag" /> <span>Swedish Krona
					(SEK)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/nz.svg" class="flag" /> <span>New Zealand Dollar
					(NZD)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/sg.svg" class="flag" /> <span>Singapore Dollar
					(SGD)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/br.svg" class="flag" /> <span>Brazilian Real
					(BRL)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/za.svg" class="flag" /> <span>South African Rand
					(ZAR)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/ru.svg" class="flag" /> <span>Russian Ruble
					(RUB)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/kr.svg" class="flag" /> <span>South Korean Won
					(KRW)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/mx.svg" class="flag" /> <span>Mexican Peso
					(MXN)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/no.svg" class="flag" /> <span>Norwegian Krone
					(NOK)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/tr.svg" class="flag" /> <span>Turkish Lira
					(TRY)</span></div>
			<div class="country-item"><img src="https://flagcdn.com/th.svg" class="flag" /> <span>Thai Baht (THB)</span>
			</div>
		</div>
	</div>
</div>
<body>

<!-- Sidebar -->
<div class="d-md-block bg-white shadow-sm hotel-sidebar" id="sidebar">
    <button class="d-md-inline-block me-2 d-none btn" onclick="toggleHotelSidebar()">
        <i class="text-2xl fas fa-bars"></i>
    </button>
    <ul class="flex-column mt-4 nav">
        <li><a class="nav-link" href="#"><i class="fa-solid fa-hotel"></i><span>Group Tours</span></a></li>
        <li><a class="nav-link" href="#"><i class="fa-solid fa-plane-departure"></i><span>Tailor Made Holiday</span></a>
        </li>
        <li><a class="nav-link" href="#"><i class="fa-solid fa-train"></i><span>Hotels</span></a></li>
        <li><a class="nav-link" href="#"><i class="fa-solid fa-car"></i><span>Flights</span></a></li>
        <li><a class="nav-link" href="#"><i class="fa-solid fa-star"></i><span>Taxi + Hotel</span></a></li>
        <li><a class="nav-link" href="#"><i class="fa-solid fa-gift"></i><span>Flight + Hotel</span></a></li>
        <li><a class="nav-link" href="#"><i class="fa-solid fa-location-dot"></i><span>Map</span></a></li>
        <li><a class="nav-link" href="#"><i class="fa-solid fa-bolt"></i><span>Deals</span></a></li>
        <li><a class="nav-link" href="#"><i class="fa-solid fa-trophy"></i><span>Rewards</span></a></li>
        <li><a class="nav-link" href="#"><i class="fa-solid fa-phone-flip"></i><span>App</span></a></li>
    </ul>
</div>
<div class="mt-1 main-content" id="main-content">
    <section class="row justify-content-center">
    <section class="header-section">
        <div class="header-bg"></div>
        <div class="header-overlay"></div>
        <div class="container header-content">
            <h1 class="header-title">Your Trip Starts Here</h1>
            <!-- <div class="header-features">
                <div>
                    <i class="fas fa-lock me-2"></i>
                    Secure payment
                </div>
                <div>
                    <i class="fas fa-headset me-2"></i>
                    Support in approx. 30s
                </div>
            </div> -->
        </div>
    </section>

    <section class="home-serch-container p-3">
        <nav>
            <ul class="nav nav-tabs booking-nav justify-content-center row" id="bookingTabs" role="tablist">
                <li class="nav-item col mt-1 mb-1" role="presentation">
                    <button class="nav-link" id="group-tab" data-bs-toggle="tab" data-bs-target="#airport-content"
                        type="button" role="tab" aria-controls="airport-content" aria-selected="false">
                        <i class="fas fa-car"></i> Group tour
                    </button>
                </li>
                <li class="nav-item col mt-1 mb-1" role="presentation">
                    <button class="nav-link active" id="Holiday-tab" data-bs-toggle="tab"
                        data-bs-target="#trains-content" type="button" role="tab" aria-controls="trains-content"
                        aria-selected="true">
                        <i class="fas fa-train"></i> Tailor made Holiday
                    </button>
                </li>
                <li class="nav-item col mt-1 mb-1" role="presentation">
                    <button class="nav-link " id="hotels-tab" data-bs-toggle="tab" data-bs-target="#hotels-content"
                        type="button" role="tab" aria-controls="hotels-content" aria-selected="true">
                        <i class="fas fa-hotel"></i> Hotels
                    </button>
                </li>
                <li class="nav-item col mt-1 mb-1" role="presentation">
                    <button class="nav-link" id="flights-tab" data-bs-toggle="tab" data-bs-target="#flights-content"
                        type="button" role="tab" aria-controls="flights-content" aria-selected="false">
                        <i class="fas fa-plane"></i> Flights
                    </button>
                </li>
                <li class="nav-item col mt-1 mb-1" role="presentation">
                    <button class="nav-link" id="flight-hotel-tab" data-bs-toggle="tab"
                        data-bs-target="#flight-hotel-content" type="button" role="tab"
                        aria-controls="flight-hotel-content" aria-selected="false">
                        <i class="fas fa-suitcase"></i> Flight + Hotel
                    </button>
                </li>
                <li class="nav-item col mt-1 mb-1" role="presentation">
                    <button class="nav-link" id="taxi+hotel-tab" data-bs-toggle="tab"
                        data-bs-target="#attractions-content" type="button" role="tab"
                        aria-controls="attractions-content" aria-selected="false">
                        <i class="fas fa-monument"></i> Taxi + Hotel
                    </button>
                </li>
            </ul>
        </nav>

        <div class="tab-content" id="searchTabsContent">
            <!-- Holiday Tab Content (Shown in image) -->
            <section class="tab-pane fade show active" id="trains-content" role="tabpanel"
                aria-labelledby="Holiday-tab">
                <form>
                    <div class="row g-3 align-items-end p-3">
                        <div class="col-md-5">
                            <label for="attraction-location" class="form-label">Destination</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt"></i></span>
                                <select class="form-select" id="attraction-location">
                                    <option selected>New York, USA</option>
                                    <option>Paris, France</option>
                                    <option>London, UK</option>
                                    <option>Tokyo, Japan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="attraction-date" class="form-label">Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="far fa-calendar"></i></span>
                                <input type="text" class="form-control" id="attraction-date"
                                    placeholder="02 January 2026">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn search-btn w-100" type="submit"><i class="fas fa-search me-2"></i>
                                Search</button>
                        </div>
                    </div>
                </form>
            </section>

            <!-- Hotels Tab Content -->
            <section class="tab-pane fade" id="hotels-content" role="tabpanel" aria-labelledby="hotels-tab">
                <form>
                    <div class="row g-3 align-items-end p-3">
                        <div class="col-md-3">
                            <label for="location" class="form-label">Location</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt"></i></span>
                                <select class="form-select" id="location">
                                    <option selected>New York, USA</option>
                                    <option>Paris, France</option>
                                    <option>London, UK</option>
                                    <option>Tokyo, Japan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="check-in" class="form-label">Check In</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="far fa-calendar"></i></span>
                                <input type="datetime-local" class="form-control" id="check-in"
                                    placeholder="02 January 2026">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="check-out" class="form-label">Check Out</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="far fa-calendar"></i></span>
                                <input type="datetime-local" class="form-control" id="check-out"
                                    placeholder="02 January 2026">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="guests" class="form-label">Guests</label>
                            <div class="input-group" id="traveller-dropdown">
                                <span class="input-group-text bg-white"><i class="fas fa-user"></i></span>
                                <button type="button" id="traveller-toggle"
                                    class="form-control text-start d-flex justify-between align-items-center bg-white border-0">
                                    <span id="traveller-summary">2 Adults | 1 Room</span>
                                    <span class="ms-auto">▾</span>
                                </button>
                            </div>
                            <div id="traveller-panel"
                                class="hidden position-absolute z-10 mt-1 bg-white rounded shadow p-3 border"
                                style="width: 400px;">
                                <p class="text-lg font-bold">Please select the number of rooms and travellers</p>
                                <p class="text-sm text-gray-500 mb-4">Airlines charge adult fare for children over 11
                                    years old. Please enter
                                    travellers' ages accurately.</p>

                                <div class="space-y-4">
                                    <!-- Entry Template -->
                                    <div class="flex justify-between items-center border-t pt-3">
                                        <div>
                                            <div class="font-medium">Rooms</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button"
                                                onclick="adjustCount('rooms', -1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-500">−</button>
                                            <span id="rooms-count">1</span>
                                            <button type="button"
                                                onclick="adjustCount('rooms', 1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-blue-500 hover:bg-blue-100">+</button>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-medium">Adults</div>
                                            <div class="text-xs text-gray-500">18 years or above</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button"
                                                onclick="adjustCount('adults', -1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-500">−</button>
                                            <span id="adults-count">2</span>
                                            <button type="button"
                                                onclick="adjustCount('adults', 1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-blue-500 hover:bg-blue-100">+</button>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-medium">Children</div>
                                            <div class="text-xs text-gray-500">2–17 years</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button"
                                                onclick="adjustCount('children', -1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-500">−</button>
                                            <span id="children-count">0</span>
                                            <button type="button"
                                                onclick="adjustCount('children', 1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-blue-500 hover:bg-blue-100">+</button>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-medium">Infants</div>
                                            <div class="text-xs text-gray-500">Under 2 years</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button"
                                                onclick="adjustCount('infants', -1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-500">−</button>
                                            <span id="infants-count">0</span>
                                            <button type="button"
                                                onclick="adjustCount('infants', 1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-blue-500 hover:bg-blue-100">+</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-right">
                                    <button onclick="closeDropdown()" class="btn btn-theme">Done</button>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-2">
                            <button class="btn search-btn w-100" type="submit"><i class="fas fa-search me-2"></i>
                                Search</button>
                        </div>
                    </div>
                </form>
            </section>

            <!-- Flights Tab Content -->
            <section class="tab-pane fade" id="flights-content" role="tabpanel" aria-labelledby="flights-tab">
                <form>
                    <div class="row g-3 align-items-end p-3">
                        <div class="col-md-2">
                            <label for="flight-from" class="form-label">From</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-plane-departure"></i></span>
                                <select class="form-select" id="flight-from">
                                    <option selected>New York, USA</option>
                                    <option>Los Angeles, USA</option>
                                    <option>Chicago, USA</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="flight-to" class="form-label">To</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-plane-arrival"></i></span>
                                <select class="form-select" id="flight-to">
                                    <option selected>London, UK</option>
                                    <option>Paris, France</option>
                                    <option>Tokyo, Japan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="departure-date" class="form-label">Departure</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="far fa-calendar"></i></span>
                                <input type="datetime-local" class="form-control" id="departure-date"
                                    placeholder="02 January 2026">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="return-date" class="form-label">Return</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="far fa-calendar"></i></span>
                                <input type="datetime-local" class="form-control" id="return-date"
                                    placeholder="09 January 2026">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="package-guests" class="form-label">Guests</label>
                            <div class="input-group" id="package-traveller-dropdown">
                                <span class="input-group-text bg-white"><i class="fas fa-user"></i></span>
                                <button type="button" id="package-traveller-toggle"
                                    class="form-control text-start d-flex justify-between align-items-center bg-white border-0">
                                    <span id="package-traveller-summary">2 Adults | 1 Room</span>
                                    <span class="ms-auto">▾</span>
                                </button>
                            </div>
                            <div id="package-traveller-panel"
                                class="hidden position-absolute z-10 mt-1 bg-white rounded shadow p-3 border"
                                style="width: 400px;">
                                <p class="text-lg font-bold">Please select the number of rooms and travellers</p>
                                <p class="text-sm text-gray-500 mb-4">Airlines charge adult fare for children over 11
                                    years old. Please enter travellers' ages accurately.</p>

                                <div class="space-y-4">
                                    <!-- Entry Template -->
                                    <div class="flex justify-between items-center border-t pt-3">
                                        <div>
                                            <div class="font-medium">Rooms</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button"
                                                onclick="adjustCount('package-rooms', -1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-500">−</button>
                                            <span id="package-rooms-count">1</span>
                                            <button type="button"
                                                onclick="adjustCount('package-rooms', 1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-blue-500 hover:bg-blue-100">+</button>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-medium">Adults</div>
                                            <div class="text-xs text-gray-500">18 years or above</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button"
                                                onclick="adjustCount('package-adults', -1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-500">−</button>
                                            <span id="package-adults-count">2</span>
                                            <button type="button"
                                                onclick="adjustCount('package-adults', 1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-blue-500 hover:bg-blue-100">+</button>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-medium">Children</div>
                                            <div class="text-xs text-gray-500">2–17 years</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button"
                                                onclick="adjustCount('package-children', -1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-500">−</button>
                                            <span id="package-children-count">0</span>
                                            <button type="button"
                                                onclick="adjustCount('package-children', 1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-blue-500 hover:bg-blue-100">+</button>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-medium">Infants</div>
                                            <div class="text-xs text-gray-500">Under 2 years</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button"
                                                onclick="adjustCount('package-infants', -1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-500">−</button>
                                            <span id="package-infants-count">0</span>
                                            <button type="button"
                                                onclick="adjustCount('package-infants', 1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-blue-500 hover:bg-blue-100">+</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-right">
                                    <button onclick="closePackageDropdown()" class="btn btn-theme">Done</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn search-btn w-100" type="submit"><i class="fas fa-search me-2"></i>
                                Search</button>
                        </div>
                    </div>
                </form>
            </section>

            <!-- Flight + Hotel Tab Content -->
            <section class="tab-pane fade" id="flight-hotel-content" role="tabpanel" aria-labelledby="flight-hotel-tab">
                <form>
                    <div class="row g-3 align-items-end p-3">
                        <div class="col-md-2">
                            <label for="package-from" class="form-label">From</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-plane-departure"></i></span>
                                <select class="form-select" id="package-from">
                                    <option selected>New York, USA</option>
                                    <option>Los Angeles, USA</option>
                                    <option>Chicago, USA</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="package-to" class="form-label">To</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-plane-arrival"></i></span>
                                <select class="form-select" id="package-to">
                                    <option selected>London, UK</option>
                                    <option>Paris, France</option>
                                    <option>Tokyo, Japan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="package-depart" class="form-label">Departure</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="far fa-calendar"></i></span>
                                <input type="text" class="form-control" id="package-depart"
                                    placeholder="02 January 2026">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="package-return" class="form-label">Return</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="far fa-calendar"></i></span>
                                <input type="text" class="form-control" id="package-return"
                                    placeholder="09 January 2026">
                            </div>
                        </div>



                        <div class="col-md-3">
                            <label for="package-guests" class="form-label">Guests</label>
                            <div class="input-group" id="package-traveller-dropdown">
                                <span class="input-group-text bg-white"><i class="fas fa-user"></i></span>
                                <button type="button" id="package-traveller-toggle"
                                    class="form-control text-start d-flex justify-between align-items-center bg-white border-0">
                                    <span id="package-traveller-summary">2 Adults | 1 Room</span>
                                    <span class="ms-auto">▾</span>
                                </button>
                            </div>
                            <div id="package-traveller-panel"
                                class="hidden position-absolute z-10 mt-1 bg-white rounded shadow p-3 border"
                                style="width: 400px;">
                                <p class="text-lg font-bold">Please select the number of rooms and travellers</p>
                                <p class="text-sm text-gray-500 mb-4">Airlines charge adult fare for children over 11
                                    years old. Please enter travellers' ages accurately.</p>

                                <div class="space-y-4">
                                    <!-- Entry Template -->
                                    <div class="flex justify-between items-center border-t pt-3">
                                        <div>
                                            <div class="font-medium">Rooms</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button"
                                                onclick="adjustCount('package-rooms', -1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-500">−</button>
                                            <span id="package-rooms-count">1</span>
                                            <button type="button"
                                                onclick="adjustCount('package-rooms', 1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-blue-500 hover:bg-blue-100">+</button>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-medium">Adults</div>
                                            <div class="text-xs text-gray-500">18 years or above</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button"
                                                onclick="adjustCount('package-adults', -1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-500">−</button>
                                            <span id="package-adults-count">2</span>
                                            <button type="button"
                                                onclick="adjustCount('package-adults', 1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-blue-500 hover:bg-blue-100">+</button>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-medium">Children</div>
                                            <div class="text-xs text-gray-500">2–17 years</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button"
                                                onclick="adjustCount('package-children', -1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-500">−</button>
                                            <span id="package-children-count">0</span>
                                            <button type="button"
                                                onclick="adjustCount('package-children', 1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-blue-500 hover:bg-blue-100">+</button>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-medium">Infants</div>
                                            <div class="text-xs text-gray-500">Under 2 years</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="button"
                                                onclick="adjustCount('package-infants', -1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-500">−</button>
                                            <span id="package-infants-count">0</span>
                                            <button type="button"
                                                onclick="adjustCount('package-infants', 1); event.stopPropagation();"
                                                class="rounded-full border text-xl w-8 h-8 flex items-center justify-center text-blue-500 hover:bg-blue-100">+</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 text-right">
                                    <button onclick="closePackageDropdown()" class="btn btn-theme">Done</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn search-btn w-100" type="submit"><i class="fas fa-search me-2"></i>
                                Search</button>
                        </div>
                    </div>
                </form>
            </section>

            <!-- Group Tab Transfers Tab Content -->
            <section class="tab-pane fade" id="airport-content" role="tabpanel" aria-labelledby="group-tab">
                <form>
                    <div class="row g-3 align-items-end p-3">
                        <div class="col-md-5">
                            <label for="attraction-location" class="form-label">Destination</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt"></i></span>
                                <select class="form-select" id="attraction-location">
                                    <option selected>New York, USA</option>
                                    <option>Paris, France</option>
                                    <option>London, UK</option>
                                    <option>Tokyo, Japan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="attraction-date" class="form-label">Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="far fa-calendar"></i></span>
                                <input type="text" class="form-control" id="attraction-date"
                                    placeholder="02 January 2026">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn search-btn w-100" type="submit"><i class="fas fa-search me-2"></i>
                                Search</button>
                        </div>
                    </div>
                </form>
            </section>

            <!-- Taxi + hotel Tab Content -->
            <section class="tab-pane fade" id="attractions-content" role="tabpanel" aria-labelledby="taxi+hotel-tab">
                <form>

                    <div class="row g-3 align-items-end p-3">
                        <div class="col-md-3">
                            <label for="pickup-location" class="form-label">Pickup Location</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt"></i></span>
                                <select class="form-select" id="pickup-location">
                                    <option selected>New York Airport, USA</option>
                                    <option>Los Angeles Airport, USA</option>
                                    <option>Chicago Airport, USA</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="pickup-date" class="form-label">Pickup Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="far fa-calendar"></i></span>
                                <input type="text" class="form-control" id="pickup-date" placeholder="02 January 2026">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="return-car-date" class="form-label">Return Date</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="far fa-calendar"></i></span>
                                <input type="text" class="form-control" id="return-car-date"
                                    placeholder="09 January 2026">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn search-btn w-100" type="submit"><i class="fas fa-search me-2"></i>
                                Search</button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </section>



    <section class="offers-section col-12 col-md-8">
        <div class="container">
            <div class="offers-carousel">
                <div class="offer-card offer-card-1">
                    <div class="offer-content">
                        <img src="https://plus.unsplash.com/premium_photo-1722169898897-b887c5548223?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxmZWF0dXJlZC1waG90b3MtZmVlZHwxfHx8ZW58MHx8fHx8"
                            alt="">
                    </div>
                </div>
                <div class="offer-card offer-card-2">
                    <div class="offer-content">
                        <img src="https://plus.unsplash.com/premium_photo-1722169898897-b887c5548223?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxmZWF0dXJlZC1waG90b3MtZmVlZHwxfHx8ZW58MHx8fHx8"
                            alt="">
                    </div>
                </div>
                <div class="offer-card offer-card-3">
                    <div class="offer-content">
                        <img src="https://plus.unsplash.com/premium_photo-1722169898897-b887c5548223?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxmZWF0dXJlZC1waG90b3MtZmVlZHwxfHx8ZW58MHx8fHx8"
                            alt="">
                    </div>
                </div>
                <div class="offer-card offer-card-1">
                    <div class="offer-content">
                        <img src="https://plus.unsplash.com/premium_photo-1722169898897-b887c5548223?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxmZWF0dXJlZC1waG90b3MtZmVlZHwxfHx8ZW58MHx8fHx8"
                            alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

</section>
    
    
    <section class="section-feature">
    <div class="container">
        <div class="featured-hotels section-heading">
            <h1 class="section-title">Featured Hotels</h1>
            <p class="featured-description">Quality as judged by customers. Book at the ideal price!</p>
        </div>

        <div class="feature-tab">
            <ul class="tab-list" id="cityTab" role="tablist">
                <li><button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all"
                        type="button" role="tab">All</button></li>
                <li><button class="nav-link" id="luxury-tab" data-bs-toggle="tab" data-bs-target="#luxury" type="button"
                        role="tab">Luxury</button></li>
                <li><button class="nav-link" id="standard-tab" data-bs-toggle="tab" data-bs-target="#standard"
                        type="button" role="tab">Standard</button></li>
                <li><button class="nav-link" id="villa-tab" data-bs-toggle="tab" data-bs-target="#villa" type="button"
                        role="tab">Villa</button></li>
                <li><button class="nav-link" id="cottage-tab" data-bs-toggle="tab" data-bs-target="#cottage"
                        type="button" role="tab">Cottages</button></li>
                <li><button class="nav-link" id="shared-tab" data-bs-toggle="tab" data-bs-target="#shared" type="button"
                        role="tab">Shared Space</button></li>
            </ul>
        </div>

        <div class="tab-content" id="cityTabContent">
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                <div id="all-cards" class="card-carousel"></div>
            </div>
            <div class="tab-pane fade" id="luxury" role="tabpanel">
                <div id="luxury-cards" class="card-carousel"></div>
            </div>
            <div class="tab-pane fade" id="standard" role="tabpanel">
                <div id="standard-cards" class="card-carousel"></div>
            </div>
            <div class="tab-pane fade" id="villa" role="tabpanel">
                <div id="villa-cards" class="card-carousel"></div>
            </div>
            <div class="tab-pane fade" id="cottage" role="tabpanel">
                <div id="cottage-cards" class="card-carousel"></div>
            </div>
            <div class="tab-pane fade" id="shared" role="tabpanel">
                <div id="shared-cards" class="card-carousel"></div>
            </div>
        </div>
        <template id="card-template">
    <div class="hotel-feature-card">
        <div class="card-container">
            <img src="{image}" class="card-image" alt="{title}" />
            <button class="like-button">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </button>
            <div class="rating-badge">
                <svg xmlns="http://www.w3.org/2000/svg" class="star-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="rating-score">{rating}</span>
                <span class="rating-text">({reviews} reviews)</span>
            </div>
        </div>
        <div class="card-details">
            <h5 class="text-theme">{title}</h5>
            <div class="card-location">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon-location" viewBox="0 0 20 20" fill="currentColor"
                    width="16" height="16">
                    <path fill-rule="evenodd"
                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                        clip-rule="evenodd" />
                </svg>
                <span class="location-text">{location}</span>
            </div>
            <div class="card-footer">
                <div class="card-price">
                    <h6>{price}</h6>
                    <p class="per-person">/ person</p>
                </div>
                <button class="btn btn-theme">Book Now</button>
            </div>
        </div>
    </div>
</template>
    </div>
</section>
    <section class="section-tours">
    <div class="container">
        <div
            class="section-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <div class="section-heading">
                <h2 class="section-title">GROUP TOURS</h2>
                <p class="section-subtitle">Top 10 curated group travel experiences</p>
            </div>
            <a href="#" class="btn-theme mt-3 mt-md-0">View All</a>
        </div>

        <div id="group-tour-carousel" class="slick-carousel"></div>
    </div>
</section>
    <section class="section-packages">
    <div class="container">
        <div class="popular-packages section-heading">
            <h1 class="section-title">Popular Packages</h1>
            <p class="packages-description">Explore our most sought-after travel experiences around the world!</p>
        </div>

        <div class="packages-tabs">
            <ul class=" countries-tab-list tab-list" id="countriesTab" role="tablist">
                <li><button class="country-nav-link active" id="worldwide-tab" data-bs-toggle="tab"
                        data-bs-target="#worldwide" type="button" role="tab">Worldwide</button></li>
                <li><button class="country-nav-link" id="india-tab" data-bs-toggle="tab" data-bs-target="#india"
                        type="button" role="tab">India</button></li>
                <li><button class="country-nav-link" id="thailand-tab" data-bs-toggle="tab" data-bs-target="#thailand"
                        type="button" role="tab">Thailand</button></li>
                <li><button class="country-nav-link" id="japan-tab" data-bs-toggle="tab" data-bs-target="#japan"
                        type="button" role="tab">Japan</button></li>
                <li><button class="country-nav-link" id="europe-tab" data-bs-toggle="tab" data-bs-target="#europe"
                        type="button" role="tab">Europe</button></li>
                <li><button class="country-nav-link" id="australia-tab" data-bs-toggle="tab" data-bs-target="#australia"
                        type="button" role="tab">Australia</button></li>
            </ul>
        </div>

        <div class="tab-content mt-1" id="countriesTabContent">
            <div class="tab-pane fade show active" id="worldwide" role="tabpanel">
                <div id="worldwide-packages" class="package-carousel"></div>
            </div>
            <div class="tab-pane fade" id="india" role="tabpanel">
                <div id="india-packages" class="package-carousel"></div>
            </div>
            <div class="tab-pane fade" id="thailand" role="tabpanel">
                <div id="thailand-packages" class="package-carousel"></div>
            </div>
            <div class="tab-pane fade" id="japan" role="tabpanel">
                <div id="japan-packages" class="package-carousel"></div>
            </div>
            <div class="tab-pane fade" id="europe" role="tabpanel">
                <div id="europe-packages" class="package-carousel"></div>
            </div>
            <div class="tab-pane fade" id="australia" role="tabpanel">
                <div id="australia-packages" class="package-carousel"></div>
            </div>
        </div>
        <template id="package-card-template">
    <div class="travel-package-card">
        <div class="package-card-container">
            <img src="{image}" class="package-image" alt="{title}" />
            <button class="package-save-button">
                <svg xmlns="http://www.w3.org/2000/svg" class="save-icon" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </button>
            <div class="package-rating-badge">
                <svg xmlns="http://www.w3.org/2000/svg" class="package-star-icon" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path
                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="package-score">{rating}</span>
                <span class="package-reviews">({reviews} reviews)</span>
            </div>
            <div class="package-duration-badge">
                <span class="duration-text">{duration}</span>
            </div>
        </div>
        <div class="package-details">
            <h5 class="package-title">{title}</h5>
            <div class="package-location">
                <svg xmlns="http://www.w3.org/2000/svg" class="package-location-icon" viewBox="0 0 20 20"
                    fill="currentColor" width="16" height="16">
                    <path fill-rule="evenodd"
                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                        clip-rule="evenodd" />
                </svg>
                <span class="package-location-text">{location}</span>
            </div>
            <div class="package-inclusions">
                <span class="inclusion-item">{inclusions}</span>
            </div>
            <div class="package-footer">
                <div class="package-price">
                    <h6>{price}</h6>
                    <p class="price-person">/ person</p>
                </div>
                <button class="btn package-book-btn">Book Now</button>
            </div>
        </div>
    </div>
</template>
    </div>
</section>
    <section class="section-flight">
    <!-- Header Section -->
    <div class="container">
        <div class="flight-header section-heading">
            <h1 class="section-title">Flight Offer Deals</h1>
            <p>Competitive fares for your route-specific searches.</p>
        </div>
        <!-- Flight Cards-->
        <article class="flight-carousel">
            <div class="flight-card bg-white p-4 rounded-xl shadow-md m-2">
    <!-- Airline Logo -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <img src="https://thehardcopy.co/wp-content/uploads/Vistara-Images-7-768x515.png" alt="American Airlines"
                class="w-20 h-20 rounded-2">
            <h4 class=" font-bold">American Airlines</h4>
        </div>
    </div>

    <!-- Route & Date -->
    <div class="mb-3">
        <h4 class="font-semibold text-lg text-gray-800">New York ⇄ Miami</h4>
        <p class="text-sm text-gray-500">Sat, Apr 26 - Fri, May 2</p>
    </div>

    <!-- Class & Price -->
    <div class="flex justify-between items-center mb-4">
        <span class="text-sm text-gray-600">Economy</span>
        <span class="text-md font-bold text-gray-800">From ₹ 13,080</span>
    </div>

    <!-- CTA -->
    <div class="flex justify-end">
        <button class="btn btn-theme px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Book Now
        </button>
    </div>
</div>
            <div class="flight-card bg-white p-4 rounded-xl shadow-md m-2">
    <!-- Airline Logo -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <img src="https://thehardcopy.co/wp-content/uploads/Vistara-Images-7-768x515.png" alt="American Airlines"
                class="w-20 h-20 rounded-2">
            <h4 class=" font-bold">American Airlines</h4>
        </div>
    </div>

    <!-- Route & Date -->
    <div class="mb-3">
        <h4 class="font-semibold text-lg text-gray-800">New York ⇄ Miami</h4>
        <p class="text-sm text-gray-500">Sat, Apr 26 - Fri, May 2</p>
    </div>

    <!-- Class & Price -->
    <div class="flex justify-between items-center mb-4">
        <span class="text-sm text-gray-600">Economy</span>
        <span class="text-md font-bold text-gray-800">From ₹ 13,080</span>
    </div>

    <!-- CTA -->
    <div class="flex justify-end">
        <button class="btn btn-theme px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Book Now
        </button>
    </div>
</div>
            <div class="flight-card bg-white p-4 rounded-xl shadow-md m-2">
    <!-- Airline Logo -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <img src="https://thehardcopy.co/wp-content/uploads/Vistara-Images-7-768x515.png" alt="American Airlines"
                class="w-20 h-20 rounded-2">
            <h4 class=" font-bold">American Airlines</h4>
        </div>
    </div>

    <!-- Route & Date -->
    <div class="mb-3">
        <h4 class="font-semibold text-lg text-gray-800">New York ⇄ Miami</h4>
        <p class="text-sm text-gray-500">Sat, Apr 26 - Fri, May 2</p>
    </div>

    <!-- Class & Price -->
    <div class="flex justify-between items-center mb-4">
        <span class="text-sm text-gray-600">Economy</span>
        <span class="text-md font-bold text-gray-800">From ₹ 13,080</span>
    </div>

    <!-- CTA -->
    <div class="flex justify-end">
        <button class="btn btn-theme px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Book Now
        </button>
    </div>
</div>
            <div class="flight-card bg-white p-4 rounded-xl shadow-md m-2">
    <!-- Airline Logo -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <img src="https://thehardcopy.co/wp-content/uploads/Vistara-Images-7-768x515.png" alt="American Airlines"
                class="w-20 h-20 rounded-2">
            <h4 class=" font-bold">American Airlines</h4>
        </div>
    </div>

    <!-- Route & Date -->
    <div class="mb-3">
        <h4 class="font-semibold text-lg text-gray-800">New York ⇄ Miami</h4>
        <p class="text-sm text-gray-500">Sat, Apr 26 - Fri, May 2</p>
    </div>

    <!-- Class & Price -->
    <div class="flex justify-between items-center mb-4">
        <span class="text-sm text-gray-600">Economy</span>
        <span class="text-md font-bold text-gray-800">From ₹ 13,080</span>
    </div>

    <!-- CTA -->
    <div class="flex justify-end">
        <button class="btn btn-theme px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Book Now
        </button>
    </div>
</div>
            <div class="flight-card bg-white p-4 rounded-xl shadow-md m-2">
    <!-- Airline Logo -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <img src="https://thehardcopy.co/wp-content/uploads/Vistara-Images-7-768x515.png" alt="American Airlines"
                class="w-20 h-20 rounded-2">
            <h4 class=" font-bold">American Airlines</h4>
        </div>
    </div>

    <!-- Route & Date -->
    <div class="mb-3">
        <h4 class="font-semibold text-lg text-gray-800">New York ⇄ Miami</h4>
        <p class="text-sm text-gray-500">Sat, Apr 26 - Fri, May 2</p>
    </div>

    <!-- Class & Price -->
    <div class="flex justify-between items-center mb-4">
        <span class="text-sm text-gray-600">Economy</span>
        <span class="text-md font-bold text-gray-800">From ₹ 13,080</span>
    </div>

    <!-- CTA -->
    <div class="flex justify-end">
        <button class="btn btn-theme px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Book Now
        </button>
    </div>
</div>
            <div class="flight-card bg-white p-4 rounded-xl shadow-md m-2">
    <!-- Airline Logo -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <img src="https://thehardcopy.co/wp-content/uploads/Vistara-Images-7-768x515.png" alt="American Airlines"
                class="w-20 h-20 rounded-2">
            <h4 class=" font-bold">American Airlines</h4>
        </div>
    </div>

    <!-- Route & Date -->
    <div class="mb-3">
        <h4 class="font-semibold text-lg text-gray-800">New York ⇄ Miami</h4>
        <p class="text-sm text-gray-500">Sat, Apr 26 - Fri, May 2</p>
    </div>

    <!-- Class & Price -->
    <div class="flex justify-between items-center mb-4">
        <span class="text-sm text-gray-600">Economy</span>
        <span class="text-md font-bold text-gray-800">From ₹ 13,080</span>
    </div>

    <!-- CTA -->
    <div class="flex justify-end">
        <button class="btn btn-theme px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Book Now
        </button>
    </div>
</div>
            <div class="flight-card bg-white p-4 rounded-xl shadow-md m-2">
    <!-- Airline Logo -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <img src="https://thehardcopy.co/wp-content/uploads/Vistara-Images-7-768x515.png" alt="American Airlines"
                class="w-20 h-20 rounded-2">
            <h4 class=" font-bold">American Airlines</h4>
        </div>
    </div>

    <!-- Route & Date -->
    <div class="mb-3">
        <h4 class="font-semibold text-lg text-gray-800">New York ⇄ Miami</h4>
        <p class="text-sm text-gray-500">Sat, Apr 26 - Fri, May 2</p>
    </div>

    <!-- Class & Price -->
    <div class="flex justify-between items-center mb-4">
        <span class="text-sm text-gray-600">Economy</span>
        <span class="text-md font-bold text-gray-800">From ₹ 13,080</span>
    </div>

    <!-- CTA -->
    <div class="flex justify-end">
        <button class="btn btn-theme px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Book Now
        </button>
    </div>
</div>
            <div class="flight-card bg-white p-4 rounded-xl shadow-md m-2">
    <!-- Airline Logo -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <img src="https://thehardcopy.co/wp-content/uploads/Vistara-Images-7-768x515.png" alt="American Airlines"
                class="w-20 h-20 rounded-2">
            <h4 class=" font-bold">American Airlines</h4>
        </div>
    </div>

    <!-- Route & Date -->
    <div class="mb-3">
        <h4 class="font-semibold text-lg text-gray-800">New York ⇄ Miami</h4>
        <p class="text-sm text-gray-500">Sat, Apr 26 - Fri, May 2</p>
    </div>

    <!-- Class & Price -->
    <div class="flex justify-between items-center mb-4">
        <span class="text-sm text-gray-600">Economy</span>
        <span class="text-md font-bold text-gray-800">From ₹ 13,080</span>
    </div>

    <!-- CTA -->
    <div class="flex justify-end">
        <button class="btn btn-theme px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Book Now
        </button>
    </div>
</div>
            <div class="flight-card bg-white p-4 rounded-xl shadow-md m-2">
    <!-- Airline Logo -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <img src="https://thehardcopy.co/wp-content/uploads/Vistara-Images-7-768x515.png" alt="American Airlines"
                class="w-20 h-20 rounded-2">
            <h4 class=" font-bold">American Airlines</h4>
        </div>
    </div>

    <!-- Route & Date -->
    <div class="mb-3">
        <h4 class="font-semibold text-lg text-gray-800">New York ⇄ Miami</h4>
        <p class="text-sm text-gray-500">Sat, Apr 26 - Fri, May 2</p>
    </div>

    <!-- Class & Price -->
    <div class="flex justify-between items-center mb-4">
        <span class="text-sm text-gray-600">Economy</span>
        <span class="text-md font-bold text-gray-800">From ₹ 13,080</span>
    </div>

    <!-- CTA -->
    <div class="flex justify-end">
        <button class="btn btn-theme px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Book Now
        </button>
    </div>
</div>
            <div class="flight-card bg-white p-4 rounded-xl shadow-md m-2">
    <!-- Airline Logo -->
    <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
            <img src="https://thehardcopy.co/wp-content/uploads/Vistara-Images-7-768x515.png" alt="American Airlines"
                class="w-20 h-20 rounded-2">
            <h4 class=" font-bold">American Airlines</h4>
        </div>
    </div>

    <!-- Route & Date -->
    <div class="mb-3">
        <h4 class="font-semibold text-lg text-gray-800">New York ⇄ Miami</h4>
        <p class="text-sm text-gray-500">Sat, Apr 26 - Fri, May 2</p>
    </div>

    <!-- Class & Price -->
    <div class="flex justify-between items-center mb-4">
        <span class="text-sm text-gray-600">Economy</span>
        <span class="text-md font-bold text-gray-800">From ₹ 13,080</span>
    </div>

    <!-- CTA -->
    <div class="flex justify-end">
        <button class="btn btn-theme px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            Book Now
        </button>
    </div>
</div>
        </article>

    </div>
</section>
    <section class="section-taxis">
    <div class="container">
        <div class="popular-taxis">
            <h1 class="section-title">City Taxi Services</h1>
            <p class="taxis-description">Find reliable taxi services across major cities!</p>
        </div>

        <div class="taxis-tabs">
            <ul class="cities-tab-list" id="citiesTab" role="tablist">
                <li><button class="city-nav-link active" id="newyork-tab" data-bs-toggle="tab" data-bs-target="#newyork"
                        type="button" role="tab">New York</button></li>
                <li><button class="city-nav-link" id="london-tab" data-bs-toggle="tab" data-bs-target="#london"
                        type="button" role="tab">London</button></li>
                <li><button class="city-nav-link" id="tokyo-tab" data-bs-toggle="tab" data-bs-target="#tokyo"
                        type="button" role="tab">Tokyo</button></li>
                <li><button class="city-nav-link" id="paris-tab" data-bs-toggle="tab" data-bs-target="#paris"
                        type="button" role="tab">Paris</button></li>
                <li><button class="city-nav-link" id="dubai-tab" data-bs-toggle="tab" data-bs-target="#dubai"
                        type="button" role="tab">Dubai</button></li>
                <li><button class="city-nav-link" id="sydney-tab" data-bs-toggle="tab" data-bs-target="#sydney"
                        type="button" role="tab">Sydney</button></li>
            </ul>
        </div>

        <div class="tab-content" id="citiesTabContent">
            <div class="tab-pane fade show active" id="newyork" role="tabpanel">
                <div id="newyork-taxis" class="taxi-carousel"></div>
            </div>
            <div class="tab-pane fade" id="london" role="tabpanel">
                <div id="london-taxis" class="taxi-carousel"></div>
            </div>
            <div class="tab-pane fade" id="tokyo" role="tabpanel">
                <div id="tokyo-taxis" class="taxi-carousel"></div>
            </div>
            <div class="tab-pane fade" id="paris" role="tabpanel">
                <div id="paris-taxis" class="taxi-carousel"></div>
            </div>
            <div class="tab-pane fade" id="dubai" role="tabpanel">
                <div id="dubai-taxis" class="taxi-carousel"></div>
            </div>
            <div class="tab-pane fade" id="sydney" role="tabpanel">
                <div id="sydney-taxis" class="taxi-carousel"></div>
            </div>
        </div>
        <template id="taxi-card-template">
    <div class="taxi-service-card">
        <div class="taxi-card-container">
            <img src="{image}" class="taxi-image" alt="{name}" />
            <button class="taxi-save-button">
                <svg xmlns="http://www.w3.org/2000/svg" class="save-icon" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </button>
            <div class="taxi-type-badge bg-success-subtle">
                <span class="type-text">{type}</span>
            </div>
        </div>
        <div class="taxi-details">
            <h5 class="taxi-title">{name}</h5>
            <div class="taxi-capacity">
                <div class="capacity-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="taxi-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                    </svg>
                    <span class="capacity-text">{passengers} passengers</span>
                </div>
                <div class="capacity-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="taxi-icon" fill="currentColor" viewBox="0 0 384 512">
                        <path
                            d="M144 56c0-4.4 3.6-8 8-8l80 0c4.4 0 8 3.6 8 8l0 72-96 0 0-72zm176 72l-32 0 0-72c0-30.9-25.1-56-56-56L152 0C121.1 0 96 25.1 96 56l0 72-32 0c-35.3 0-64 28.7-64 64L0 416c0 35.3 28.7 64 64 64c0 17.7 14.3 32 32 32s32-14.3 32-32l128 0c0 17.7 14.3 32 32 32s32-14.3 32-32c35.3 0 64-28.7 64-64l0-224c0-35.3-28.7-64-64-64zM112 224l160 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-160 0c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 128l160 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-160 0c-8.8 0-16-7.2-16-16s7.2-16 16-16z" />

                    </svg>
                    <span class="capacity-text">{bags} bags</span>
                </div>
            </div>
            <div class="taxi-amenities">
                <span class="amenities-item">{amenities}</span>
            </div>
            <div class="taxi-footer">
                <button class="btn-theme">Book Now</button>
            </div>
        </div>
    </div>
</template>
    </div>
</section>
    <section class="container py-5">
    <div class="row justify-content-center">

        <div class="travila-carousel col-12 col-md-8">
            <div>
                <img src="https://images.unsplash.com/photo-1741606552241-fbd67e574f7f?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                    class="carousel-img" alt="Slide 1">
            </div>
            <div>
                <img src="https://images.unsplash.com/photo-1741606552241-fbd67e574f7f?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                    class="carousel-img" alt="Slide 2">
            </div>
            <div>
                <img src="https://images.unsplash.com/photo-1741606552241-fbd67e574f7f?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                    class="carousel-img" alt="Slide 3">
            </div>
        </div>
    </div>
</section>
    <footer class="new_footer_area bg_color">
	<section class="new_footer_top">
		<div class="container">
			<div class="row">
				<div class="col-lg-3 col-md-6">
					<div class="f_widget about-widget">
						<h3 class="f-title f_600 t_color f_size_18">About us
						</h3>
						<ul class="list-unstyled f_list">
							<li><a href="#">Best taxi ride </a></li>
							<li><a href="#">Group tour Holiday</a></li>
							<li><a href="#">Tailor made holiday </a></li>
							<li><a href="#">Hotel Booking</a></li>
							<li><a href="#">Tourist Guide </a></li>
							<li><a href="#">Prepurchase Flight</a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="f_widget about-widget pl_70">
						<h3 class="f-title f_600 t_color f_size_18">Our Partnership </h3>
						<ul class="list-unstyled f_list">
							<li><a href="#">taxi partner</a></li>
							<li><a href="#">Hotelier</a></li>
							<li><a href="#">Guide partner</a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="f_widget about-widget pl_70">
						<h3 class="f-title f_600 t_color f_size_18">Help</h3>
						<ul class="list-unstyled f_list">
							<li><a href="#">List your hotel</a></li>
							<li><a href="#">Term &amp; conditions</a></li>
							<li><a href="#">Reporting</a></li>
							<li><a href="#">Documentation</a></li>
							<li><a href="#">Support Policy</a></li>
							<li><a href="#">Privacy</a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="f_widget payment_channels">
						<h3 class="f-title f_600 t_color f_size_18">payment channels</h3>
						<ul class="f_social_icon list-unstyled">
							<li>
								<a href="#"><img src="assets/trip-assets/images/payment/1.png" alt=""></a>
							</li>
							<li>
								<a href="#"><img src="assets/trip-assets/images/payment/1.png" alt=""></a>
							</li>
							<li>
								<a href="#"><img src="assets/trip-assets/images/payment/1.png" alt=""></a>
							</li>
							<li>
								<a href="#"><img src="assets/trip-assets/images/payment/1.png" alt=""></a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="footer_bg">
			<div class="footer_bg_one"></div>
			<div class="footer_bg_two"></div>
		</div>
	</section>
	<section class="new_footer_bottom">
		<div class="container">
			<div class="row">
				<div class="col-12 col-lg-3 col-md-3">
					<a href="#">General Policy</a>
				</div>
				<div class="col-12 col-lg-3 col-md-3">
					<a href="#">Privacy Policy</a>
				</div>
				<div class="col-12 col-lg-6 col-sm-6">
				<p class="mb-0 f_400">© ladkah travel Inc.. 2019 All rights reserved.</p>
			</div>
			</div>
		</div>
	</section>

</footer>
</div>


<script src="assets/trip-assets/js/hotel-home.js"></script>
<script src="assets/trip-assets/js/home.js"></script>
<script src="assets/trip-assets/js/section-feature.js"></script>
<script src="assets/trip-assets/js/section-tour.js"></script>
<script src="assets/trip-assets/js/section-packages.js"></script>
<script src="assets/trip-assets/js/section-taxi.js"></script>

<script src="assets/trip-assets/plugins/bootstrap/js/bootstrap.min.js"></script>


<script src="assets/trip-assets/js/country-modal.js"></script>
<script src="assets/trip-assets/js/currency-modal.js"></script>
<script src="assets/trip-assets/js/script.js"></script>
</body>

</html>