<?php
session_start();
require_once '../config/config.php';

$db = getDbInstance();
$db->where('package_id', $_POST['package_id']);
$db->where('itineary', ['TWIN Fixed', 'CWB Fixed', 'CNB Fixed', 'TRIPLE Fixed', 'SINGLE Fixed', 'QUAD SHARING Fixed'], "NOT IN");
$results = $db->get("package_details");

$tour_date = date('d-m-Y', strtotime($_POST['tour_date']));
$location = "";
$checkIn = $tour_date;
$day = 1; // Starting day number

foreach ($results as $key => $result) :
    $night = 0;

    // Switch block for determining the amount based on category
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

    // Check if the location changes
    if ($result['location'] != $location) {
        $location = $result['location'];
        $checkOut = addOneDay($checkIn);
        $i = $key + 1;
        $night++;

        // Loop to find continuous locations and calculate nights
        while (isset($results[$i]['location']) && $result['location'] == $results[$i]['location']) {
            $checkOut = addOneDay($checkOut);
            $i++;
            $night++;
        }

        // Fetch hotel details
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
                <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="right">
                        <?php
                        for ($i = 1; $i <= $night; $i++) {
                            echo 'Day ' . $day++;
                            if ($i < $night) {
                                echo ' | ';
                            }
                        }
                        ?>
                    </div>
                    <div class="left"><?= $night ?> Night</div>
                </div>

                <div class="detail">
                    <div class="hotel-name">
                        <div class="title">Hotel Name</div>
                        <div class="name"><?= $hotel['hotel_name'] ?></div>
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
                            <?php
                            for ($i = 0; $i < $night; $i++) {
                                $mealPlan = $results[$key + $i]['meal_plan'] ?? 'N/A'; // Fetching meal plan from package details
                            ?>
                                <div class="meal-day">
                                    <div class="desc"><?= $mealPlan ?></div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="status">
                    <div class="left">Hotel Status</div>
                    <div class="right">Pending...</div>
                </div>
            </div>
<?php
        endif;
        // Update check-in date for next location
        $checkIn = $checkOut;
    }
endforeach;
?>