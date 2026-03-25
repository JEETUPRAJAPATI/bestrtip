<?php

session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$db = getDbInstance();
$db->orderBy("name", "asc");
$destinations = $db->get("flight_destination", null, 'id, name');
$traveling_from = $db->get("flight_destination", null, 'id, name');

$id = isset($_GET['id']) && !empty($_GET['id']) ? decryptId($_GET['id']) : "";
$edit = !empty($id);

if ($edit) {
    $db->where('f.id', $id);
    $db->join("flight_details d", "f.id = d.flight_id", "LEFT");
    $results = $db->get("flight_lists f", null, "f.*, d.flight_id, d.flight_number, d.departure_datetime, d.arrival_datetime, d.price");

    $data = [];

    // Initialize the main flight info
    if (!empty($results)) {
        $data = [
            "id" => $results[0]['id'],
            "from" => $results[0]['from'],
            "destination" => $results[0]['destination'],
            "cabin_baggage" => $results[0]['cabin_baggage'],
            "flight_logo" => $results[0]['flight_logo'],
            "person" => $results[0]['person'],
            "status" => $results[0]['status'],
            "created_at" => $results[0]['created_at'],
            "updated_at" => $results[0]['updated_at'],
            "flight_listing" => [] // Initialize flight details array
        ];

        // Loop through results and add to flight_listing
        foreach ($results as $row) {
            $data['flight_listing'][] = [
                "flight_id" => $row['flight_id'],
                "flight_number" => $row['flight_number'],
                "departure_datetime" => $row['departure_datetime'],
                "arrival_datetime" => $row['arrival_datetime'],
                "price" => $row['price']
            ];
        }
    }

    // echo "<pre>";
    // print_r($data);
    // die();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // echo "<pre>";
    // print_r($_POST);
    // print_r($_FILES);
    // die();
    $from = $_POST['from'];
    $destination = $_POST['destination'];
    $person = $_POST['person'];
    $cabin_baggage = $_POST['cabin_baggage'];
    $flight_numbers = $_POST['flight'];
    $departure_datetimes = $_POST['departure_datetime'];
    $arrival_datetimes = $_POST['arrival_datetime'];
    $prices = $_POST['price'];


    $data = [
        "from" => $from,
        "destination" => $destination,
        "person" => $person,
        "cabin_baggage" => $cabin_baggage,
    ];
    $db = getDbInstance();
    $target_dir = "uploads/airline/logo/";

    // Ensure directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Check if flight logo is uploaded
    if (isset($_FILES['flight_logo']) && !empty($_FILES['flight_logo']['name'])) {
        $image_files = $_FILES['flight_logo'];

        // If editing, remove old image before uploading new one
        if ($edit && $id) {
            $db->where('id', $id);
            $existing_flight = $db->getOne('flight_lists', ['flight_logo']);

            if ($existing_flight && !empty($existing_flight['flight_logo'])) {
                $old_image_path = $existing_flight['flight_logo'];

                if (file_exists($old_image_path)) {
                    unlink($old_image_path); // Delete old image
                }
            }
        }

        // Upload new image(s)
        $uploaded_images = [];
        // Check if a file is uploaded
        if (isset($_FILES['flight_logo']) && !empty($_FILES['flight_logo']['name'])) {
            $image_file = $_FILES['flight_logo'];
            $tmp_name = $image_file['tmp_name'];
            $size = $image_file['size'];
            $error = $image_file['error'];

            if ($error === UPLOAD_ERR_OK && $size <= 10000000) { // 10MB limit
                $new_file_name = time() . "_" . basename($image_file['name']);
                $target_file = $target_dir . $new_file_name;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $data['flight_logo'] = $target_file; // Store the uploaded image path
                }
            }
        }
    }


    if ($edit) {
        $db->where('id', $id);
        $db->update('flight_lists', $data);
        $db->where('flight_id', $id);
        $db->delete('flight_details');
        $_SESSION['success'] = "Record updated successfully";
    } else {
        $id = $db->insert('flight_lists', $data);
        $_SESSION['success'] = "New record created successfully";
    }
    $flight_details = [];

    foreach ($flight_numbers as $key => $flight_number) {
        $flight_details[] = [
            "flight_id" => $id,
            "flight_number" => $flight_number,
            "departure_datetime" => $departure_datetimes[$key],
            "arrival_datetime" => $arrival_datetimes[$key],
            "price" => $prices[$key]
        ];
    }
    // echo "<pre>";
    // print_r($flight_details);
    // die();
    // Bulk insert flight details
    if (!empty($flight_details)) {
        $db->insertMulti('flight_details', $flight_details);
    }

    header('Location: view-flight-booking.php');
    exit();
}

include BASE_PATH . '/includes/header.php';
?>

<style>
    .remove-more-btn {
        position: absolute;
        margin-top: -25px;
        right: 25px;
        text-transform: uppercase;
        font-size: 12px;
        cursor: pointer;
        color: red;
        text-decoration: underline;
    }

    .add-more-row {
        .col-md {
            text-align: right;
        }

        span {
            color: #566a7f;
            cursor: pointer;
        }
    }
</style>
<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4">Airline Booking Form</h4>
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="row mb-3">
                                    <div class="col-md">
                                        <label class="form-label">From</label>
                                        <select class="form-select" name="from" required>
                                            <option disabled selected>Select Departure</option>
                                            <?php foreach ($traveling_from as $location): ?>
                                                <option value="<?= $location['id']; ?>" <?= ($edit && $data['from'] == $location['id']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($location['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md">
                                        <label class="form-label">Destination</label>
                                        <select class="form-select" name="destination" required>
                                            <option disabled selected>Select Destination</option>
                                            <?php foreach ($destinations as $destination): ?>
                                                <option value="<?= $destination['id']; ?>" <?= ($edit && $data['destination'] == $destination['id']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($destination['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md">
                                        <label class="form-label">Flight Logo</label>
                                        <input type="file" name="flight_logo" class="form-control" />

                                        <div class="mt-3">
                                            <div style="display: inline-block; margin-right: 10px; text-align: center;">

                                                <?php if (!empty($data['flight_logo'])) : ?>
                                                    <img src="<?php echo $data['flight_logo']; ?>" alt="Flight Logo" height="100px" width="100px" style="display: block;">
                                                <?php else : ?>
                                                    N/A
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md">
                                        <label class="form-label">Cabin Baggage</label>
                                        <div class="input-group">
                                            <input class="form-control" type="number" name="cabin_baggage" value="<?= $edit ? htmlspecialchars($data['cabin_baggage']) : ''; ?>" placeholder="Enter Weight" required>
                                            <span class="input-group-text">kg</span>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <label class="form-label">Seat Available</label>
                                        <input class="form-control" type="number" name="person"
                                            value="<?= $edit && isset($data['person']) ? $data['person'] : ''; ?>"
                                            placeholder="Enter Total Person" required>

                                    </div>
                                </div>



                                <div id="repeater-fields">
                                    <?php
                                    $flights = $edit ? $data['flight_listing'] : [[]];

                                    foreach ($flights as $index => $flight) { ?>
                                        <div class="row mb-3 repeat-box align-items-end">

                                            <div class="col-md">
                                                <label class="form-label">Flight Number</label>
                                                <input class="form-control" type="text" name="flight[]"
                                                    value="<?= $flight['flight_number'] ?? ''; ?>"
                                                    placeholder="Enter Flight Number" required>
                                            </div>
                                            <div class="col-md">
                                                <label class="form-label">Departure Date & Time</label>
                                                <input class="form-control" name="departure_datetime[]"
                                                    type="datetime-local"
                                                    value="<?= isset($flight['departure_datetime']) ? date('Y-m-d\TH:i', strtotime($flight['departure_datetime'])) : ''; ?>"
                                                    required>
                                            </div>


                                            <div class="col-md">
                                                <label class="form-label">Arrival Date & Time</label>
                                                <?php if ($index > 0) { ?>
                                                    <div class="col-md remove-more-btn"
                                                        style="margin-top: -30px; position: absolute; left: 1296px; cursor: pointer;"
                                                        onclick="removeRow(this)">
                                                        Remove
                                                    </div>
                                                <?php } ?>
                                                <input class="form-control" name="arrival_datetime[]"
                                                    type="datetime-local"
                                                    value="<?= isset($flight['arrival_datetime']) ? date('Y-m-d\TH:i', strtotime($flight['arrival_datetime'])) : ''; ?>"
                                                    required>
                                            </div>

                                            <div class="col-md">
                                                <label class="form-label">Price</label>
                                                <input class="form-control" name="price[]"
                                                    type="text"
                                                    value="<?= $flight['price'] ?? ''; ?>"
                                                    placeholder="Enter Price" required>
                                            </div>


                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="row mb-3 add-more-row" id="add-more-date">
                                    <div class="col-md">
                                        <span class="add-more-btn">+ Add More</span>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('add-more-date').addEventListener('click', function() {
        document.querySelector('#repeater-fields').insertAdjacentHTML(
            'beforeend',
            `<div class="row mb-3 repeat-box">
             <div class="col-md">
                    <label class="form-label">Flight Number</label>
                    <input class="form-control" type="text" name="flight[]" placeholder="Enter Flight Name" required>
                </div>
                <div class="col-md">
                    <label class="form-label">Date</label>
                   <input class="form-control" name="departure_datetime[]" type="datetime-local" required>
                </div>
                <div class="col-md">
                    <label class="form-label">Time</label>
                       <input class="form-control" name="arrival_datetime[]" type="datetime-local"required>
                </div>
                <div class="col-md">
                    <label class="form-label">Price</label>
                    <input class="form-control" name="price[]" type="text" required>
                </div>
                <div class="col-md remove-more-btn"
     style="margin-top: -4px; position: absolute; left: 1296px; cursor: pointer;"
     onclick="removeRow(this)">
     Remove
</div>

            </div>`
        );
    });

    function removeRow(input) {
        input.parentNode.remove();
    }
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>