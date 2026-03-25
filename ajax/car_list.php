<?php
session_start();
require_once '../config/config.php';
// require_once BASE_PATH . '/includes/auth_validate.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$db = getDbInstance();
$db->where('taxi_id', $_POST['taxi_id']);
$db->where('itineary', ['TWIN Fixed', 'CWB Fixed', 'CNB Fixed', 'TRIPLE Fixed', 'SINGLE Fixed', 'QUAD SHARING Fixed'], "NOT IN");
$results = $db->get("taxi_details");

$db->where('status', 'active');
$carlisting = $db->get("carlist");


$finalArray = [];

// Loop through each car in carlisting
foreach ($carlisting as $car) {
    $carNameLower = strtolower($car['name']); // Convert car name to lowercase for matching
    $totalPrice = 0;

    // Loop through results to calculate total price
    foreach ($results as $result) {
        if (isset($result[$carNameLower])) {
            $totalPrice += $result[$carNameLower];
        }
    }

    // Only add the car if it has a matching price in results
    if ($totalPrice > 0) {
        $finalArray[] = [
            'id' => $car['id'],
            'name' => $car['name'],
            'image' => $car['image'],
            'passenger' => $car['passenger'],
            'bag' => $car['bag'],
            'description' => $car['description'],
            'price' => $totalPrice
        ];
    }
}

// echo "<pre>";
// print_r($finalArray);
// die();
?>

<style>
    .car-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 20px;
        background: white;
        max-width: 430px;
        transition: transform 0.2s ease-in-out;

    }


    .badge-custom {
        background: #E0EAFD;
        color: #0056b3;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 14px;
    }

    .car-title {
        font-weight: bold;
        font-size: 18px;
        margin-left: 10px;
        color: #333;
    }

    .car-image {
        width: 100px;
        height: auto;
        border-radius: 6px;
        margin-right: 16px;
    }

    .features {
        display: flex;
        align-items: center;
        gap: 14px;
        font-size: 15px;
        font-weight: 500;
        color: #555;
    }

    .features i {
        font-size: 18px;
        color: #555;
    }

    .price {
        font-size: 1.4rem;
        font-weight: bold;
        color: #0056b3;
    }

    .view-deal-btn {
        background-color: #0056b3;
        color: white;
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        transition: background 0.2s ease-in-out;
    }

    .view-deal-btn:hover {
        background-color: #0056b3;
    }

    .price-text {
        font-size: 14px;
        color: #666;
    }

    hr {
        margin: 12px 0;
        border-top: 1px solid #ddd;
    }
</style>

<div class="container">
    <div class="row" style="row-gap: 23px;column-gap: 33px;">
        <?php


        foreach ($finalArray as $key => $car): ?>

            <div class="car-card p-3 border rounded">
                <div class="car-card">
                    <!-- Header -->
                    <div class="d-flex align-items-center">
                        <span class="badge badge-custom">Intermediate</span>
                        <h5 class="car-title"><?= $car['name'] ?></h5>
                    </div>

                    <!-- Car Image & Features -->
                    <div class="d-flex align-items-center mt-3">
                        <img src="<?= $car['image'] ?>" alt="Car Image" class="car-image">
                        <div class="features">
                            <span><i class="bi bi-people"></i><?= $car['passenger'] ?></span>
                            <span><i class="bi bi-suitcase-lg"></i> <?= $car['bag'] ?> </span>
                            <span><i class="bi bi-car-front"></i> 4</span>
                        </div>
                    </div>

                    <hr>

                    <!-- Pricing & Button -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="price-text">From</span>
                            <span class="price"><?= $car['price'] ?></span>
                            <span class="price-text">/day</span>
                        </div>
                        <button class="btn view-deal-btn" data-bs-toggle="modal" data-bs-target="#carModal<?= $car['id'] ?>">
                            View Details
                        </button>
                        <button class="btn view-deal-btn" onClick="return selectCar(
        <?= $car['id'] ?>,
        '<?= !empty($car['name']) ? addslashes($car['name']) : '' ?>',
        '<?= addslashes($car['passenger']) ?>',
        '<?= !empty($car['bag']) ? addslashes($car['bag']) : '' ?>',
         '<?= !empty($car['price']) ? addslashes($car['price']) : '' ?>'
    )">Add Car</button>
                        <!-- Bootstrap Modal -->
                        <div class="modal fade" id="carModal<?= $car['id'] ?>" tabindex="-1" aria-labelledby="carModalLabel<?= $car['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered"> <!-- Centered Modal -->
                                <div class="modal-content">
                                    <div class="modal-header w-100">
                                        <h5 class="modal-title" id="carModalLabel<?= $car['id'] ?>">Car Description</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center mt-2">
                                        <?= $car['description'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>