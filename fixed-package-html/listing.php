<!DOCTYPE html>
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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">

    <link href="css/style.min.css" rel="stylesheet" />
    <link href="css/page.min.css" rel="stylesheet" />
    <link href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css' rel='stylesheet'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.js"></script>
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

        .premium-container {
            font-family: 'Outfit', 'Inter', sans-serif;
            border-radius: 40px;
            overflow: hidden;
        }

        /* Detail View UI (Expanded) */
        .premium-detail {
            display: flex;
            gap: 40px;
            padding: 40px;
            background: #fff;
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
        }

        .premium-content { flex: 1; }
        .premium-sidebar { width: 300px; }

        .premium-gallery {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }
        .premium-hero { width: 100%; height: 350px; object-fit: cover; border-radius: 20px; }
        .premium-sub-imgs { display: flex; flex-direction: column; gap: 15px; }
        .premium-sub { width: 100%; height: 167px; object-fit: cover; border-radius: 20px; }

        .premium-tabs { border-bottom: 2px solid #F5F5F5; display: flex; gap: 30px; margin-bottom: 30px; }
        .premium-tab-btn { padding: 0 5px 12px 5px; cursor: pointer; color: #888; font-weight: 500; background: none; border: none; font-family: inherit; font-size: 15px; }
        .premium-tab-btn.active { color: var(--primary); font-weight: 700; border-bottom: 3px solid var(--primary); }
        .premium-tab-content { display: none; line-height: 1.7; color: #555; }
        .premium-tab-content.active { display: block; }

        .premium-price-card {
            background: #fff;
            border: 1px solid #F2F2F2;
            border-radius: 30px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        }

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
    </style>

    <script>
        function switchPremiumTab(btn, tabId) {
            let container = jQuery(btn).closest('.premium-content');
            container.find('.premium-tab-btn').removeClass('active');
            jQuery(btn).addClass('active');
            container.find('.premium-tab-content').removeClass('active');
            jQuery('#' + tabId).addClass('active');
        }
    </script>
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
                            <!-- <ul>
                                <li><a href="<?php echo SITE_URL?>/places-to-visit-in-ladakh/">Places to Visit in Ladakh</a></li>
                                <li><a href="<?php echo SITE_URL?>/things-to-do-in-ladakh/">Things to Do in Ladakh</a></li>
                            </ul> -->
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
<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__FILE__).'/../config/config.php';


$db = getDbInstance();
$db->orderBy("name","asc");
$categoryData = $db->get("category");
// print_r($categoryData);exit;

$db = getDbInstance();
$db->orderBy("name","asc");
$destinationData = $db->get("destination");

$db = getDbInstance();
$db->orderBy("name","asc");
if (isset($_GET['category']) && !empty($_GET['category'])  ) {
    $db->where("tour_category",$_GET['category']);
} else {
    $db->where("tour_category",'1');
}

if (isset($_GET['tour_start']) && !empty($_GET['tour_start'])  ) {
    $db->where("tour_start",$_GET['tour_start']);
}

if (isset($_GET['tour_end']) && !empty($_GET['tour_end'])  ) {
     $db->where("tour_end",$_GET['tour_end']);
}

if (isset($_GET['newOrderDates']) && !empty($_GET['newOrderDates'])  ) {
    $date = json_encode([$_GET['newOrderDates']]);  // Wrap the date in an array and encode it as JSON
    $db->where("JSON_CONTAINS(tour_start_date, ?)", [$date]);
}
$packageData = $db->get("fixed_package");
// if (!isset($packageData)|| empty($packageData) || count($packageData) = 0) {
//     echo 'aaaaaaaaaaaaaaaaa';
//     echo "<script type='text/javascript'>
//             window.onload = function() {
//                 alert('No data found please change tour');
//             };
//           </script>";
// }
// print_r($packageData);exit;

// Function to get the name by id
function getNameById($array, $id) {
    foreach ($array as $item) {
        if ($item['id'] == $id) {
            return $item['name'];
        }
    }
    return null; // Return null if no matching ID is found
}
?>
    <div class="page-content">
        <!-- searching panel -->
        <div class="searching-main-box">
            <div class="top-header">
                <ul>
                    <li class="selected"><a href="#">Group Tour</a></li>
                    <li><a href="#">Customize Package</a></li>
                    <li><a href="#">Flight</a></li>
                    <li><a href="#">Hotel</a></li>
                    <li><a href="#">Taxi & Bike</a></li>
                </ul>
            </div>
            <div class="search-panel">
                <form>
                <div class="search-form">
                    <div class="row">
                        <div class="radio-button-group">
                            <?php
                            foreach ($categoryData as $rows ) { 
                                if (isset($_GET['category']) && !empty($_GET['category']) ) {
                                    $checked = ($rows['id'] == $_GET['category']) ? 'checked' : '';
                                } else {
                                    $checked = ($rows['id'] == 1) ? 'checked' : '';
                                }
                                 
                            ?>
                            <div class="single-button">
                                <input type="radio" value="<?php echo ucfirst( $rows['id']); ?>" name="category" id="<?php echo ucfirst( $rows['id']); ?>" <?php echo  $checked; ?> /> <label for="bike"><?php echo ucfirst( $rows['name']); ?></label>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <i class="fa fa-map-marker icon" aria-hidden="true"></i>
                            <label>From</label>
                            <!-- <input type="text" list="destinationfrom" /> -->
                            <select id="destinationfrom"  name="tour_start"  required>
                                 <?php
                                      foreach ($destinationData as $rows ) {
                                          $selected = '';
                                          if (isset($_GET['tour_start']) && !empty($_GET['tour_start'])) {
                                              $selected = $rows['id'] == $_GET['tour_start'] ? 'selected':'';
                                          }
                                     ?>
                                    <option value="<?php echo $rows['id']; ?>" <?php echo $selected; ?> ><?php echo ucfirst( $rows['name']); ?></option>
                                    <?php
                                      }
                                    ?>
                            </select>
                        </div>
                        <div class="col">
                            <i class="fa fa-map-marker icon" aria-hidden="true"></i>
                            <label>To</label>
                            <!-- <input type="text" list="destinationto" /> -->
                            <select id="destinationto"  name="tour_end"  required>
                                 <?php
                                      foreach ($destinationData as $rows ) :
                                         $selected = '';
                                          if (isset($_GET['tour_end']) && !empty($_GET['tour_end'])) {
                                              $selected = $rows['id'] == $_GET['tour_end'] ? 'selected':'';
                                          }
                                     ?>
                                    <option value="<?php echo $rows['id']; ?>" <?php echo $selected; ?> ><?php echo ucfirst( $rows['name']); ?></option>
                                    <?php
                                    endforeach;
                                    ?>
                            </select>
                        </div>
                        <div class="col">
                            <i class="fa fa-calendar icon" aria-hidden="true"></i>
                            <label>Select Date</label>
                            <input type="text" id="newOrderDates" name="newOrderDates" value="<?php echo (isset($_GET['newOrderDates']) && !empty ($_GET['newOrderDates']) ) ? $_GET['newOrderDates']:''; ?>" required>
                        </div>
                    </div>
                    <div class="row mb-0">
                        <div class="disclaimer-text">
                           
                        </div>
                        <button type="submit" class="submitButton"> Search </button>
                    </div>
                </div>
                </form>
            </div>
        </div>

        <!-- Listing layout start -->
        <div class="search-listing">
            <div class="search-heading"><h1>Group Tour - <?php echo getNameById( $destinationData, $_GET['tour_start']).' to ';  ?> <?php echo getNameById( $destinationData, $_GET['tour_end']);  ?></h1></div>
            <?php foreach($packageData as $package) { ?>
            <div class="single-list expanded" id="package-container">
                <div class="img-area">
                    <img src="https://agent.go2ladakh.in/<?php echo $package['image']  ?>" alt="leh" />
                </div>
                <div class="content-area">
                    <div class="selected-destination"> <?php echo  $package['name'];  ?> </br> Group Tour - <?php echo getNameById( $destinationData, $package['tour_start']);  ?> to <?php echo getNameById( $destinationData, $package['tour_end']);  ?></div>
                    <?php 
                    $startDate = new DateTime( json_decode($package['tour_start_date'])[0] );  // Replace with dynamic data if needed
                    $endDate = new DateTime( json_decode($package['tour_end_date'])[0] );    // Replace with dynamic data if needed
        
                    // Calculate the difference between the two dates
                    $interval = $startDate->diff($endDate);
        
                    // Get the number of days and nights
                    $days = $interval->days;  // Total number of days
                    $nights = $days - 1;      // Nights would be 1 less than days
                    ?>
                    <div class="duration"> <?php echo $days; ?> Days <?php echo $nights;  ?> Nights</div>
                    <div class="short-description"><?php echo $package['description']  ?></div>
                </div>
                <div class="dated">
                    <span>Dated:</span> <?php echo $startDate->format('d F Y'); ?>
                </div>
                <div class="pax-number">
                    <span>No. of Pax</span> <?php echo $package['inventory']  ?>
                </div>
                <div class="pax-number">
                    <span>Price Starting From</span> <i class="fa fa-inr" aria-hidden="true"></i>
                    <?php echo $package['solo_riding_on_double_room'] ? $package['solo_riding_on_double_room']: $package['dbl_twin'] ?>
                </div>
                <div class="button">
                    <a href="#" class="book-now-button">View Details</a>
                    <!-- <a href="#" class="view-detail-button">View Details</a> -->
                </div>

                <div class="detail-view premium-container">
                    <div class="premium-detail">
                        <div class="premium-content">
                            <!-- Premium Gallery -->
                            <div class="premium-gallery">
                                <img src="https://agent.go2ladakh.in/<?php echo $package['image'] ?>" class="premium-hero">
                                <div class="premium-sub-imgs">
                                    <img src="https://images.unsplash.com/photo-1540518614846-7eded433c457?q=80&w=600" class="premium-sub">
                                    <img src="https://images.unsplash.com/photo-1584132967334-10e028bd69f7?q=80&w=600" class="premium-sub">
                                </div>
                            </div>

                            <div class="premium-tabs">
                                <button class="premium-tab-btn active" onclick="switchPremiumTab(this, 'desc-<?php echo $package['id'] ?>')">Description</button>
                                <button class="premium-tab-btn" onclick="switchPremiumTab(this, 'feat-<?php echo $package['id'] ?>')">Feature</button>
                                <button class="premium-tab-btn" onclick="switchPremiumTab(this, 'virt-<?php echo $package['id'] ?>')">Virtual</button>
                                <button class="premium-tab-btn" onclick="switchPremiumTab(this, 'revs-<?php echo $package['id'] ?>')">Reviews</button>
                            </div>

                            <div id="desc-<?php echo $package['id'] ?>" class="premium-tab-content active">
                                <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 15px;">Overview</h3>
                                <p><?php echo $package['description'] ?></p>
                            </div>

                            <div id="feat-<?php echo $package['id'] ?>" class="premium-tab-content">
                                <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 15px;">Key Features</h3>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                    <?php 
                                    // Combine itinerary highlights and inclusions as "Features"
                                    echo $package['add_inclusions'] ? $package['add_inclusions'] : 'Premium amenities included.';
                                    ?>
                                </div>
                            </div>

                            <div id="virt-<?php echo $package['id'] ?>" class="premium-tab-content">
                                <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 15px;">Virtual Experience</h3>
                                <div style="background: #f9f9f9; padding: 40px; border-radius: 12px; text-align: center; border: 2px dashed #EEE;">
                                    <img src="https://img.icons8.com/material-rounded/48/B19470/360-degrees.png" style="margin-bottom: 15px;">
                                    <p style="color: #666;">Experience this destination in immersive 360° VR.</p>
                                    <button class="premium-tab-btn" style="border: 1px solid var(--primary); margin-top: 10px;">Launch Virtual Tour</button>
                                </div>
                            </div>

                            <div id="revs-<?php echo $package['id'] ?>" class="premium-tab-content">
                                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 25px;">
                                    <div style="font-size: 48px; font-weight: 700; color: var(--primary);">4.9</div>
                                    <div>
                                        <div style="font-weight: 700; font-size: 18px;">Excellent</div>
                                        <div style="color: #888; font-size: 14px;">Based on <?php echo (15 + ($package['id'] % 10)); ?> verified reviews</div>
                                    </div>
                                </div>
                                <div class="review-item" style="border-top: 1px solid #EEE; padding: 15px 0;">
                                    <div style="font-weight: 600; margin-bottom: 5px;">"Unforgettable Experience"</div>
                                    <p style="font-size: 14px; color: #666;">Everything was perfectly organized. The attention to detail is remarkable.</p>
                                </div>
                            </div>

                        </div>

                        <div class="premium-sidebar">
                            <div class="premium-price-card">
                                <div style="font-size: 24px; font-weight: 700; color: var(--primary); margin-bottom: 10px;">
                                    ₹<?php echo $package['solo_riding_on_double_room'] ? $package['solo_riding_on_double_room']: $package['dbl_twin'] ?>
                                </div>
                                <div style="font-size: 14px; color: #888; margin-bottom: 25px;">Starting Price Per Person</div>
                                
                                <a href="https://wa.me/919512087057" style="display: block; background: var(--primary); color: #fff; text-align: center; padding: 15px; border-radius: 12px; text-decoration: none; font-weight: 700; box-shadow: 0 5px 15px rgba(177,148,112,0.3);">Book via WhatsApp</a>
                                
                                <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #EEE;">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px; font-size: 14px;">
                                        <img src="https://img.icons8.com/material-rounded/20/B19470/check-all.png"> Instant Confirmation
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 10px; font-size: 14px;">
                                        <img src="https://img.icons8.com/material-rounded/20/B19470/check-all.png"> Best Price Guaranteed
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
            <?php } ?>
        </div>
        <!-- Listing layout end -->

         
        <!-- boxes banner -->
        <div class="boxes-banner">
            <div class="box">
                <img src="./images/box-banner1.jpg" alt="banner" />
                <div class="box-content">
                    <h3>Pack it and track it</h3>
                    <p>More destinations, cruises and new guided tour packages in Asia.</p>
                    <a href="#">Learn More</a>
                </div>
            </div>
            <div class="box">
                <img src="./images/box-banner2.jpg" alt="banner" />
                <div class="box-content">
                    <h3>Pack it and track it</h3>
                    <p>More destinations, cruises and new guided tour packages in Asia.</p>
                    <a href="#">Learn More</a>
                </div>
            </div>
            <div class="box">
                <img src="./images/box-banner3.jpg" alt="banner" />
                <div class="box-content">
                    <h3>Pack it and track it</h3>
                    <p>More destinations, cruises and new guided tour packages in Asia.</p>
                    <a href="#">Learn More</a>
                </div>
            </div>
        </div>
    </div>

    <div class="fixed-footer">
        <ul>
             <li class="selected"><a href="#">Group Tour</a></li>
            <li><a href="https://agent.go2ladakh.in/fixed-package-html/customize-package.php">Customize Package</a></li>
            <li><a href="#">Flight</a></li>
            <li><a href="#">Hotel</a></li>
            <li><a href="#">Taxi & Bike</a></li>
            <!--<li class="selected"><a href="#"><i class="fa fa-users icon" aria-hidden="true"></i> Group Tour</a></li>-->
            <!--<li><a href="#"><i class="fa fa-file-text-o icon" aria-hidden="true"></i> Customize Package</a></li>-->
            <!--<li><a href="#"><i class="fa fa-hotel icon" aria-hidden="true"></i> Hotel</a></li>-->
            <!--<li><a href="#"><i class="fa fa-taxi icon" aria-hidden="true"></i> Taxi & Bike</a></li>-->
        </ul>
    </div>

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
                        <li><a href="https://www.go2ladakh.in/north-east-tour-packages/">Northeast  Tour packages</a></li>
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
                        <li><a href="https://www.go2ladakh.in/covid19-tnc/">COVID 19 travel  advisory 2021</a></li>
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
</div>
</div>

<script>
    $('#dbl_twin').on('change', function(event) {
        const dbl_twin = Number( $('#dbl_twin').val());
        const dbl_twin_price = Number($('#dbl_twin_price').val())
        document.getElementById("dbl_twin_room").innerHTML= dbl_twin >3 ? Math. trunc( (dbl_twin/3)+1):1; 
        document.getElementById("dbl_twin_value").innerHTML= '<i class="fa fa-inr" aria-hidden="true"></i>'+dbl_twin *dbl_twin_price; 
        totalValue ();
        
    });
    
    $('#single_sharing').on('change', function(event) {
        const single_sharing = Number( $('#single_sharing').val());
        const single_sharing_price = Number($('#single_sharing_price').val());
        document.getElementById("single_sharing_room").innerHTML= single_sharing; 
        document.getElementById("single_sharing_value").innerHTML= '<i class="fa fa-inr" aria-hidden="true"></i>'+single_sharing *single_sharing_price; 
        totalValue ();
    });
    
     $('#extra_bed_18').on('change', function(event) {
        const extra_bed_18 = Number( $('#extra_bed_18').val());
        const extra_bed_18_price = Number($('#dbl_twin_price').val());
        document.getElementById("extra_bed_18_room").innerHTML= extra_bed_18 ; 
        document.getElementById("extra_bed_18_value").innerHTML= '<i class="fa fa-inr" aria-hidden="true"></i>'+extra_bed_18 *extra_bed_18_price; 
        totalValue ();
    });
    
    $('#child_no_bed').on('change', function(event) {
        const child_no_bed = Number( $('#child_no_bed').val());
        const child_no_bed_price = Number($('#child_no_bed_price').val());
        document.getElementById("child_no_bed_room").innerHTML= child_no_bed ; 
        document.getElementById("child_no_bed_value").innerHTML= '<i class="fa fa-inr" aria-hidden="true"></i>'+child_no_bed *child_no_bed_price; 
        totalValue ();
    });
    
     $('#child_with_bed').on('change', function(event) {
        const child_with_bed = Number( $('#child_with_bed').val());
        const child_with_bed_price = Number($('#child_with_bed_price').val());
        document.getElementById("child_with_bed_room").innerHTML= child_with_bed; 
        document.getElementById("child_with_bed_value").innerHTML= '<i class="fa fa-inr" aria-hidden="true"></i>'+ child_with_bed *child_with_bed_price; 
        totalValue ();
    });
    
    function totalValue () {
        const dbl_twin = Number( $('#dbl_twin').val());
        const dbl_twin_price = Number($('#dbl_twin_price').val());
        
        const single_sharing = Number( $('#single_sharing').val());
        const single_sharing_price = Number($('#single_sharing_price').val());
        
        const extra_bed_18 = Number( $('#extra_bed_18').val());
        const extra_bed_18_price = Number($('#dbl_twin_price').val());
        
        const child_no_bed = Number( $('#child_no_bed').val());
        const child_no_bed_price = Number($('#child_no_bed_price').val());
        
        const child_with_bed = Number( $('#child_with_bed').val());
        const child_with_bed_price = Number($('#child_with_bed_price').val());
        
        let total_final_amount = 0;
        let html = ``;
        let totalPax = 0;
        
        if (dbl_twin && dbl_twin > 0) {
            html += `<p><span>DBL/TWIN:</span> <strong>${dbl_twin} pax</strong></p>`;
            totalPax += dbl_twin;
            total_final_amount += dbl_twin * dbl_twin_price;
        }
        if (single_sharing && single_sharing > 0) {
            html += `<p><span>Single sharing:</span> <strong>${single_sharing} pax</strong></p>`;
            totalPax += single_sharing;
            total_final_amount += single_sharing * single_sharing_price;
        }
        if (extra_bed_18 && extra_bed_18 > 0) {
            html += `<p><span>Extra bed 18+:</span> <strong>${extra_bed_18} pax</strong></p>`;
            totalPax += extra_bed_18;
            total_final_amount += extra_bed_18 * extra_bed_18_price;
        }
        if (child_no_bed && child_no_bed > 0) {
            html += `<p><span>Child no bed:</span> <strong>${child_no_bed} pax</strong></p>`;
            totalPax += child_no_bed;
            total_final_amount += child_no_bed * child_no_bed_price;
        }
        if (child_with_bed && child_with_bed > 0) {
            html += `<p><span>child with bed:</span> <strong>${child_with_bed} pax</strong></p>`;
            totalPax += child_with_bed;
            total_final_amount += child_with_bed * child_with_bed_price;
        }
        
        
        
        html += `<p class="add-top-border"><span><strong>Total Pax:</strong></span> <strong>${totalPax} pax</strong></p>`;
        document.getElementById("booking_summary").innerHTML = html ;
        document.getElementById("total_final_amount").innerHTML = `<i class="fa fa-inr" aria-hidden="true"></i>${total_final_amount}` ;
        if (total_final_amount) {
            let payableAmount = Math. trunc(total_final_amount/10);  
            document.getElementById("payable_amount").innerHTML =`<i class="fa fa-inr" aria-hidden="true"></i> ${payableAmount}`;
        }
        
    }
    
</script>

<!--bike data-->
<script>
    $('#solo_riding_on_double_room').on('change', function(event) {
        const solo_riding_on_double_room = Number( $('#solo_riding_on_double_room').val());
        const solo_riding_on_double_room_price = Number($('#solo_riding_on_double_room_price').val());
        document.getElementById("solo_riding_on_double_room_room").innerHTML= solo_riding_on_double_room >2 ? Math. trunc( (solo_riding_on_double_room/2)+ (solo_riding_on_double_room%2)):1; 
        document.getElementById("solo_riding_on_double_room_bike").innerHTML= solo_riding_on_double_room; 
        document.getElementById("solo_riding_on_double_room_value").innerHTML= '<i class="fa fa-inr" aria-hidden="true"></i>'+solo_riding_on_double_room *solo_riding_on_double_room_price; 
        totalValueSolo ();
        
    });
    
    $('#double_riding_on_double_room').on('change', function(event) {
        const double_riding_on_double_room = Number( $('#double_riding_on_double_room').val());
        const double_riding_on_double_room_price = Number($('#double_riding_on_double_room_price').val());
        document.getElementById("double_riding_on_double_room_room").innerHTML=  double_riding_on_double_room >2 ? Math. trunc( (double_riding_on_double_room/2)+ (double_riding_on_double_room%2)):1; 
        document.getElementById("double_riding_on_double_room_bike").innerHTML=  double_riding_on_double_room >2 ? Math. trunc( (double_riding_on_double_room/2)+ (double_riding_on_double_room%2)):1; 
        document.getElementById("double_riding_on_double_room_value").innerHTML= '<i class="fa fa-inr" aria-hidden="true"></i>'+double_riding_on_double_room * double_riding_on_double_room_price; 
        totalValueSolo ();
    });
    
     $('#solo_riding_on_single_room').on('change', function(event) {
        const solo_riding_on_single_room = Number( $('#solo_riding_on_single_room').val());
        const solo_riding_on_single_room_price = Number($('#solo_riding_on_single_room_price').val());
        document.getElementById("solo_riding_on_single_room_room").innerHTML= solo_riding_on_single_room; 
        document.getElementById("solo_riding_on_single_room_bike").innerHTML= solo_riding_on_single_room;
        document.getElementById("solo_riding_on_single_room_value").innerHTML= '<i class="fa fa-inr" aria-hidden="true"></i>'+solo_riding_on_single_room * solo_riding_on_single_room_price; 
        totalValueSolo ();
    });
    
    $('#double_riding_on_single_room').on('change', function(event) {
        const double_riding_on_single_room = Number( $('#double_riding_on_single_room').val());
        const double_riding_on_single_room_price = Number($('#double_riding_on_single_room_price').val());
        document.getElementById("double_riding_on_single_room_room").innerHTML= double_riding_on_single_room; 
        document.getElementById("double_riding_on_single_room_bike").innerHTML= double_riding_on_single_room >2 ? Math. trunc( (double_riding_on_single_room/2)+ (double_riding_on_single_room%2)):1;  
        document.getElementById("double_riding_on_single_room_value").innerHTML= '<i class="fa fa-inr" aria-hidden="true"></i>'+double_riding_on_single_room * double_riding_on_single_room_price; 
        totalValueSolo ();
    });
    
    $('#three_rider_2_bike_1_triple_room').on('change', function(event) {
        const three_rider_2_bike_1_triple_room = Number( $('#three_rider_2_bike_1_triple_room').val());
        const three_rider_2_bike_1_triple_room_price = Number($('#three_rider_2_bike_1_triple_room_price').val());
        document.getElementById("three_rider_2_bike_1_triple_room_room").innerHTML= three_rider_2_bike_1_triple_room >3 ? Math. trunc( (three_rider_2_bike_1_triple_room/3)+1):1; 
        document.getElementById("three_rider_2_bike_1_triple_room_bike").innerHTML= three_rider_2_bike_1_triple_room >2 ? Math. trunc( (three_rider_2_bike_1_triple_room/2)+ (three_rider_2_bike_1_triple_room%2)):1;
        document.getElementById("three_rider_2_bike_1_triple_room_value").innerHTML= '<i class="fa fa-inr" aria-hidden="true"></i>'+ three_rider_2_bike_1_triple_room * three_rider_2_bike_1_triple_room_price; 
        totalValueSolo ();
    });
    
    $('#three_rider_3_bike_1_triple_room').on('change', function(event) {
        const three_rider_3_bike_1_triple_room = Number( $('#three_rider_3_bike_1_triple_room').val());
        const three_rider_3_bike_1_triple_room_price = Number($('#three_rider_3_bike_1_triple_room_price').val());
        document.getElementById("three_rider_3_bike_1_triple_room_room").innerHTML= three_rider_3_bike_1_triple_room >3 ? Math. trunc( (three_rider_3_bike_1_triple_room/3)+1):1; 
        document.getElementById("three_rider_3_bike_1_triple_room_bike").innerHTML= three_rider_3_bike_1_triple_room;
        document.getElementById("three_rider_3_bike_1_triple_room_value").innerHTML= '<i class="fa fa-inr" aria-hidden="true"></i>'+ three_rider_3_bike_1_triple_room * three_rider_3_bike_1_triple_room_price; 
        totalValueSolo ();
    });
    
    function totalValueSolo () {
        const solo_riding_on_double_room = Number( $('#solo_riding_on_double_room').val());
        const solo_riding_on_double_room_price = Number($('#solo_riding_on_double_room_price').val());
        
        
        const double_riding_on_double_room = Number( $('#double_riding_on_double_room').val());
        const double_riding_on_double_room_price = Number($('#double_riding_on_double_room_price').val());
        
        
        const solo_riding_on_single_room = Number( $('#solo_riding_on_single_room').val());
        const solo_riding_on_single_room_price = Number($('#solo_riding_on_single_room_price').val());
        
        const double_riding_on_single_room = Number( $('#double_riding_on_single_room').val());
        const double_riding_on_single_room_price = Number($('#double_riding_on_single_room_price').val());
        
        const three_rider_2_bike_1_triple_room = Number( $('#three_rider_2_bike_1_triple_room').val());
        const three_rider_2_bike_1_triple_room_price = Number($('#three_rider_2_bike_1_triple_room_price').val());
        
        const three_rider_3_bike_1_triple_room = Number( $('#three_rider_3_bike_1_triple_room').val());
        const three_rider_3_bike_1_triple_room_price = Number($('#three_rider_3_bike_1_triple_room_price').val());
        
        let total_final_amount = 0;
        let html = ``;
        let totalPax = 0;
        
        if (solo_riding_on_double_room && solo_riding_on_double_room > 0) {
            html += `<p><span>Solo riding on Double Room:</span> <strong>${solo_riding_on_double_room} pax</strong></p>`;
            totalPax += solo_riding_on_double_room;
            total_final_amount += solo_riding_on_double_room * solo_riding_on_double_room_price;
        }
        if (double_riding_on_double_room && double_riding_on_double_room > 0) {
            html += `<p><span>Double riding on Double Room:</span> <strong>${double_riding_on_double_room} pax</strong></p>`;
            totalPax += double_riding_on_double_room;
            total_final_amount += double_riding_on_double_room * double_riding_on_double_room_price;
        }
        if (solo_riding_on_single_room && solo_riding_on_single_room > 0) {
            html += `<p><span>Solo riding Single Room:</span> <strong>${solo_riding_on_single_room} pax</strong></p>`;
            totalPax += solo_riding_on_single_room;
            total_final_amount += solo_riding_on_single_room * solo_riding_on_single_room_price;
        }
        if (double_riding_on_single_room && double_riding_on_single_room > 0) {
            html += `<p><span>Double riding Single Room:</span> <strong>${double_riding_on_single_room} pax</strong></p>`;
            totalPax += double_riding_on_single_room;
            total_final_amount += double_riding_on_single_room * double_riding_on_single_room_price;
        }
        if (three_rider_2_bike_1_triple_room && three_rider_2_bike_1_triple_room > 0) {
            html += `<p><span>3 Rider 2. bike 1 Triple room:</span> <strong>${three_rider_2_bike_1_triple_room} pax</strong></p>`;
            totalPax += three_rider_2_bike_1_triple_room;
            total_final_amount += three_rider_2_bike_1_triple_room * three_rider_2_bike_1_triple_room_price;
        }
        if (three_rider_3_bike_1_triple_room && three_rider_3_bike_1_triple_room > 0) {
            html += `<p><span>3 Rider 3 bike 1 Triple room:</span> <strong>${three_rider_3_bike_1_triple_room} pax</strong></p>`;
            totalPax += three_rider_3_bike_1_triple_room;
            total_final_amount += three_rider_3_bike_1_triple_room * three_rider_3_bike_1_triple_room_price;
        }
        
        
        
        html += `<p class="add-top-border"><span><strong>Total Pax:</strong></span> <strong>${totalPax} pax</strong></p>`;
        document.getElementById("booking_summary").innerHTML = html ;
        document.getElementById("total_final_amount").innerHTML = `<i class="fa fa-inr" aria-hidden="true"></i>${total_final_amount}` ;
        if (total_final_amount) {
            let payableAmount = Math. trunc(total_final_amount/10);  
            document.getElementById("payable_amount").innerHTML =`<i class="fa fa-inr" aria-hidden="true"></i> ${payableAmount}`;
        }
        
    }
    
</script>

<script type="text/javascript">
// var $ = jQuery;
    $(document).ready(function(){
        if($(window).width() <= 1023){
            $('.toggle-btn').on('click', function(){
                $('#nav').addClass('active-mode');
                $('#transBg').fadeIn();
            });
            $('.menu-item-has-children').click(function(e){
                $('.menu-item-has-children > ul').slideUp();
                if(!($(this).children('ul').is(":visible"))){
                    $(this).children('ul').slideDown();
                }
                //$(this).children('ul').slideToggle();
                e.stopPropagation();
            });
            $('.multi-col li h3').click(function(e){
                $('.multi-col li h3').next().slideUp();
                if(!($(this).next('ul').is(":visible"))){
                    $(this).next('ul').slideDown();
                }
                e.stopPropagation();
            });
        }
    });
</script>

<script>
var enabledDates = []
    
    $(document).ready(function() {
    // Initialize datepicker here but with a placeholder function
    $("#newOrderDates").datepicker({
        todayHighlight: true,
        dateFormat: 'yy-mm-dd',
        multidate: true,
        startDate: new Date(),
        beforeShowDay: function(date) {
            return [false]; // Initially disable all days
        }
    });

    function callAjax() {
        const destinationfrom = $('#destinationfrom').val();
        const destinationto = $('#destinationto').val();
        const category = $('input[name="category"]:checked').val();

        $.ajax({
            url: 'https://agent.go2ladakh.in/fixed-package-html/ajaxApi.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ 
                destinationto: destinationto, 
                destinationfrom: destinationfrom ,
                category: category
            }), 
            success: function(response) {

                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if (Array.isArray(response)) {
                    const enabledDates = (response.map(tour => JSON.parse(tour.tour_start_date)))[0];
                    console.log('Enabled Dates:', enabledDates);

                    // Update the datepicker to enable only the specified dates
                    $("#newOrderDates").datepicker("option", "beforeShowDay", function(date) {
                        var sdate = moment(date).format('YYYY-MM-DD');
                        if ($.inArray(sdate, enabledDates) !== -1) {
                            return [true]; // Enable this date
                        }
                        return [false]; // Disable this date
                    });

                    // Refresh the datepicker UI to apply changes
                    $("#newOrderDates").datepicker("refresh");
                } else {
                    console.error('Expected an array but got:', response);
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    }

    $('#destinationfrom').on('change', function(event) {
        $('#newOrderDates').val('');
        callAjax();
    });
    $('#destinationto').on('change', function(event) {
         $('#newOrderDates').val('');
        callAjax();
    });
    $('input[name="category"]').on('click', function(event) {
        $('#newOrderDates').val(''); // Clear the date input field
        callAjax(); // Call your Ajax function
    });
    callAjax();
//   $('#submitSearch').on('click', function(event) {
//             console.log('Button clicked'); // Check if the button click is recognized
//             callAjax(); // Call your AJAX function
//     });
});

    
    
    jQuery(document).ready(function(){

    if (jQuery(".accordion-block").length) {
        //Active first child 
        if(jQuery('.accordion-list').hasClass('first-active')){
            jQuery('.first-active').children('.accordion-main').addClass('active');
            jQuery('.first-active').children('.accordion-main').children('span').text('—');

        }
        jQuery('.accordion-main').click(function() {
            if (jQuery(this).hasClass('active')) {
                jQuery(this).removeClass('active');
                jQuery(this).next('.accordion-expand').slideUp();
                jQuery(this).children('span').text('+');
            } else {
                jQuery(this).addClass('active');
                jQuery(this).next('.accordion-expand').slideDown();
                jQuery(this).children('span').text('—');
            }
        });
    }

});



    // Toggle Detail View
    $(document).ready(function() {
        $('.book-now-button').on('click', function(e) {
            e.preventDefault();
            $(this).closest('.single-list').find('.detail-view').slideToggle();
        });
    });
</script>


</body>
</html>