<?php
session_start();
require_once '../config/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = getDbInstance();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    // echo "<pre>";
    // print_r($_POST);
    // die();
    $driver_name = $_POST['name'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $email = $_POST['email'] ?? '';
    $age = $_POST['age'] ?? '';
    $experience = $_POST['experience'] ?? '';
    $vehicle_number = $_POST['vehicle_number'] ?? '';
    $vehicle_type = $_POST['vehicle_type'] ?? '';
    $facilities = isset($_POST['facilities']) ? implode(',', $_POST['facilities']) : '';

    // Insert vehicle data into the database
    $data_to_store = [
        'driver_name' => $driver_name,
        'mobile' => $mobile,
        'email' => $email,
        'age' => $age,
        'experience' => $experience,
        'vehicle_number' => $vehicle_number,
        'vehicle_type' => $vehicle_type,
        'facilities' => $facilities,
    ];

    $msg = "added";
    $vehicle_id = $db->insert('vehicles', $data_to_store);

    if ($vehicle_id) {
        // Handle image uploads
        $target_dir = "../uploads/vehicles/";

        // Ensure the directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $exteriorImages = [];
        $interiorImages = [];

        // Process exterior images
        if (!empty($_FILES['exteriorImages']['name'][0])) {
            foreach ($_FILES['exteriorImages']['name'] as $key => $name) {
                $tmp_name = $_FILES['exteriorImages']['tmp_name'][$key];
                $size = $_FILES['exteriorImages']['size'][$key];
                $error = $_FILES['exteriorImages']['error'][$key];

                if ($error === UPLOAD_ERR_OK && $size <= 5000000) { // Limit: 5MB per image
                    $filename = time() . "_" . basename($name);
                    $target_file = $target_dir . $filename;
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $exteriorImages[] = $target_file;
                    }
                }
            }
        }

        // Process interior images
        if (!empty($_FILES['interiorImages']['name'][0])) {
            foreach ($_FILES['interiorImages']['name'] as $key => $name) {
                $tmp_name = $_FILES['interiorImages']['tmp_name'][$key];
                $size = $_FILES['interiorImages']['size'][$key];
                $error = $_FILES['interiorImages']['error'][$key];

                if ($error === UPLOAD_ERR_OK && $size <= 5000000) { // Limit: 5MB per image
                    $filename = time() . "_" . basename($name);
                    $target_file = $target_dir . $filename;
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $interiorImages[] = $target_file;
                    }
                }
            }
        }

        // Convert image paths to comma-separated string
        $exteriorImagesStr = !empty($exteriorImages) ? implode(',', $exteriorImages) : '';
        $interiorImagesStr = !empty($interiorImages) ? implode(',', $interiorImages) : '';

        // Update the vehicle record with image paths
        $updateData = [
            'exterior_images' => $exteriorImagesStr,
            'interior_images' => $interiorImagesStr
        ];
        $db->where('id', $vehicle_id);
        $db->update('vehicles', $updateData);

        $_SESSION['success'] = "Vehicle registered successfully!";
        header('Location: view-vehicles.php');
        exit();
    } else {
        echo 'Insert failed: ' . $db->getLastError();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Signup</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 900px;
            margin-top: 50px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .image-section img {
            width: 100%;
            height: 100%;
            border-radius: 10px 0 0 10px;
        }

        .form-section {
            padding: 30px;
        }

        .btn-register {
            background-color: #fdbf00;
            border: none;
            padding: 10px;
        }

        .preview-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 image-section">
                <img src="https://t4.ftcdn.net/jpg/10/01/90/67/360_F_1001906743_wIUb9PnD3SvcE7b9TMjQzcMUGCjumzrC.jpg" alt="Driver Image">
            </div>
            <div class="col-md-6 form-section">
                <h4 class="text-center">Signup to Drive</h4>
                <form method="post" action="#" enctype="multipart/form-data">
                    <input type="text" class="form-control mb-2" name="name" placeholder="Driver Name">
                    <input type="text" class="form-control mb-2" name="mobile" placeholder="Mobile">
                    <input type="email" class="form-control mb-2" name="email" placeholder="Email ID">
                    <input type="number" class="form-control mb-2" name="age" placeholder="Age">
                    <input type="text" class="form-control mb-2" name="experience" placeholder="Experience">
                    <input type="text" class="form-control mb-2" name="vehicle_number" placeholder="Vehicle Number">
                    <select class="form-control mb-2" name="vehicle_type">
                        <option value="">Select Car Type</option>
                        <option value="sedan">Sedan</option>
                        <option value="suv">SUV</option>
                        <option value="hatchback">Hatchback</option>
                        <option value="luxury">Luxury</option>
                        <option value="van">Van</option>
                    </select>
                    <div class="mb-3">
                        <select id="carFacilities" class="form-select" name="facilities[]" multiple>
                            <option value="tick">Ticks</option>
                            <option value="oxygen">Oxygen</option>
                            <option value="water">Water Bottle</option>
                            <option value="first_aid">First Aid Kit</option>
                            <option value="snacks">Snacks</option>
                        </select>
                    </div>
                    <label class="mt-2">Exterior Picture (4)</label>
                    <input type="file" class="form-control mb-2" multiple id="exteriorImages" name="exteriorImages[]" accept="image/*">
                    <div id="exteriorPreview" class="d-flex"></div>
                    <label class="mt-2">Interior Picture (5)</label>
                    <input type="file" class="form-control mb-2" multiple id="interiorImages" name="interiorImages[]" accept="image/*">
                    <div id="interiorPreview" class="d-flex"></div>
                    <button type="submit" class="btn btn-register w-100 mt-3">REGISTER</button>
                    <a href="./driver_login.php">Login</a>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#carFacilities").select2({
                placeholder: "Choose Car Facilities...",
                allowClear: true
            });
        });

        function previewImages(input, previewDiv) {
            previewDiv.innerHTML = "";
            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement("img");
                        img.src = e.target.result;
                        img.classList.add("preview-img");
                        previewDiv.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }
        document.getElementById("exteriorImages").addEventListener("change", function() {
            previewImages(this, document.getElementById("exteriorPreview"));
        });
        document.getElementById("interiorImages").addEventListener("change", function() {
            previewImages(this, document.getElementById("interiorPreview"));
        });
    </script>
</body>

</html>