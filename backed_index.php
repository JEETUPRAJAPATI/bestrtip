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

    <link href="assets/css/custom-package.min.css" rel="stylesheet" />
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
                        <a href="#">LOGO</a>
                    </div>
                    <div class="nav" id="toggle-nav">
                        <nav>
                            <ul>
                                <li><a href="#"><span class="nav-icon"><img src="/assets/images/nav-item-icon-1.svg" /></span><span>Travel</span></a></li>
                                <li><a href="#"><span class="nav-icon"><img src="/assets/images/nav-item-icon-1.svg" /></span><span>Hotel</span></a></li>
                                <li><a href="#"><span class="nav-icon"><img src="/assets/images/nav-item-icon-1.svg" /></span><span>Tour</span></a></li>
                                <li><a href="#"><span class="nav-icon"><img src="/assets/images/nav-item-icon-1.svg" /></span><span>Blog</span></a></li>
                                <li class="login-btn"><a href="login.php"><span class="nav-icon"><img src="/assets/images/login-icon.svg" /></span><span>Login</span></a></li>
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

                                <!-- Other form fields here -->
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
                                    <button type="button" class="search" id="searchBtn">Search</button>
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
            <div class="block">
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
                    <a class="card" href="#">
                        <div class="img-part">
                            <span>5D | 4N</span>
                            <figure><img src="./assets/images/tour-1.png" alt="tour" /></figure>
                        </div>
                        <h3>Srinagar Ladakh Manali Bike Trip Group Tour - Srinagar to Manali</h3>
                        <div class="column">Price: <span class="price">5,321/-</span></div>
                        <div class="column">Destination: <span>Ladakh</span></div>
                        <div class="plan-3d"><img src="./assets/images/3d.png" alt="3d" /> Customize Your Plan</div>
                    </a>
                    <a class="card" href="#">
                        <div class="img-part">
                            <span>5D | 4N</span>
                            <figure><img src="./assets/images/tour-2.png" alt="tour" /></figure>
                        </div>
                        <h3>Srinagar Ladakh Manali Bike Trip Group Tour - Srinagar to Manali</h3>
                        <div class="column">Price: <span class="price">5,321/-</span></div>
                        <div class="column">Destination: <span>Ladakh</span></div>
                        <div class="plan-3d"><img src="./assets/images/3d.png" alt="3d" /> Customize Your Plan</div>
                    </a>
                    <a class="card" href="#">
                        <div class="img-part">
                            <span>5D | 4N</span>
                            <figure><img src="./assets/images/tour-3.png" alt="tour" /></figure>
                        </div>
                        <h3>Srinagar Ladakh Manali Bike Trip Group Tour - Srinagar to Manali</h3>
                        <div class="column">Price: <span class="price">5,321/-</span></div>
                        <div class="column">Destination: <span>Ladakh</span></div>
                        <div class="plan-3d"><img src="./assets/images/3d.png" alt="3d" /> Customize Your Plan</div>
                    </a>
                    <a class="card" href="#">
                        <div class="img-part">
                            <span>5D | 4N</span>
                            <figure><img src="./assets/images/tour-4.png" alt="tour" /></figure>
                        </div>
                        <h3>Srinagar Ladakh Manali Bike Trip Group Tour - Srinagar to Manali</h3>
                        <div class="column">Price: <span class="price">5,321/-</span></div>
                        <div class="column">Destination: <span>Ladakh</span></div>
                        <div class="plan-3d"><img src="./assets/images/3d.png" alt="3d" /> Customize Your Plan</div>
                    </a>
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
            var selectedPackage = null;
            var selectedDuration = null;
            $('#durationDropdown').change(function() {
                var duration = $(this).val();
                selectedDuration = duration;
                $.ajax({
                    url: 'ajax/package_list.php',
                    type: 'POST',
                    data: {
                        duration: duration
                    },
                    success: function(data) {
                        $('#package_list').html(data);
                        $('#customModal').show();
                        $('#customModalOverlay').show();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });
            var selectedPackageData = null;

            $(document).on('click', '.form-check-input', function() {
                const row = $(this).closest('tr');
                selectedPackageData = {
                    id: $(this).val(),
                    name: row.find('td:nth-child(3)').text().trim(),
                    code: row.find('td:nth-child(2)').text().trim(),
                    duration: row.find('td:nth-child(4)').text().trim(),
                    category: row.find('td:nth-child(5) select.category-dropdown').val()
                };
                $('#package_list tr').removeClass('selected');
                row.addClass('selected');
            });
            $(document).on('change', '.category-dropdown', function() {
                const row = $(this).closest('tr');
                if (row.hasClass('selected')) {
                    selectedPackageData.category = $(this).val();
                    console.log('Selected Package Data (Dropdown Change):', selectedPackageData);
                }
            });
            $('#submitPackageSelection').click(function() {
                if (!selectedPackageData) {
                    alert('Please select a package before submitting!');
                    return;
                }

                selectedPackage = selectedPackageData;
                $('#customModal').hide();
                $('#customModalOverlay').hide();
            });
            $('#closeCustomModal, #closeCustomModalFooter').click(function() {
                $('#customModal').hide();
                $('#customModalOverlay').hide();
            });
            $(window).click(function(event) {
                if ($(event.target).is('#customModalOverlay')) {
                    $('#customModal').hide();
                    $('#customModalOverlay').hide();
                }
            });
            $('#searchBtn').click(function() {
                if (!selectedDuration) {
                    alert('Please select a duration before searching!');
                    return;
                }

                if (!selectedPackage) {
                    alert('Please select a package before searching!');
                    return;
                }
                const selectedTourType = $('input[name="tour-type"]:checked').val();

                if (!selectedTourType) {
                    alert('Please select a tour type!');
                    return;
                }
                console.log('Selected Tour Type:', selectedTourType);
                // Prepare query parameters for URL
                var queryString = '?id=' + selectedPackage.id +
                    '&name=' + encodeURIComponent(selectedPackage.name) +
                    '&code=' + encodeURIComponent(selectedPackage.code) +
                    '&duration=' + encodeURIComponent(selectedDuration) +
                    '&category=' + encodeURIComponent(selectedPackage.category) +
                    '&tour-type=' + encodeURIComponent(selectedTourType);
                window.location.href = '/besttripdeal/agent_query_generate.php' + queryString;
            });
        });
    </script>
</body>

</html>





















<!-- agent file backeup -->

<?php
session_start();
require_once './config/config.php';
require_once 'includes/agent_header.php';
is_agent_login();
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


// Get the latest active overview section
$db = getDbInstance();
$db->where('status', 'active'); // Filter by active status
$db->orderBy('id', 'desc'); // Fetch the latest record
$latestOverviewSection = $db->getOne('overview_section');

// Check if a record exists
if ($latestOverviewSection) {
    // Get images for the latest overview section
    $db = getDbInstance();
    $db->where('overview_section_id', $latestOverviewSection['id']);
    $db->orderBy('id', 'asc');
    $overviewSectionImages = $db->get('overview_section_images');
} else {
    $overviewSectionImages = [];
}

// Function to get images by overview_section_id
function getImagesBySectionId($images, $sectionId)
{
    $result = [];
    foreach ($images as $image) {
        if ($image['overview_section_id'] == $sectionId) {
            $result[] = $image['image_path'];
        }
    }
    return $result;
}

$selectedPackage = [
    'id' => isset($_GET['id']) ? $_GET['id'] : null,
    'name' => isset($_GET['name']) ? $_GET['name'] : null,
    'code' => isset($_GET['code']) ? $_GET['code'] : null,
    'duration' => isset($_GET['duration']) ? $_GET['duration'] : null,
    'category' => isset($_GET['category']) ? $_GET['category'] : null,
];

// Fetch the latest active package details
// Fetch the latest active package details
$db->where('package_id', $_GET['id']);
$db->orderBy('id', 'desc');
$latestPackage = $db->get('package_details');

// Debugging output to check the structure of the data
// echo "<pre>";
// print_r($latestPackage);
// echo "</pre>";
// die();
$transportPrices = [
    'coach' => 0,
    'tempo' => 0,
    'cryista' => 0,
    'innova' => 0,
    'zyalo_ertiga' => 0,
    'eco' => 0
];
foreach ($latestPackage as $package) {
    foreach ($transportPrices as $key => $price) {
        if (!empty($package[$key])) {
            $transportPrices[$key] += $package[$key];
        }
    }
}
// echo "<pre>";
// print_r($transportPrices);
// echo "</pre>";

?>

<link href="assets/css/custom-package.min.css" rel="stylesheet" />
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
                                </div>
                            </div>
                            <div class="group-tour-form">
                                <!-- Group Tour Form -->
                                <div class="single tour-type">
                                    <select name="duration" id="durationDropdown" required="">
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
                    <li><a href="#" class="selected">Overview</a></li>
                    <li><a href="#">Itinerary</a></li>
                    <li><a href="#">Transportation</a></li>
                    <li><a href="#">Hotels</a></li>
                    <li><a href="#">Extra Services</a></li>
                    <li><a href="#">Per Service</a></li>
                    <li><a href="#">Inclusions</a></li>
                    <li><a href="#">Exclusions</a></li>
                </ul>
            </div>
            <!-- Title and Gallery Section -->
            <div class="gallery-section">
                <div class="gallery-header">
                    <div class="gallery-title">
                        <h1>Ladakh Delight</h1>
                    </div>
                    <div class="rating-with-pkg">
                        <?php if ($selectedPackage['duration']) : ?>
                            <p><?= htmlspecialchars($selectedPackage['duration']) ?> | Customizable Package</p>
                        <?php else : ?>
                            <p>Package details not available.</p>
                        <?php endif; ?>
                        <div class="rating">4.5/5 - 245 Reviews</div>
                    </div>
                </div>
                <div class="gallery-images">
                    <?php if (!empty($overviewSectionImages)) : ?>
                        <div class="l-img">
                            <!-- Display the first image prominently -->
                            <img src="<?= htmlspecialchars($overviewSectionImages[0]['image_path']) ?>" alt="gallery" />
                        </div>
                        <div class="s-img">
                            <!-- Loop through all images -->
                            <?php foreach ($overviewSectionImages as $image) : ?>
                                <div class="col">
                                    <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="gallery" />
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p>No images available.</p>
                    <?php endif; ?>
                </div>

            </div>
            <!-- Overview section -->
            <div class="overview-section">
                <?php if ($latestOverviewSection) : ?>
                    <h2>Overview</h2>
                    <p><?= htmlspecialchars($latestOverviewSection['description']) ?></p>
                <?php else : ?>
                    <p>No overview available.</p>
                <?php endif; ?>
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

            <!-- select transportation -->
            <div class="select-transport">
                <h2>Select Transportation</h2>
                <div class="transport-main">
                    <?php foreach ($transportPrices as $key => $price) : ?>
                        <?php
                        $transportOptions = [
                            'coach' => 'Coach',
                            'tempo' => 'Tempo',
                            'cryista' => 'Cryista',
                            'innova' => 'Innova',
                            'zyalo_ertiga' => 'Zyalo Ertiga',
                            'eco' => 'Eco'
                        ];
                        $label = isset($transportOptions[$key]) ? $transportOptions[$key] : $key;
                        ?>
                        <div class="transport-col">
                            <label class="checkmark-container">
                                <div class="left">
                                    <input type="checkbox" class="check" name="transport[<?= $key ?>]" value="<?= $price ?>" />
                                    <span class="checkmark"></span>
                                    <span class="title"><?= $label ?></span>
                                    <span class="desc">Total price: <?= $price ?></span>
                                </div>
                                <div class="right">
                                    <button class="left" type="button" onclick="updateCount(this, -1)">-</button>
                                    <input name="transport_quantity[<?= $key ?>]" type="text" value="0" readonly>
                                    <button type="button" onclick="updateCount(this, 1)">+</button>
                                </div>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Hotel Details layout -->
            <div class="hotel-details-layout">
                <h2>Hotel Details</h2>
                <div class="hotels-list" id="hotel-list">

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
                    <ol id="exclusive">
                    </ol>
                </div>
                <div class="inclusion-col colm">
                    <h2>Inclusions</h2>
                    <ol id="inclusive">
                    </ol>
                </div>
            </div>

            <!-- Final Quotation -->
            <div class="final-quotation">
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
                            <div class="title">Duration:</div>
                            <div class="desc" id="summary-duration"></div>
                        </div>
                        <div class="single">
                            <div class="title">Travel Date:</div>
                            <div class="desc" id="summary-travel-date"></div>
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
                        <div class="single">
                            <div class="title"><label class="form-label">Your Budget</label></div>
                            <div class="desc" id="summary-duration"><input type="number" name="your_budget" class="form-control"></div>
                        </div>
                        <div class="single">
                            <button type="submit" class="btn btn-primary">Generate Quote</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="layout-page" style="width: 100%;">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="front-body-content">
                <form method="post">
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
                            </div>
                        </div>
                        <div class="right-part" style="position:fixed;right:0px">

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Extract parameters from the URL
        const urlParams = new URLSearchParams(window.location.search);
        const selectedTourType = urlParams.get('tour-type');
        const selectedDuration = urlParams.get('duration');
        console.log(selectedDuration);
        // Preselect the radio button for "tour-type"
        if (selectedTourType) {
            const radioButton = document.querySelector(`input[name="tour-type"][id="${CSS.escape(selectedTourType)}"]`);
            if (radioButton) {
                radioButton.checked = true;
            }
        }
        if (selectedDuration) {
            const decodedDuration = decodeURIComponent(selectedDuration); // Decode URL parameter
            const durationDropdown = document.getElementById('durationDropdown');
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

    var inclusive = [];
    var exclusive = [];
    $(document).ready(function() {
        // Get URL parameters for package id, duration, and category
        var urlParams = new URLSearchParams(window.location.search);
        var packageId = urlParams.get('id');
        var packageDuration = urlParams.get('duration');
        var packageCategory = urlParams.get('category');
        // Get query parameters from the URL
        const selectedTourType = urlParams.get('tour-type');
        console.log('type', selectedTourType);
        // Select the radio button matching the tour-type
        if (selectedTourType) {
            // Escape special characters in the value for querySelector
            const sanitizedValue = CSS.escape(selectedTourType.trim());

            const radioButton = document.querySelector(`input[name="tour-type"][value="${sanitizedValue}"]`);
            if (radioButton) {
                radioButton.checked = true;
            } else {
                console.log(`Radio button with value "${sanitizedValue}" not found.`);
            }
        }
        // Set the duration dropdown value based on the URL parameter
        if (packageDuration) {
            $('#duration').val(packageDuration);
            fetchPackagesByDuration(packageDuration); // Fetch packages based on the duration
        }


        // Trigger the AJAX request to update the package list if a duration is present
        $('#duration').change(function() {
            var selectedDuration = $(this).val();
            fetchPackagesByDuration(selectedDuration);
        });

        // Automatically select the package if the URL contains the package ID
        if (packageId) {
            selectPackageById(packageId);
        }

        // Function to set the selected category for the given package


        // Function to fetch packages by duration via AJAX
        function fetchPackagesByDuration(duration) {
            $.ajax({
                url: 'ajax/package_list.php', // Ensure this URL points to the correct endpoint
                type: 'POST',
                data: {
                    duration: duration
                },
                success: function(response) {
                    $('#package_list').html(response);
                    // If a package ID exists in the URL, select the corresponding package
                    var urlParams = new URLSearchParams(window.location.search);
                    var packageId = urlParams.get('id');
                    if (packageId) {
                        selectPackageById(packageId);
                        setCategory(packageCategory, packageId);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

        // Function to select package by ID from the list
        function selectPackageById(packageId) {
            $('#package_list input[type="radio"]').each(function() {
                if ($(this).val() == packageId) {
                    $(this).prop('checked', true);
                    setPackageId(packageId);
                }
            });
        }

        // Function to set the selected package ID
        function setPackageId(package_id) {
            $('input[name="package_id"]').val(package_id);
            setTimeout(function() {
                itinerary_list(); // Assuming this function is already defined to handle further processing
            }, 10);
        }

        function setCategory(category, package_id) {
            console.log('Category:', category, 'Package ID:', package_id);

            // Use the package_id to target the category dropdown by id
            var categoryDropdown = $('#categoryDropdown-' + package_id);

            console.log(categoryDropdown); // Check if the dropdown is being selected correctly

            if (categoryDropdown.length) {
                categoryDropdown.val(category); // Set the selected category value
            } else {
                console.error('Category dropdown not found for package id:', package_id);
            }

            // Optionally call the hotel_list function after setting the category
            setTimeout(function() {
                hotel_list(); // Assuming this function is defined to handle hotel list updates
            }, 10);
        }
    });




    function updateCount(button, increment) {
        var quantityInput = button.closest('.checkmark-container').querySelector('input[name^="transport_quantity"]');
        var currentValue = parseInt(quantityInput.value) || 0;
        currentValue += increment;
        if (currentValue >= 0) {
            quantityInput.value = currentValue;
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
            url: 'ajax/itinerary_list.php',
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
                url: 'ajax/service_list.php',
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
        let package_id = $('input[name="package_id"]').val()
        let category = $('#categoryDropdown-' + package_id).val();
        console.log(package_id);
        console.log(category);

        package_other_details(package_id, category)
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
            li.className = 'add-items';
            li.textContent = value;
            exclusiveList.appendChild(li);
        });


        var inclusiveList = document.getElementById('inclusive');
        inclusiveList.innerHTML = '';
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
<?php include  'includes/agent_footer.php'; ?>























<?php
session_start();
require_once '../config/config.php';
// require_once BASE_PATH . '/includes/auth_validate.php';

$default_categories = getCategories();
$db = getDbInstance();
$db->where('duration', $_POST['duration']);
$db->where('status', 'Active');
$results = $db->get("packages");
foreach ($results as $result): ?>
    <tr>
        <td>
            <div class="form-check">
                <!-- Radio button for package selection -->
                <input name="package_name" class="form-check-input" type="radio" value="<?= $result['id'] ?>" id="packageRadio<?= $result['id'] ?>" data-package-id="<?= $result['id'] ?>">
            </div>
        </td>
        <td>
            <span class="fw-medium">#00<?= $result['id'] ?></span>
        </td>
        <td><?= $result['package_name'] ?></td>
        <td><?= $result['duration'] ?></td>
        <td>
            <!-- Category dropdown for each package, using the category name as value -->
            <select class="form-select category-dropdown" id="categoryDropdown-<?= $result['id'] ?>" data-package-id="<?= $result['id'] ?>">
                <option>Choose Hotel Category</option>
                <?php foreach ($default_categories as $category): ?>
                    <option value="<?= $category ?>"><?= $category ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
<?php endforeach; ?>



<!-- onchange="return setCategory(this.value, <?= $result['id'] ?>);" -->
<!-- onClick="return setPackageId(<?= $result['id'] ?>)" -->