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
        case '1*':
            $amount = $result['1*'];
            break;
        case '2**':
            $amount = $result['2**'];
            break;
        case '3***':
            $amount = $result['3***'];
            break;
        case '4****':
            $amount = $result['4****'];
            break;
        case '5*****':
            $amount = $result['5*****'];
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
        //print_r(hotel);
        if ($hotel) :
?>
 <div class="mb-3 col-12 col-lg-6">
        <input type="hidden" name="hotel_night[]" value="<?= $night ?>" />
                <input type="hidden" name="hotel_amount[]" value="<?= $amount ?>" />
                <input type="hidden" name="hotel_name[]" value="<?= $hotel['hotel_name'] ?>"/>
<div class="card rounded-lg overflow-hidden shadow-md  p-0">
    <div class="p-3">
        <div class="flex flex-col md:flex-row">
             <!--Carousel Section -->
            <div class="hotel-card-img relative mb-1 sm:mb-0">
                <div x-data='{ 
                    activeSlide: 0, 
                   slides: <?=$hotel['images'];?>
                }' class="relative w-full h-48 sm:w-48 sm:h-48">


                    <div class="overflow-hidden relative w-full h-full">
                        <template x-for="(slide, index) in slides" :key="index">
                            <!--<img :src="slide"-->
                            <!--    class="absolute top-0 left-0 w-full h-full object-cover rounded-md transition-opacity duration-500 ease-in-out"-->
                            <!--    :class="activeSlide === index ? 'opacity-100' : 'opacity-0'" x-cloak>-->
                            <img 
        :src="'uploads/hotel_images/' + slides[activeSlide]" 
        class="w-full h-full object-cover transition-all duration-500" 
        alt="Hotel Image" />
                        </template>
                    </div>

                     <!--Indicators -->
                    <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-2">
                        <template x-for="(slide, index) in slides" :key="index">
                            <button @click="activeSlide = index"
                                class="w-3 h-3 rounded-full bg-white border-0 hover:bg-gray-600"
                                :class="{'bg-gray-900': activeSlide === index}"></button>
                        </template>
                    </div>

                     <!--Navigation Arrows -->
                    <button @click="activeSlide = (activeSlide === 0 ? slides.length - 1 : activeSlide - 1)"
                        class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-30 text-white p-1 rounded-full text-xs">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button @click="activeSlide = (activeSlide === slides.length - 1 ? 0 : activeSlide + 1)"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-30 text-white p-1 rounded-full text-xs">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

             <!--Hotel Info -->
            <div class="w-full md:w-1/2 md:pl-4 md:mt-0">
                <div class="flex justify-between">
                    <h3 class="text-lg md:text-xl font-bold text-gray-800">The <?= $hotel['hotel_name'] ?>
                        <span class="text-yellow-500">
                            <?php
                            $rating = $hotel['category'];

                            if ($rating == '1*') {
                                echo "★☆☆☆☆";
                            } elseif ($rating == '2**') {
                                echo "★★☆☆☆";
                            } elseif ($rating == '3***') {
                                echo "★★★☆☆";
                            } elseif ($rating == '4****') {
                                echo "★★★★☆";
                            } elseif ($rating == '5*****') {
                                echo "★★★★★";
                            } else {
                                echo "No rating";
                            }
                            ?>
                        </span>
                    </h3>
                </div>
                <p class="text-gray-600 text-sm flex items-center mb-2">
                    <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>
                    <?= $hotel['location'] ?>
                </p>

                <div class="flex flex-wrap text-sm text-gray-600 space-y-1 md:space-y-0">
                    <div class="pt-3 flex items-center w-full md:w-auto mr-4">
                        <i class="fas fa-utensils mr-2 text-gray-500"></i>
                        <!--Breakfast Included-->
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
                    <div class="pt-3 flex items-center w-full md:w-auto mr-4">
                        <i class="fas fa-clock mr-2 text-gray-500"></i>
                        <?php
                        for ($i = 1; $i <= $night; $i++) {
                            echo $day++ . ' Day';
                            if ($i < $night) {
                                echo ' | ';
                            }
                        }
                        ?> <?= $night ?> Night
                    </div>
                    <div class="pt-3 flex items-center w-full md:w-auto mr-4">
                        <i class="fas fa-bed mr-2 text-gray-500"></i>
                        2 beds
                    </div>
                    <div class=" pt-3 flex items-center w-full md:w-auto">
                        <i class="fas fa-door-open mr-2 text-gray-500"></i>
                        3 rooms
                    </div>
                </div>

                 <!--Buttons -->
            </div>
        </div>
    </div>


    <div class="flex justify-end mb-1 me-4">
        <button
            class="border-0 bg-white text-theme py-1 me-3 rounded-md hover:text-purple-900 transition duration-300 ease-in-out hover:translate-x-2">
            <i class=" fa-solid fa-pencil"></i>
            Change Hotel
        </button>
        <button class="btn-theme text-white px-4 py-1 rounded-md hover:bg-purple-700">
            Select
        </button>
    </div>
     <!--Hotel Status -->
    <div class="flex bg-green-100">
        <div class="w-1/2 py-2 px-4 text-gray-600">Hotel Status</div>
        <div class="w-1/2 py-2 px-4 text-right text-purple-800">In Enquiry</div>
    </div>
</div>
                    </div>

<?php
        endif;
        // Update check-in date for next location
        $checkIn = $checkOut;
    }
endforeach;
?>