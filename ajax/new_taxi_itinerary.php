 <?php
session_start();
require_once '../config/config.php';
// require_once BASE_PATH . '/includes/auth_validate.php';

$db = getDbInstance();
$db->where('taxi_id', $_POST['package_id']);
$db->where('itineary', ['TWIN Fixed', 'CWB Fixed', 'CNB Fixed', 'TRIPLE Fixed', 'SINGLE Fixed', 'QUAD SHARING Fixed'], "NOT IN");
$results = $db->get("taxi_details");
$tour_date =  date('d-m-Y', strtotime($_POST['tour_date']));
foreach ($results as $key => $result):

?>

    <tr>
        <td style="min-width: 82px">Day <?= $key + 1 ?>, <?= date('l', strtotime($tour_date)) ?></td>
        <td style="min-width: 130px"><?= $tour_date ?></td>
        <td><?= $result['itineary'] ?></td>
    </tr>
    <div class="itinerary-day col-12 completed-day bg-gray-100 text-muted">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <h3 class="section-title me-3 fs-5">Select Hotel</h3>
                        <div class="d-flex gap-2">
                            <button class="btn rounded-3 rating-btn text-theme hover:bg-purple-900 hover:text-white"
                                onclick="selectRating(this)" disabled>
                                <i class="fas fa-star"></i> 2+
                            </button>
                            <button class="btn rounded-3 rating-btn text-theme hover:bg-purple-900 hover:text-white"
                                onclick="selectRating(this)" disabled>
                                <i class="fas fa-star"></i> 3+
                            </button>
                            <button class="btn rounded-3 rating-btn text-theme hover:bg-purple-900 hover:text-white"
                                onclick="selectRating(this)" disabled>
                                <i class="fas fa-star"></i> 4+
                            </button>
                            <button class="btn rounded-3 rating-btn text-theme hover:bg-purple-900 hover:text-white"
                                onclick="selectRating(this)" disabled>
                                <i class="fas fa-star"></i> 5
                            </button>
                        </div>
                    </div>
                    <!-- Alpine.js for Carousel Functionality -->

<div class="card rounded-lg overflow-hidden shadow-md  p-0">
    <div class="p-3">
        <div class="flex flex-col md:flex-row">
            <!-- Carousel Section -->
            <div class="hotel-card-img relative mb-1 sm:mb-0">
                <div x-data="{ 
                    activeSlide: 0, 
                   slides: [
    'https://plus.unsplash.com/premium_photo-1661929519129-7a76946c1d38?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OXx8aG90ZWx8ZW58MHx8MHx8fDA%3D',
    'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTh8fGhvdGVsfGVufDB8fDB8fHww',
    'https://images.unsplash.com/photo-1660557989725-f511e9fa6267?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8aG90ZWwlMjBsb2JieXxlbnwwfHwwfHx8MA%3D%3D'
]
                }" class="relative w-full h-48 sm:w-48 sm:h-48">

                    <div class="overflow-hidden relative w-full h-full">
                        <template x-for="(slide, index) in slides" :key="index">
                            <img :src="slide"
                                class="absolute top-0 left-0 w-full h-full object-cover rounded-md transition-opacity duration-500 ease-in-out"
                                :class="activeSlide === index ? 'opacity-100' : 'opacity-0'" x-cloak>
                        </template>
                    </div>

                    <!-- Indicators -->
                    <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-2">
                        <template x-for="(slide, index) in slides" :key="index">
                            <button @click="activeSlide = index"
                                class="w-3 h-3 rounded-full bg-gray-400 hover:bg-gray-600"
                                :class="{'bg-gray-900': activeSlide === index}"></button>
                        </template>
                    </div>

                    <!-- Navigation Arrows -->
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

            <div class="w-full md:w-1/2 md:pl-4 md:mt-0">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-800">
                            The Grand Paris Hotel
                        </h3>
                        <p class="text-theme text-base  fs-5">
                            $150
                        </p>
                        <span class="text-yellow-500 ">★★★</span>
                    </div>
                </div>

                <p class="text-gray-600 text-sm flex items-center mb-1">
                    <i class="fas fa-map-marker-alt mr-2 text-gray-500"></i>
                    Paris, France
                </p>

                <!-- Price -->

                <div class="flex flex-wrap text-sm text-gray-600 space-y-1 md:space-y-0">
                    <div class="pt-1 flex items-center w-full md:w-auto mr-4">
                        <i class="fas fa-utensils mr-2 text-gray-500"></i>
                        Breakfast Included
                    </div>
                    <div class="pt-1 flex items-center w-full md:w-auto mr-4">
                        <i class="fas fa-clock mr-2 text-gray-500"></i>
                        3 days 2 nights
                    </div>
                    <div class="pt-1 flex items-center w-full md:w-auto mr-4">
                        <i class="fas fa-bed mr-2 text-gray-500"></i>
                        2 beds
                    </div>
                    <div class="pt-1 flex items-center w-full md:w-auto">
                        <i class="fas fa-door-open mr-2 text-gray-500"></i>
                        3 rooms
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="flex justify-end mb-1 me-4">
        <button
            class=" text-theme py-1 me-3 rounded-md hover:text-purple-900 transition duration-300 ease-in-out hover:translate-x-2">
            <i class=" fa-solid fa-pencil"></i>
            Change Hotel
        </button>
        <button class="btn-theme text-white px-4 py-1 rounded-md hover:bg-purple-700">
            Select
        </button>
    </div>
    <!-- Hotel Status -->
    <div class="flex bg-green-100">
        <div class="w-1/2 py-2 px-4 text-gray-600">Hotel Status</div>
        <div class="w-1/2 py-2 px-4 text-right text-purple-800">In Enquiry</div>
    </div>
</div>
                    <!-- Hotel card would be included here -->
                </div>
                <div class="col-md-6 mt-4 p-5">
                    <div class="">
                        <h2 class="fs-2 fw-bold">Day <?= $key + 1 ?> <?= date('l', strtotime($tour_date)) ?></h2>
                        <h3 class="mt-2 fs-3 font-semibold"><?= $tour_date ?></h3>
                        <span class="badge bg-secondary">Completed</span>
                    </div>
                    <h3 class="fs-4 mb-2 font-medium">
                        Tower of London Tour
                    </h3>
                    <p class="text-base font-weight-semibold">
                       <?= $result['itineary'] ?>
                    </p>
                </div>
            </div>
        </div>
<?php
    $tour_date = addOneDay($tour_date);
endforeach;

$db = getDbInstance();
$db->where('id', $_POST['package_id']);
$result = $db->getOne("packages", 'permit, guide');
?>
98230948klasd908809230894
<h2>Extra Services</h2>
<div class="fixed-service">
    <div class="colm">
        <label class="container"> Permit
            <input class="custom-check" checked type="checkbox" onClick="return calculateTotal();" data-permit="<?= $result['permit'] ?>" name="permit" id="permit">
            <span class="checkmark"></span>
        </label>
    </div>
    <div class="colm">
        <label class="container"> Guide
            <input class="custom-check" checked type="checkbox" onClick="return calculateTotal();" data-guide="<?= $result['guide'] ?>" name="guide" id="guide">
            <span class="checkmark"></span>
        </label>
    </div>
</div>
 
 