<?php
session_start();
require_once '../config/config.php';
// require_once BASE_PATH . '/includes/auth_validate.php';

$db = getDbInstance();
$db->where('package_id', $_POST['package_id']);
$db->where('itineary', ['TWIN Fixed', 'CWB Fixed', 'CNB Fixed', 'TRIPLE Fixed', 'SINGLE Fixed', 'QUAD SHARING Fixed'], "NOT IN");
$results = $db->get("package_details");
$tour_date =  date('d-m-Y', strtotime($_POST['tour_date']));
$location = "";
$checkIn = $tour_date;
$day = 0;
//print_r($results);
foreach ($results as $key => $result) :
    $night = 0;

    switch (strtolower($_POST['category'])) {
        case 'budget':
            $amount = $result['budget'];
            break;
        case 'standard':
            $amount = $result['standard'];
            break;
        case 'deluxe':
            $amount = $result['deluxe'];
            break;
        case 'super deluxe':
            $amount = $result['super_deluxe'];
            break;
        case 'premium':
            $amount = $result['premium'];
            break;
        case 'premium plus':
            $amount = $result['premium_plus'];
            break;
        case 'luxury':
            $amount = $result['luxury'];
            break;
        case 'luxury plus':
            $amount = $result['luxury_plus'];
            break;
        default:
            $amount = 0;
            break;
    }

    if ($result['location'] != $location) {
        $location = $result['location'];
        $checkOut = addOneDay($checkIn);
        $i = $key + 1;
        $night++;
        while (isset($results[$i]['location']) && $result['location'] == $results[$i]['location']) {
            $checkOut =  addOneDay($checkOut);
            $i++;
            $night++;
        }


        $db = getDbInstance();
        $db->where('location', $result['location']);
        $db->where('category', $_POST['category']);
        $hotel = $db->getOne("hotels");
        if ($hotel) :
?>
            <div class="list">
                <input type="hidden" name="hotel_night[]" value="<?= $night ?>" />
                <input type="hidden" name="hotel_amount[]" value="<?= $amount ?>" />
                <input type="hidden" name="hotel_name[]" value="<?= $hotel['hotel_name'] ?>" />
                <div class="header">
                    <div class="left">Day <?= $day = $day + $night ?> </div>
                    <div class="right"><?= $night ?> Night</div>
                </div>
                <div class="detail">
                    <div class="hotel-name">
                        <div class="title">Hotel Name</div>
                        <div class="name"><?= $hotel['hotel_name'] ?>
                            <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#z4sio6fmua)">
                                    <path d="M19 19.5H5v-14h7v-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zm-5-16v2h3.59l-9.83 9.83 1.41 1.41L19 6.91v3.59h2v-7h-7z" fill="#C6C6C6" />
                                </g>
                                <defs>
                                    <clipPath id="z4sio6fmua">
                                        <path fill="#fff" transform="translate(0 .5)" d="M0 0h24v24H0z" />
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                    </div>
                    <div class="other-detail">
                        <div class="colm">
                            <div class="title">Check in Date</div>
                            <div class="desc"><?= $checkIn ?></div>
                        </div>
                        <div class="colm">
                            <div class="title">Check out Date</div>
                            <div class="desc"><?= $checkOut ?></div>
                        </div>
                        <div class="colm">
                            <div class="title">Location</div>
                            <div class="desc"><?= $hotel['location'] ?></div>
                        </div>
                        <div class="colm">
                            <div class="title">Meal Plan</div>
                            <div class="desc">Breakfast</div>
                        </div>
                    </div>
                </div>
                <div class="status">
                    <div class="left">Hotel Status</div>
                    <div class="right">Pending...
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#qwk9os973a)">
                                <path d="M11 7h2v2h-2V7zm0 4h2v6h-2v-6zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" fill="#C9BF93" />
                            </g>
                            <defs>
                                <clipPath id="qwk9os973a">
                                    <path fill="#fff" d="M0 0h24v24H0z" />
                                </clipPath>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>

<?php
        endif;
        $checkIn = $checkOut;
    }
endforeach; ?>