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

//foreach ($results as $key => $result) :
    $night = 0;

    // Check if the location changes
    // if ($result['location'] != $location) {
    //     $location = $result['location'];
    //     $checkOut = addOneDay($checkIn);
    //     $i = $key + 1;
    //     $night++;

       

        // Fetch hotel details
        $db = getDbInstance();
        $results = $db->get("carlist");
        foreach($results as $key=>$hotel):
?>
<div class="col-12 col-sm-6 col-md-4 col-lg-4">
    <!-- Header with "tion" text -->

    <!-- Car Card -->
    <div class="bg-white p-2 rounded-lg overflow-hidden mb-4 shadow">
        <!-- Car Image -->
        <div class="w-auto p-2">
            <img src="<?=$hotel['image']?>"
                alt="White sports car" class="w-full h-full object-cover">
        </div>

        <!-- Car Details -->
        <div class="p-2">
            <!-- Car Name -->
            <h2 class="text-2xl font-bold text-gray-800"><?=$hotel['name']?></h2>

            <!-- Car Type -->
            <p class="text-gray-500 mb-2">SUV</p>

            <!-- Car Features -->
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center">
                    <div class="rounded p-1">
                        <i class="fas fa-suitcase-rolling fs-5 text-theme"></i>
                    </div>
                    <span><?=$hotel['bag']?></span>
                </div>

                <div class="flex items-center">
                    <div class="bg-white text-theme">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <span><?=$hotel['passenger']?></span>
                </div>
            </div>

            <!-- Quantity Selector -->
            <div class="quantity-control">
                <button class="quantity-btn">−</button>
                <span class="quantity">2</span>
                <button class="quantity-btn">+</button>
            </div>
        </div>
    </div>
</div>

<?php
        endforeach;
        // Update check-in date for next location
       // $checkIn = $checkOut;
   // }
//endforeach;
?>