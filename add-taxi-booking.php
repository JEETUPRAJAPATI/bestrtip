<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_to_store = array_filter($_POST);

    $details = $data_to_store['detail'];
    $twinSave = $data_to_store['twin'];
    $cwbSave = $data_to_store['cwb'];
    $cnbSave = $data_to_store['cnb'];
    $tripleSave = $data_to_store['triple'];
    $singleSave = $data_to_store['single'];
    $quadSharingSave = $data_to_store['quad_sharing'];

    // die($custom_package);
    unset($data_to_store['detail']);
    unset($data_to_store['twin']);
    unset($data_to_store['cwb']);
    unset($data_to_store['cnb']);
    unset($data_to_store['triple']);
    unset($data_to_store['single']);
    unset($data_to_store['quad_sharing']);

    unset($data_to_store['service_name']);
    unset($data_to_store['service_type']);
    unset($data_to_store['service_amount']);

    $db = getDbInstance();

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $msg = "edited";

        $data_to_store['destination'] = !empty($_POST['destination']) ? $_POST['destination'] : null;
        $data_to_store['traveling_from'] = !empty($_POST['traveling_from']) ? $_POST['traveling_from'] : null;


        $description = filter_input(INPUT_POST, 'description');
        $data_to_store['description'] = isset($description) ? $description : 'NULL';

        $db->where('id', $_POST['id']);
        $package_id = $db->update('taxi', $data_to_store);

        // echo "<pre>";
        // print_r($_POST['id']);
        // echo "</pre>";
        // die();
        if ($package_id) {
            // Handle image updates for editing
            $target_dir = "uploads/taxi/overview/";

            // Check if the directory exists; if not, create it
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $image_files = $_FILES['images'];

            if (isset($image_files) && !empty($image_files['name'][0])) {
                foreach ($image_files['name'] as $key => $name) {
                    $tmp_name = $image_files['tmp_name'][$key];
                    $size = $image_files['size'][$key];
                    $error = $image_files['error'][$key];

                    if ($error === UPLOAD_ERR_OK && $size <= 10000000) {
                        $target_file = $target_dir . time() . "_" . basename($name);
                        if (move_uploaded_file($tmp_name, $target_file)) {
                            // Save new image path to overview_section_images
                            $imageData = [
                                'taxi_id' => $_POST['id'],
                                'image_path' => $target_file,
                            ];
                            $inserted = $db->insert('overview_section_images', $imageData);
                            if (!$inserted) {
                                die("Insert failed: " . $db->getLastError() . " | Query: " . $db->getLastQuery());
                            }
                        }
                    }
                }
            }
        } else {
            echo "<script>alert('Error updating overview section!');</script>";
        }

        // delete exiting package details for insert new
        $db = getDbInstance();
        $db->where('taxi_id', $_POST['id']);
        $db->delete('taxi_details');
        // Insert here
        $db = getDbInstance();

        $structuredServices = [];
        $existingServiceIds = [];
        $existingServices = $db->where('taxi_id', $_POST['id'])->get('services');
        // Store IDs of existing services to track updates

        foreach ($_POST['service_name'] as $index => $name) {
            $serviceId = $_POST['service_id'][$index] ?? null; // Capture service ID if exists

            $serviceData = [
                'taxi_id' => $_POST['id'],
                'name' => $name,
                'type' => $_POST['service_type'][$index] ?? null,
                'amount' => $_POST['service_amount'][$index] ?? null
            ];

            if ($serviceId) {
                // Update existing service
                $db->where('id', $serviceId)->update('services', $serviceData);
                $existingServiceIds[] = $serviceId;
            } else {
                // New service to insert
                $structuredServices[] = $serviceData;
            }
        }
        // Insert new services if any
        if (!empty($structuredServices)) {
            $db->insertMulti('services', $structuredServices);
        }

        // Delete services that are no longer in the form
        $existingIds = array_column($existingServices, 'id');
        $toDelete = array_diff($existingIds, $existingServiceIds);

        if (!empty($toDelete)) {
            $db->where('taxi_id', $package_id);
            $db->where('id', $toDelete, 'IN');
            $db->delete('services');
        }
        foreach ($details as $detail) {
            $detail['taxi_id'] = $_POST['id'];
            $pkg_detail_id = $db->insert('taxi_details', $detail);
        }
        $twinSave['taxi_id'] = $_POST['id'];
        $cwbSave['taxi_id'] = $_POST['id'];
        $cnbSave['taxi_id'] = $_POST['id'];
        $tripleSave['taxi_id'] = $_POST['id'];
        $singleSave['taxi_id'] = $_POST['id'];
        $quadSharingSave['taxi_id'] = $_POST['id'];
        $pkg_detail_id = $db->insert('taxi_details', $twinSave);
        $pkg_detail_id = $db->insert('taxi_details', $cwbSave);
        $pkg_detail_id = $db->insert('taxi_details', $cnbSave);
        $pkg_detail_id = $db->insert('taxi_details', $tripleSave);
        $pkg_detail_id = $db->insert('taxi_details', $singleSave);
        $pkg_detail_id = $db->insert('taxi_details', $quadSharingSave);
    } else {

        // echo "<pre>";
        // print_r($_POST);
        // echo "</pre>";
        // die();
        $msg = "added";
        $db->orderBy('id', 'desc');
        $package_last = $db->getOne("taxi");
        if ($package_last) {
            $data_to_store['package_code'] = sprintf("PG%04d", $package_last['id'] + 1);
        } else {
            $data_to_store['package_code'] = sprintf("PG%04d",  1);
        }
        $db = getDbInstance();


        $data_to_store['destination'] = !empty($_POST['destination']) ? $_POST['destination'] : null;

        $data_to_store['traveling_from'] = !empty($_POST['traveling_from']) ? $_POST['traveling_from'] : null;

        $description = $_POST['description'];
        $data_to_store['description'] = $description;
        // echo "<pre>";
        // print_r($data_to_store);
        // echo "</pre>";
        // die();
        $package_id = $db->insert('taxi', $data_to_store);
        // Overview Section new Add
        if ($package_id) {
            // Handle multiple image uploads for new section
            $target_dir = "uploads/taxi/overview/";

            // Check if the directory exists; if not, create it
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $image_files = $_FILES['images'];

            if (isset($image_files) && !empty($image_files['name'][0])) {
                foreach ($image_files['name'] as $key => $name) {
                    $tmp_name = $image_files['tmp_name'][$key];
                    $size = $image_files['size'][$key];
                    $error = $image_files['error'][$key];

                    if ($error === UPLOAD_ERR_OK && $size <= 10000000) {
                        $target_file = $target_dir . time() . "_" . basename($name);
                        if (move_uploaded_file($tmp_name, $target_file)) {

                            $imageData = [
                                'taxi_id' => $package_id,
                                'image_path' => $target_file
                            ];
                            $inserted = $db->insert('overview_section_images', $imageData);
                            if (!$inserted) {
                                die("Insert failed: " . $db->getLastError() . " | Query: " . $db->getLastQuery());
                            }
                        }
                    } else {
                        echo "Failed to upload file: " . $name . "<br>";
                    }
                }
            }
        } else {
            echo "<script>alert('Error creating overview section!');</script>";
        }
        // die();
        $db = getDbInstance();


        $structuredServices = [];

        foreach ($_POST['service_name'] as $index => $name) {
            $structuredServices[] = [
                'taxi_id' => $package_id,
                'name' => $name,
                'type' => $_POST['service_type'][$index] ?? null,
                'amount' => $_POST['service_amount'][$index] ?? null
            ];
        }
        $db->insertMulti('services', $structuredServices);

        foreach ($details as $detail) {
            $detail['taxi_id'] = $package_id;
            $pkg_detail_id = $db->insert('taxi_details', $detail);
        }

        $twinSave['taxi_id'] = $package_id;
        $cwbSave['taxi_id'] = $package_id;
        $cnbSave['taxi_id'] = $package_id;
        $tripleSave['taxi_id'] = $package_id;
        $singleSave['taxi_id'] = $package_id;
        $quadSharingSave['taxi_id'] = $package_id;
        $pkg_detail_id = $db->insert('taxi_details', $twinSave);
        $pkg_detail_id = $db->insert('taxi_details', $cwbSave);
        $pkg_detail_id = $db->insert('taxi_details', $cnbSave);
        $pkg_detail_id = $db->insert('taxi_details', $tripleSave);
        $pkg_detail_id = $db->insert('taxi_details', $singleSave);
        $pkg_detail_id = $db->insert('taxi_details', $quadSharingSave);
    }
    if ($package_id) {
        $_SESSION['success'] = "Package $msg successfully!";
        header('location: view-taxi-booking.php');
        exit();
    } else {
        echo 'insert failed: ' . $db->getLastError();
        exit();
    }
}

$id = isset($_GET['crm']) && !empty($_GET['crm']) ? decryptId($_GET['crm']) : "";
$edit = false;
if (!empty($id)) {
    $edit = true;
    $db = getDbInstance();
    $db->where('id', $id);
    $data = $db->getOne("taxi");

    $db = getDbInstance();
    $db->where('itineary', ['TWIN Fixed', 'CWB Fixed', 'CNB Fixed', 'TRIPLE Fixed', 'SINGLE Fixed', 'QUAD SHARING Fixed'], 'not in');
    $db->where('taxi_id', $id);
    $package_details = $db->get("taxi_details");
    // echo "<pre>";
    // print_r($package_details);
    // die();
    $db = getDbInstance();
    $db->where('itineary', 'TWIN Fixed');
    $db->where('id', $id);
    $twin = $db->getOne("taxi_details");

    $db = getDbInstance();
    $db->where('itineary', 'CWB Fixed');
    $db->where('id', $id);
    $cwb = $db->getOne("taxi_details");

    $db = getDbInstance();
    $db->where('itineary', 'CNB Fixed');
    $db->where('id', $id);
    $cnb = $db->getOne("taxi_details");

    $db = getDbInstance();
    $db->where('itineary', 'TRIPLE Fixed');
    $db->where('id', $id);
    $triple = $db->getOne("taxi_details");

    $db = getDbInstance();
    $db->where('itineary', 'SINGLE Fixed');
    $db->where('id', $id);
    $single = $db->getOne("taxi_details");

    $db = getDbInstance();
    $db->where('itineary', 'QUAD SHARING Fixed');
    $db->where('id', $id);
    $quad_sharing = $db->getOne("taxi_details");


    // Get associated images
    $db->where('taxi_id', $id);
    $images = $db->get("overview_section_images");

    $db->where('taxi_id', $id);
    $services = $db->get("services");
    // echo "<pre>";
    // print_r($services);
    // echo "</pre>";
    // die();
}

$db = getDbInstance();
$db->where('location', "", "<>");
$db->groupBy('location');
$locations = $db->get("hotels", null, 'location');
$locationOptions = "<option value=''>Choose location..</option>";
foreach ($locations as $location) {
    $locationOptions .= "<option>" . $location['location'] . "</option>";
}

// Fetch destinations
$destinations = $db->get("destination", null, 'id, name');
$traveling_from = $db->get("traveling_from", null, 'id, name');


$db->where('status', 'active');
$cars = $db->get("carlist", null, 'id, name');
$carlisting = json_encode($cars);
$service_type = ['Cumulative', 'Per Person', 'Per Service'];
include BASE_PATH . '/includes/header.php';
?>
<style>
    .select2-selection__choice {
        background-color: #007bff !important;
        color: #fff !important;
        padding: 6px 12px !important;
        border-radius: 15px !important;
        display: flex !important;
        align-items: center !important;
        font-size: 14px !important;
        margin: 4px !important;
    }


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

    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Taxi/</span> <?= $edit ? 'Edit' : "Add" ?> taxi</h4>

            <!-- Basic Layout -->
            <div class="row">
                <div class="col-xl">
                    <form action="" method="post" id="hotel_form" enctype="multipart/form-data">

                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Create Taxi</h5>
                                <!-- <small class="text-muted float-end">Product Code</small> -->

                            </div>
                            <div class="card-body">

                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-company">Package Name</label>
                                    <input type="text" class="form-control" name="package_name" value="<?php echo xss_clean($edit ? $data['package_name'] : ''); ?>" required />
                                </div>

                                <div class="row mb-3">

                                    <div class="col-md">
                                        <label class="form-label" for="traveling_from">Select Traveling From</label>
                                        <div class="input-group">
                                            <label class="input-group-text" for="traveling_from">Traveling From</label>
                                            <select class="form-select" id="traveling_from" name="traveling_from">
                                                <option value="">Choose location...</option>
                                                <?php foreach ($traveling_from as $location): ?>
                                                    <option value="<?= $location['id']; ?>" <?= ($edit && $data['traveling_from'] == $location['id']) ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($location['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Destination Selection -->
                                    <div class="col-md">
                                        <label class="form-label" for="destination">Select Destination</label>
                                        <div class="input-group">
                                            <label class="input-group-text" for="destination">Destination</label>
                                            <select class="form-select" id="destination" name="destination">
                                                <option value="">Choose destination...</option>
                                                <?php foreach ($destinations as $destination): ?>
                                                    <option value="<?= $destination['id']; ?>" <?= ($edit && $data['destination'] == $destination['id']) ? 'selected' : ''; ?>>
                                                        <?= htmlspecialchars($destination['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-email">Select Duration</label>
                                    <div class="input-group">
                                        <label class="input-group-text" for="inputGroupSelect01">Options</label>
                                        <select class="form-select" id="duration" name="duration" <?php echo  $edit ? "disabled" : "" ?> required>
                                            <option value="">Choose...</option>
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

                                <!-- Add overview section -->

                                <!-- Frontend Form -->
                                <div class="content-wrapper">
                                    <div class="flex-grow-1 container-p-y">
                                        <h4 class="py-3 mb-4"><?= $edit ? 'Edit' : 'Add' ?> Overview Section</h4>
                                        <div class="row mb-3">
                                            <div class="col-md">
                                                <label class="form-label">Product Description</label>
                                                <textarea class="form-control" name="description" rows="10"><?= isset($data['description']) ? $data['description'] : ''; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md">
                                                <label class="form-label">Upload Images</label>
                                                <input type="file" name="images[]" class="form-control" multiple />
                                                <?php if ($edit && isset($images)): ?>
                                                    <div class="mt-3">
                                                        <?php foreach ($images as $image): ?>
                                                            <div style="display: inline-block; margin-right: 10px; text-align: center;">
                                                                <img src="<?= $image['image_path']; ?>" height="100px" width="100px" style="display: block;" />
                                                                <a href="delete-taxi-image.php?id=<?= $image['id']; ?>&section_id=<?= $id; ?>" class="btn btn-sm btn-danger mt-1">Delete</a>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Overview Section -->


                                <!-- Frontend Form -->
                                <div class="content-wrapper">
                                    <div class="flex-grow-1 container-p-y">
                                        <h4 class="py-3 mb-4"><?= $edit ? 'Edit' : 'Add' ?> Service Information</h4>
                                        <div id="repeater-fields">

                                            <?php if (isset($edit) && $edit && isset($services) && is_array($services)) {
                                                foreach ($services as $index => $service) { ?>
                                                    <div class="row mb-3 repeat-box">
                                                        <!-- Service Name -->
                                                        <div class="col-md">
                                                            <label class="form-label">Service Name</label>
                                                            <input type="text" class="form-control" name="service_name[]" value="<?php echo htmlspecialchars($service['name']); ?>" required />
                                                        </div>
                                                        <!-- Service Type -->
                                                        <div class="col-md">
                                                            <label class="form-label">Service Type</label>
                                                            <div class="input-group">
                                                                <label class="input-group-text">Options</label>
                                                                <select class="form-select" name="service_type[]" required>
                                                                    <option value="">Choose...</option>
                                                                    <?php
                                                                    foreach ($service_type as $service_t) {
                                                                        $selected = ($service_t == $service['type']) ? "selected" : "";
                                                                        echo "<option value=\"$service_t\" $selected>$service_t</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!-- Amount -->
                                                        <div class="col-md">
                                                            <label class="form-label">Amount</label>
                                                            <input type="text" class="form-control phone-mask" name="service_amount[]" value="<?php echo htmlspecialchars($service['amount']); ?>" required />
                                                        </div>
                                                        <?php if ($index > 0) { ?>
                                                            <div class="col-md remove-more-btn"
                                                                style="margin-top: -6px; position: absolute; left: 1296px; cursor: pointer;"
                                                                onclick="removeRow(this)">
                                                                Remove
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                            <?php }
                                            } ?>
                                        </div>

                                        <div class="row mb-3 add-more-row" id="add-more-date">
                                            <div class="col-md">
                                                <span class="add-more-btn">+ Add More</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Overview Section -->

                                <div class="table-responsive text-nowrap border-light border-solid mb-3">
                                    <table class="table">
                                        <thead>
                                            <tr class="text-nowrap bg-dark align-middle">
                                                <th class="text-white border-right-white sticky-col">Day</th>
                                                <th class="text-white border-right-white sticky-col">Location</th>
                                                <th class="text-white border-right-white sticky-col">Itineary</th>

                                                <?php foreach ($cars as $vehicle) : ?>
                                                    <th class="text-white border-right-white"><?= $vehicle['name'] ?></th>
                                                <?php endforeach; ?>

                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0" id="existingTable">
                                            <?php
                                            if ($edit) {
                                                foreach ($package_details as $j => $pack) {
                                            ?>

                                                    <tr>
                                                        <td class="border-right-dark sticky-col">
                                                            <input type="number" class="form-control phone-mask w-px-75" value="<?= $pack['day'] ?>" name="detail[<?= $j ?>][day]" placeholder="Day" />
                                                        </td>
                                                        <td class="border-right-dark sticky-col">
                                                            <select class="form-select" name="detail[<?= $j ?>][location]">
                                                                <?php foreach ($locations as $location) { ?>
                                                                    <option <?= ($location['location'] == $pack['location']) ? "selected" : "" ?>><?= $location['location'] ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                        <td class="border-right-dark sticky-col">
                                                            <textarea class="form-control w-px-300 h-px-75" name="detail[<?= $j ?>][itineary]" placeholder="Enter Short Itinerary"><?= $pack['itineary'] ?></textarea>
                                                        </td>

                                                        <?php foreach ($cars as $vehicle) : ?>
                                                            <?php
                                                            $vehicleName = strtolower(str_replace(' ', '_', $vehicle['name']));
                                                            ?>
                                                            <td class="border-right-dark">
                                                                <input type="number" class="form-control phone-mask w-px-100"
                                                                    value="<?= isset($pack[$vehicleName]) ? $pack[$vehicleName] : 0 ?>"
                                                                    name="detail[<?= $j ?>][<?= $vehicleName ?>]" />
                                                            </td>
                                                        <?php endforeach; ?>



                                                    </tr>
                                            <?php
                                                }
                                            }
                                            ?>


                                            <?php
                                            $sections = ['twin', 'cwb', 'cnb', 'triple', 'single'];
                                            foreach ($sections as $section) :
                                            ?>
                                                <tr>
                                                    <td class="border-right-dark sticky-col"></td>
                                                    <td class="border-right-dark sticky-col"></td>
                                                    <td class="border-right-dark sticky-col"><?= strtoupper($section) ?></td>

                                                    <?php foreach ($cars as $vehicle) : ?>
                                                        <?php
                                                        $vehicleName = strtolower(str_replace(' ', '_', $vehicle['name']));
                                                        ?>
                                                        <td class="border-right-dark" id="<?= $section . '-' . $vehicleName ?>"></td>
                                                    <?php endforeach; ?>

                                                    <input type="hidden" value="" name="<?= $section ?>[day]" />
                                                    <input type="hidden" value="" name="<?= $section ?>[location]" />
                                                    <input type="hidden" value="<?= strtoupper($section) ?> Fixed" name="<?= $section ?>[itineary]" />

                                                    <?php foreach ($cars as $vehicle) : ?>
                                                        <?php
                                                        $vehicleName = strtolower(str_replace(' ', '_', $vehicle['name']));
                                                        ?>
                                                        <input type="hidden" value="<?= ${$section}[$vehicleName] ?? "" ?>" name="<?= $section ?>[<?= $vehicleName ?>]" />
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endforeach; ?>

                                            <tr>
                                                <td class="border-right-dark sticky-col"></td>
                                                <td class="border-right-dark sticky-col"></td>
                                                <td class="border-right-dark sticky-col">QUAD SHARING</td>

                                                <?php foreach ($cars as $vehicle) : ?>
                                                    <?php
                                                    $vehicleName = strtolower(str_replace(' ', '_', $vehicle['name']));
                                                    ?>
                                                    <td class="border-right-dark" id="quad_sharing-<?= $vehicleName ?>"></td>
                                                <?php endforeach; ?>

                                                <input type="hidden" value="" name="quad_sharing[day]" />
                                                <input type="hidden" value="" name="quad_sharing[location]" />
                                                <input type="hidden" value="QUAD SHARING Fixed" name="quad_sharing[itineary]" />

                                                <?php foreach ($cars as $vehicle) : ?>
                                                    <?php
                                                    $vehicleName = strtolower(str_replace(' ', '_', $vehicle['name']));
                                                    ?>
                                                    <input type="hidden" value="<?= $quad_sharing[$vehicleName] ?? "" ?>" name="quad_sharing[<?= $vehicleName ?>]" />
                                                <?php endforeach; ?>
                                            </tr>



                                        </tbody>
                                    </table>
                                </div>

                                <input type="hidden" name="id" value="<?php echo $id ?>" />
                                <button type="submit" class="btn btn-primary">SAVE</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- / Content -->
</div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $("#agents").select2({
            placeholder: "Choose agents...",
            allowClear: true
        });
    });

    document.getElementById('add-more-date').addEventListener('click', function() {

        document.querySelector('#repeater-fields').insertAdjacentHTML(
            'beforeend',
            `<div class="row mb-3 repeat-box">
             <div class="col-md">
                     <label class="form-label" for="basic-default-phone">Service Name</label>
                    <input type="text" class="form-control" name="service_name[]"  required />
                </div>
                <div class="col-md">
                  <label class="form-label" for="basic-default-email">Service Type</label>
                    <div class="input-group">
                      <label class="input-group-text" for="inputGroupSelect01">Options</label>
                <select class="form-select" id="inputGroupSelect01" name="service_type[]" required>
    <option value="">Choose...</option>
    <?php foreach ($service_type as $service_t): ?>
        <option value="<?= htmlspecialchars($service_t, ENT_QUOTES, 'UTF-8'); ?>"
            <?= ($edit && isset($data['type']) && $service_t == $data['type']) ? 'selected' : ''; ?>>
            <?= htmlspecialchars($service_t, ENT_QUOTES, 'UTF-8'); ?>
        </option>
    <?php endforeach; ?>
</select>
                    </div>
                </div>
                <div class="col-md">
                     <label class="form-label" for="basic-default-phone">Amount</label>
                    <input type="text" class="form-control phone-mask" name="service_amount[]"  required/>

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

    document.getElementById('duration').addEventListener('change', function() {
        const pattern = /(\d+)\s*Nights?\s*(\d+)\s*Days?/i;
        const matches = this.value.match(pattern);

        if (!matches || matches.length < 3) {
            console.error("Invalid input format for duration.");
            return;
        }

        let numberOfRows = parseInt(matches[2], 10);
        let tbody = document.getElementById('existingTable'); // ✅ Get <tbody>, not <table>

        // Remove previously added rows
        let rowsToRemove = tbody.getElementsByClassName('new-row');
        while (rowsToRemove.length > 0) {
            rowsToRemove[0].remove();
        }

        let carList = <?= $carlisting ?> || []; // Ensure car list is valid
        let locationOptions = `<?= $locationOptions ?>`;
        let dayval = numberOfRows;

        for (let j = 0; j < numberOfRows; j++) {
            let row = document.createElement('tr'); // Create a new row
            row.classList.add('new-row');

            let html = `
            <td class="border-right-dark sticky-col">
                <input type="number" class="form-control phone-mask w-px-75" name="detail[${j}][day]" value="${dayval--}" placeholder="Day" required/>
            </td>
            <td class="border-right-dark sticky-col">
                <select class="form-select" name="detail[${j}][location]">
                    ${locationOptions}
                </select>
            </td>
            <td class="border-right-dark sticky-col">
                <textarea class="form-control w-px-300 h-px-75" name="detail[${j}][itineary]" placeholder="Enter Short Itinerary"></textarea>
            </td>
        `;

            carList.forEach(car => {
                let formattedName = car.name.toLowerCase().replace(/\s+/g, '_');

                html += `
                <td class="border-right-dark">
                    <input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][${formattedName}]" />
                </td>
            `;
            });

            row.innerHTML = html;
            tbody.insertBefore(row, tbody.firstChild);
        }
    });
</script>
<?php include BASE_PATH . '/includes/footer.php'; ?>