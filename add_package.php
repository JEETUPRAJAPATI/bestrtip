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
  $custom_package = isset($data_to_store['custom_package']) && $data_to_store['custom_package'] == 'on' ? "1" : "0";

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

  $data_to_store['custom_package'] = $custom_package;
  $db = getDbInstance();

  if (isset($_POST['id']) && !empty($_POST['id'])) {
    $msg = "edited";

    $data_to_store['destination'] = !empty($_POST['destination']) ? $_POST['destination'] : null;
    $data_to_store['traveling_from'] = !empty($_POST['traveling_from']) ? $_POST['traveling_from'] : null;
    // Check if agents are selected, else set NULL
    if (!empty($_POST['agents']) && is_array($_POST['agents'])) {
      $data_to_store['agents'] = implode(",", $_POST['agents']);
    } else {
      $data_to_store['agents'] = null;
    }

    $description = filter_input(INPUT_POST, 'description');
    $data_to_store['description'] = isset($description) ? $description : 'NULL';

    $db->where('id', $_POST['id']);
    $package_id = $db->update('packages', $data_to_store);

    // echo "<pre>";
    // print_r($_POST['id']);
    // echo "</pre>";
    // die();
    if ($package_id) {
      // Handle image updates for editing
      $target_dir = "uploads/package/overview/";

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
                'package_id' => $_POST['id'],
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
    $db->where('package_id', $_POST['id']);
    $db->delete('package_details');
    // Insert here
    $db = getDbInstance();

    $structuredServices = [];
    $existingServiceIds = [];
    $existingServices = $db->where('package_id', $_POST['id'])->get('services');
    // Store IDs of existing services to track updates

    foreach ($_POST['service_name'] as $index => $name) {
      $serviceId = $_POST['service_id'][$index] ?? null; // Capture service ID if exists

      $serviceData = [
        'package_id' => $_POST['id'],
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
      $db->where('package_id', $package_id);
      $db->where('id', $toDelete, 'IN');
      $db->delete('services');
    }
    foreach ($details as $detail) {
      $detail['package_id'] = $_POST['id'];
      $pkg_detail_id = $db->insert('package_details', $detail);
    }
    $twinSave['package_id'] = $_POST['id'];
    $cwbSave['package_id'] = $_POST['id'];
    $cnbSave['package_id'] = $_POST['id'];
    $tripleSave['package_id'] = $_POST['id'];
    $singleSave['package_id'] = $_POST['id'];
    $quadSharingSave['package_id'] = $_POST['id'];
    $pkg_detail_id = $db->insert('package_details', $twinSave);
    $pkg_detail_id = $db->insert('package_details', $cwbSave);
    $pkg_detail_id = $db->insert('package_details', $cnbSave);
    $pkg_detail_id = $db->insert('package_details', $tripleSave);
    $pkg_detail_id = $db->insert('package_details', $singleSave);
    $pkg_detail_id = $db->insert('package_details', $quadSharingSave);
  } else {

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // die();
    $msg = "added";
    $db->orderBy('id', 'desc');
    $package_last = $db->getOne("packages");
    if ($package_last) {
      $data_to_store['package_code'] = sprintf("PG%04d", $package_last['id'] + 1);
    } else {
      $data_to_store['package_code'] = sprintf("PG%04d",  1);
    }
    $db = getDbInstance();


    $data_to_store['destination'] = !empty($_POST['destination']) ? $_POST['destination'] : null;

    $data_to_store['traveling_from'] = !empty($_POST['traveling_from']) ? $_POST['traveling_from'] : null;
    // Check if agents are selected, else set NULL
    if (!empty($_POST['agents']) && is_array($_POST['agents'])) {
      $data_to_store['agents'] = implode(",", $_POST['agents']);
    } else {
      $data_to_store['agents'] = null;
    }
    $description = $_POST['description'];
    $data_to_store['description'] = $description;
    // echo "<pre>";
    // print_r($data_to_store);
    // echo "</pre>";
    // die();
    $package_id = $db->insert('packages', $data_to_store);
    // Overview Section new Add
    if ($package_id) {
      // Handle multiple image uploads for new section
      $target_dir = "uploads/package/overview/";

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
                'package_id' => $package_id,
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
        'package_id' => $package_id,
        'name' => $name,
        'type' => $_POST['service_type'][$index] ?? null,
        'amount' => $_POST['service_amount'][$index] ?? null
      ];
    }
    $db->insertMulti('services', $structuredServices);

    foreach ($details as $detail) {
      $detail['package_id'] = $package_id;
      $pkg_detail_id = $db->insert('package_details', $detail);
    }

    $twinSave['package_id'] = $package_id;
    $cwbSave['package_id'] = $package_id;
    $cnbSave['package_id'] = $package_id;
    $tripleSave['package_id'] = $package_id;
    $singleSave['package_id'] = $package_id;
    $quadSharingSave['package_id'] = $package_id;
    $pkg_detail_id = $db->insert('package_details', $twinSave);
    $pkg_detail_id = $db->insert('package_details', $cwbSave);
    $pkg_detail_id = $db->insert('package_details', $cnbSave);
    $pkg_detail_id = $db->insert('package_details', $tripleSave);
    $pkg_detail_id = $db->insert('package_details', $singleSave);
    $pkg_detail_id = $db->insert('package_details', $quadSharingSave);
  }
  if ($package_id) {
    $_SESSION['success'] = "Package $msg successfully!";
    header('location: package.php');
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
  $data = $db->getOne("packages");

  $db = getDbInstance();
  $db->where('itineary', ['TWIN Fixed', 'CWB Fixed', 'CNB Fixed', 'TRIPLE Fixed', 'SINGLE Fixed', 'QUAD SHARING Fixed'], 'not in');
  $db->where('package_id', $id);
  $package_details = $db->get("package_details");

  $db = getDbInstance();
  $db->where('itineary', 'TWIN Fixed');
  $db->where('package_id', $id);
  $twin = $db->getOne("package_details");

  $db = getDbInstance();
  $db->where('itineary', 'CWB Fixed');
  $db->where('package_id', $id);
  $cwb = $db->getOne("package_details");

  $db = getDbInstance();
  $db->where('itineary', 'CNB Fixed');
  $db->where('package_id', $id);
  $cnb = $db->getOne("package_details");

  $db = getDbInstance();
  $db->where('itineary', 'TRIPLE Fixed');
  $db->where('package_id', $id);
  $triple = $db->getOne("package_details");

  $db = getDbInstance();
  $db->where('itineary', 'SINGLE Fixed');
  $db->where('package_id', $id);
  $single = $db->getOne("package_details");

  $db = getDbInstance();
  $db->where('itineary', 'QUAD SHARING Fixed');
  $db->where('package_id', $id);
  $quad_sharing = $db->getOne("package_details");


  // Get associated images
  $db->where('package_id', $id);
  $images = $db->get("overview_section_images");

  $db->where('package_id', $id);
  $services = $db->get("services");
  // echo "<pre>";
  // print_r($services);
  // echo "</pre>";
  // die();
}

$db = getDbInstance();
//$db->where('location', "", "<>");
//$db->groupBy('location');
$locations = $db->get("location", null, 'location');
$locationOptions = "<option value=''>Choose location..</option>";
foreach ($locations as $location) {
  $locationOptions .= "<option>" . $location['location'] . "</option>";
}

$agents = $db->get("agents", null, 'id, full_name');

// Fetch destinations
$destinations = $db->get("destination", null, 'id, name');
$traveling_from = $db->get("traveling_from", null, 'id, name');

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
      <h4 class="py-3 mb-4"><span class="text-muted fw-light">Package/</span> <?= $edit ? 'Edit' : "Add" ?> Package</h4>

      <!-- Basic Layout -->
      <div class="row">
        <div class="col-xl">
          <form action="" method="post" id="hotel_form" enctype="multipart/form-data">

            <div class="card mb-4">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Create Package</h5>
                <!-- <small class="text-muted float-end">Product Code</small> -->
                <td class="border-right-dark">
                  <div class="form-check form-switch">
                    <span>Custom Package</span>
                    <input class="form-check-input <?= (!empty($data) && !empty($data['custom_package']) && $data['custom_package'] == 1) ? 'active' : 'inactive' ?>"
                      type="checkbox"
                      name="custom_package"
                      id="flexSwitchCheckChecked"
                      <?= (!empty($data) && !empty($data['custom_package']) && $data['custom_package'] == 1) ? 'checked' : ''; ?>
                      <?= isset($data['custom_package']) ? 'onchange="this.form.submit()"' : '' ?>>
                  </div>
                </td>
              </div>
              <div class="card-body">

                <div class="mb-3">
                  <label class="form-label" for="basic-default-company">Package Name</label>
                  <input type="text" class="form-control" name="package_name" value="<?php echo xss_clean($edit ? $data['package_name'] : ''); ?>" required />
                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Permit</label>
                    <input type="number" id="basic-default-phone" class="form-control phone-mask" name="permit" value="<?php echo xss_clean($edit ? $data['permit'] : ''); ?>" required />
                  </div>
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Guide</label>
                    <input type="number" id="basic-default-phone" class="form-control phone-mask" name="guide" value="<?php echo xss_clean($edit ? $data['guide'] : ''); ?>" required />
                  </div>
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


                <!-- Agents Multi-Selection -->
                <div class="mb-3">
                  <label class="form-label" for="agents">Select Agents</label>
                  <select class="form-select" name="agents[]" id="agents" multiple>
                    <option value="">Choose agent...</option>
                    <?php
                    $selectedAgents = $edit ? explode(",", $data['agents']) : [];
                    foreach ($agents as $agent): ?>
                      <option value="<?= $agent['id']; ?>" <?= in_array($agent['id'], $selectedAgents) ? 'selected' : ''; ?>>
                        <?= $agent['full_name']; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
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
                                <a href="delete-image.php?id=<?= $image['id']; ?>&section_id=<?= $id; ?>" class="btn btn-sm btn-danger mt-1">Delete</a>
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
                        <th class="text-white border-right-white">1*</th>
                        <th class="text-white border-right-white">2**</th>
                        <th class="text-white border-right-white">3***</th>
                        <th class="text-white border-right-white">4****</th>
                        <th class="text-white border-right-white">5*****</th>
                        <th class="text-white border-right-white">Luxury Plus</th>
                        <th class="text-white border-right-white">Premium</th>
                        <th class="text-white border-right-white">Premium Plus</th>
                        <th class="text-white border-right-white">Meal Plan</th>
                        <th class="text-white border-right-white">Fortuner</th>
                        <th class="text-white border-right-white">Coach</th>
                        <th class="text-white border-right-white">Tempo</th>
                        <th class="text-white border-right-white">Cryista</th>
                        <th class="text-white border-right-white">Innova</th>
                        <th class="text-white border-right-white">Zyalo / Ertiga</th>
                        <th class="text-white border-right-white">Bike</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="existingTable">
                      <?php
                      if ($edit) {
                        $total_budget = 0;
                        foreach ($package_details as $j => $pack) {
                          $total_budget += (int)$pack['budget'];
                      ?>

                          <tr>
                            <td class="border-right-dark sticky-col"><input type="number" class="form-control phone-mask w-px-75" value="<?= $pack['day'] ?>" name="detail[<?= $j ?>][day]" placeholder="Day" /></td>
                            <td class="border-right-dark sticky-col">
                              <select class="form-select" name="detail[<?= $j ?>][location]">
                                <?php foreach ($locations as $location) { ?>
                                  <option <?php echo ($location['location'] == $pack['location']) ? "selected" : "" ?>><?= $location['location'] ?></option>
                                <?php } ?>
                              </select>
                            </td>
                            <td class="border-right-dark sticky-col"><textarea class="form-control w-px-300 h-px-75" name="detail[<?= $j ?>][itineary]" placeholder="Enter Short Itineary"><?= $pack['itineary'] ?></textarea></td>

                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['budget'] ?>" name="detail[<?= $j ?>][budget]" onchange="calculateBudgetSum()" placeholder="" /></td>
                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['standard'] ?>" name="detail[<?= $j ?>][standard]" onchange="calculateStandardSum()" placeholder="" /></td>
                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['deluxe'] ?>" name="detail[<?= $j ?>][deluxe]" onchange="calculateDeluxeSum()" placeholder="" /></td>
                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['super_deluxe'] ?>" name="detail[<?= $j ?>][super_deluxe]" onchange="calculateSuperDeluxeSum()" placeholder="" /></td>

                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['luxury'] ?>" name="detail[<?= $j ?>][luxury]" onchange="calculateLuxurySum()" placeholder="" /></td>
                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['luxury_plus'] ?>" name="detail[<?= $j ?>][luxury_plus]" onchange="calculateLuxuryPlusSum()" placeholder="" /></td>

                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['premium'] ?>" name="detail[<?= $j ?>][premium]" onchange="calculatePremiumSum()" placeholder="" /></td>
                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['premium_plus'] ?>" name="detail[<?= $j ?>][premium_plus]" onchange="calculatePremiumPlusSum()" placeholder="" /></td>
                            <td class="border-right-dark"><input type="text" class="form-control phone-mask w-px-100" value="<?= $pack['meal_plan'] ?>" name="detail[<?= $j ?>][meal_plan]" onchange="calculatePremiumPlusSum()" placeholder="" /></td>

                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['fortuner'] ?>" name="detail[<?= $j ?>][fortuner]" /></td>
                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['coach'] ?>" name="detail[<?= $j ?>][coach]" /></td>
                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['tempo'] ?>" name="detail[<?= $j ?>][tempo]" /></td>
                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['cryista'] ?>" name="detail[<?= $j ?>][cryista]" /></td>
                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['innova'] ?>" name="detail[<?= $j ?>][innova]" /></td>
                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['zyalo_ertiga'] ?>" name="detail[<?= $j ?>][zyalo_ertiga]" /></td>
                            <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" value="<?= $pack['bike'] ?>" name="detail[<?= $j ?>][bike]" /></td>

                          </tr>
                      <?php
                        }
                      }
                      ?>

                      <tr>
                        <td class="border-right-dark sticky-col"></td>
                        <td class="border-right-dark sticky-col"></td>
                        <td class="border-right-dark sticky-col">TWIN</td>
                        <td class="border-right-dark" id="twin-budget">₹<?= $twin['budget'] ?? "" ?></td>
                        <td class="border-right-dark" id="twin-standard">₹<?= $twin['standard'] ?? "" ?></td>
                        <td class="border-right-dark" id="twin-deluxe">₹<?= $twin['deluxe'] ?? "" ?></td>
                        <td class="border-right-dark" id="twin-super-deluxe">₹<?= $twin['super_deluxe'] ?? "" ?></td>
                        <td class="border-right-dark" id="twin-luxury">₹<?= $twin['luxury'] ?? "" ?></td>
                        <td class="border-right-dark" id="twin-luxury_plus">₹<?= $twin['luxury_plus'] ?? "" ?></td>
                        <td class="border-right-dark" id="twin-premium">₹<?= $twin['premium'] ?? "" ?></td>
                        <td class="border-right-dark" id="twin-premium_plus">₹<?= $twin['premium_plus'] ?? "" ?></td>


                        <td class="border-right-dark" id="twin-meal_plan"></td>
                        <td class="border-right-dark" id="twin-fortuner"></td>
                        <td class="border-right-dark" id="twin-coach"></td>
                        <td class="border-right-dark" id="twin-tempo"></td>
                        <td class="border-right-dark" id="twin-cryista"></td>
                        <td class="border-right-dark" id="twin-innova"></td>
                        <td class="border-right-dark" id="twin-zyalo_ertiga"></td>
                        <td class="border-right-dark" id="twin-bike"></td>
                        <input type="hidden" value="" name="twin[day]" />
                        <input type="hidden" value="" name="twin[location]" />
                        <input type="hidden" value="TWIN Fixed" name="twin[itineary]" />
                        <input type="hidden" value="<?= $twin['budget'] ?? "" ?>" name="twin[budget]" />
                        <input type="hidden" value="<?= $twin['standard'] ?? "" ?>" name="twin[standard]" />
                        <input type="hidden" value="<?= $twin['deluxe'] ?? "" ?>" name="twin[deluxe]" />
                        <input type="hidden" value="<?= $twin['super_deluxe'] ?? "" ?>" name="twin[super_deluxe]" />
                        <input type="hidden" value="<?= $twin['luxury'] ?? "" ?>" name="twin[luxury]" />
                        <input type="hidden" value="<?= $twin['luxury_plus'] ?? "" ?>" name="twin[luxury_plus]" />
                        <input type="hidden" value="<?= $twin['premium'] ?? "" ?>" name="twin[premium]" />
                        <input type="hidden" value="<?= $twin['premium_plus'] ?? "" ?>" name="twin[premium_plus]" />
                        <input type="hidden" value="<?= $twin['meal_plan'] ?? "" ?>" name="twin[meal_plan]" />

                        <input type="hidden" value="<?= $twin['fortuner'] ?? "" ?>" name="twin[fortuner]" />
                        <input type="hidden" value="<?= $twin['coach'] ?? "" ?>" name="twin[coach]" />
                        <input type="hidden" value="<?= $twin['tempo'] ?? "" ?>" name="twin[tempo]" />
                        <input type="hidden" value="<?= $twin['cryista'] ?? "" ?>" name="twin[cryista]" />
                        <input type="hidden" value="<?= $twin['innova'] ?? "" ?>" name="twin[innova]" />
                        <input type="hidden" value="<?= $twin['zyalo_ertiga'] ?? "" ?>" name="twin[zyalo_ertiga]" />
                        <input type="hidden" value="<?= $twin['bike'] ?? "" ?>" name="twin[bike]" />
                      </tr>
                      <tr>
                        <td class="border-right-dark sticky-col"></td>
                        <td class="border-right-dark sticky-col"></td>
                        <td class="border-right-dark sticky-col">CWB</td>
                        <td class="border-right-dark" id="cwb-budget">₹<?= $cwb['budget'] ?? "" ?></td>
                        <td class="border-right-dark" id="cwb-standard">₹<?= $cwb['standard'] ?? "" ?></td>
                        <td class="border-right-dark" id="cwb-deluxe">₹<?= $cwb['deluxe'] ?? "" ?></td>
                        <td class="border-right-dark" id="cwb-super-deluxe">₹<?= $cwb['super_deluxe'] ?? "" ?></td>
                        <td class="border-right-dark" id="cwb-luxury">₹<?= $cwb['luxury'] ?? "" ?></td>
                        <td class="border-right-dark" id="cwb-luxury_plus">₹<?= $cwb['luxury_plus'] ?? "" ?></td>
                        <td class="border-right-dark" id="cwb-premium">₹<?= $cwb['premium'] ?? "" ?></td>
                        <td class="border-right-dark" id="cwb-premium_plus">₹<?= $cwb['premium_plus'] ?? "" ?></td>


                        <td class="border-right-dark" id="cwb-meal_plan"></td>
                        <td class="border-right-dark" id="cwb-fortuner"></td>
                        <td class="border-right-dark" id="cwb-coach"></td>
                        <td class="border-right-dark" id="cwb-tempo"></td>
                        <td class="border-right-dark" id="cwb-cryista"></td>
                        <td class="border-right-dark" id="cwb-innova"></td>
                        <td class="border-right-dark" id="cwb-zyalo_ertiga"></td>
                        <td class="border-right-dark" id="cwb-bike"></td>
                        <input type="hidden" value="" name="cwb[day]" />
                        <input type="hidden" value="" name="cwb[location]" />
                        <input type="hidden" value="CWB Fixed" name="cwb[itineary]" />
                        <input type="hidden" value="<?= $cwb['budget'] ?? "" ?>" name="cwb[budget]" />
                        <input type="hidden" value="<?= $cwb['standard'] ?? "" ?>" name="cwb[standard]" />
                        <input type="hidden" value="<?= $cwb['deluxe'] ?? "" ?>" name="cwb[deluxe]" />
                        <input type="hidden" value="<?= $cwb['super_deluxe']  ?? ""  ?>" name="cwb[super_deluxe]" />
                        <input type="hidden" value="<?= $cwb['luxury'] ?? "" ?>" name="cwb[luxury]" />
                        <input type="hidden" value="<?= $cwb['luxury_plus'] ?? "" ?>" name="cwb[luxury_plus]" />
                        <input type="hidden" value="<?= $cwb['premium'] ?? "" ?>" name="cwb[premium]" />
                        <input type="hidden" value="<?= $cwb['premium_plus'] ?? "" ?>" name="cwb[premium_plus]" />
                        <input type="hidden" value="<?= $cwb['meal_plan'] ?? "" ?>" name="cwb[meal_plan]" />


                        <input type="hidden" value="<?= $cwb['fortuner'] ?? "" ?>" name="twin[fortuner]" />
                        <input type="hidden" value="<?= $cwb['coach'] ?? ""  ?>" name="cwb[coach]" />
                        <input type="hidden" value="<?= $cwb['tempo'] ?? ""  ?>" name="cwb[tempo]" />
                        <input type="hidden" value="<?= $cwb['cryista'] ?? "" ?>" name="cwb[cryista]" />
                        <input type="hidden" value="<?= $cwb['innova'] ?? "" ?>" name="cwb[innova]" />
                        <input type="hidden" value="<?= $cwb['zyalo_ertiga'] ?? "" ?>" name="cwb[zyalo_ertiga]" />
                        <input type="hidden" value="<?= $cwb['bike'] ?? ""  ?>" name="cwb[bike]" />
                      </tr>
                      <tr>
                        <td class="border-right-dark sticky-col"></td>
                        <td class="border-right-dark sticky-col"></td>
                        <td class="border-right-dark sticky-col">CNB</td>
                        <td class="border-right-dark" id="cnb-budget">₹<?= $cnb['budget'] ?? ""  ?></td>
                        <td class="border-right-dark" id="cnb-standard">₹<?= $cnb['standard'] ?? ""  ?></td>
                        <td class="border-right-dark" id="cnb-deluxe">₹<?= $cnb['deluxe'] ?? ""  ?></td>
                        <td class="border-right-dark" id="cnb-super-deluxe">₹<?= $cnb['super_deluxe'] ?? ""  ?></td>
                        <td class="border-right-dark" id="cnb-luxury">₹<?= $cnb['luxury'] ?? "" ?></td>
                        <td class="border-right-dark" id="cnb-luxury_plus">₹<?= $cnb['luxury_plus'] ?? "" ?></td>
                        <td class="border-right-dark" id="cnb-premium">₹<?= $cnb['premium'] ?? "" ?></td>
                        <td class="border-right-dark" id="cnb-premium_plus">₹<?= $cnb['premium_plus'] ?? "" ?></td>



                        <td class="border-right-dark" id="cnb-meal_plan"></td>
                        <td class="border-right-dark" id="cnb-fortuner"></td>
                        <td class="border-right-dark" id="cnb-coach"></td>
                        <td class="border-right-dark" id="cnb-tempo"></td>
                        <td class="border-right-dark" id="cnb-cryista"></td>
                        <td class="border-right-dark" id="cnb-innova"></td>
                        <td class="border-right-dark" id="cnb-zyalo_ertiga"></td>
                        <td class="border-right-dark" id="cnb-bike"></td>

                        <input type="hidden" value="" name="cnb[day]" />
                        <input type="hidden" value="" name="cnb[location]" />
                        <input type="hidden" value="CNB Fixed" name="cnb[itineary]" />
                        <input type="hidden" value="<?= $cnb['budget'] ?? ""  ?>" name="cnb[budget]" />
                        <input type="hidden" value="<?= $cnb['standard'] ?? ""  ?>" name="cnb[standard]" />
                        <input type="hidden" value="<?= $cnb['deluxe'] ?? ""  ?>" name="cnb[deluxe]" />
                        <input type="hidden" value="<?= $cnb['super_deluxe'] ?? ""  ?>" name="cnb[super_deluxe]" />
                        <input type="hidden" value="<?= $cnb['luxury'] ?? "" ?>" name="cnb[luxury]" />
                        <input type="hidden" value="<?= $cnb['luxury_plus'] ?? "" ?>" name="cnb[luxury_plus]" />
                        <input type="hidden" value="<?= $cnb['premium'] ?? "" ?>" name="cnb[premium]" />
                        <input type="hidden" value="<?= $cnb['premium_plus'] ?? "" ?>" name="cnb[premium_plus]" />
                        <input type="hidden" value="<?= $cnb['meal_plan'] ?? "" ?>" name="cnb[meal_plan]" />



                        <input type="hidden" value="<?= $cnb['fortuner'] ?? "" ?>" name="twin[fortuner]" />
                        <input type="hidden" value="<?= $cnb['coach'] ?? ""  ?>" name="cnb[coach]" />
                        <input type="hidden" value="<?= $cnb['tempo'] ?? ""  ?>" name="cnb[tempo]" />
                        <input type="hidden" value="<?= $cnb['cryista'] ?? ""  ?>" name="cnb[cryista]" />
                        <input type="hidden" value="<?= $cnb['innova'] ?? ""  ?>" name="cnb[innova]" />
                        <input type="hidden" value="<?= $cnb['zyalo_ertiga'] ?? ""  ?>" name="cnb[zyalo_ertiga]" />
                        <input type="hidden" value="<?= $cnb['bike'] ?? ""  ?>" name="cnb[bike]" />
                      </tr>
                      <tr>
                        <td class="border-right-dark sticky-col"></td>
                        <td class="border-right-dark sticky-col"></td>
                        <td class="border-right-dark sticky-col">TRIPLE</td>
                        <td class="border-right-dark" id="triple-budget">₹<?= $triple['budget'] ?? ""  ?></td>
                        <td class="border-right-dark" id="triple-standard">₹<?= $triple['standard'] ?? ""  ?></td>
                        <td class="border-right-dark" id="triple-deluxe">₹<?= $triple['deluxe'] ?? ""  ?></td>
                        <td class="border-right-dark" id="triple-super-deluxe">₹<?= $triple['super_deluxe'] ?? ""  ?></td>
                        <td class="border-right-dark" id="triple-luxury">₹<?= $triple['luxury'] ?? "" ?></td>
                        <td class="border-right-dark" id="triple-luxury_plus">₹<?= $triple['luxury_plus'] ?? "" ?></td>
                        <td class="border-right-dark" id="triple-premium">₹<?= $triple['premium'] ?? "" ?></td>
                        <td class="border-right-dark" id="triple-premium_plus">₹<?= $triple['premium_plus'] ?? "" ?></td>




                        <td class="border-right-dark" id="triple-meal_plan"></td>
                        <td class="border-right-dark" id="triple-fortuner"></td>
                        <td class="border-right-dark" id="triple-coach"></td>
                        <td class="border-right-dark" id="triple-tempo"></td>
                        <td class="border-right-dark" id="triple-cryista"></td>
                        <td class="border-right-dark" id="triple-innova"></td>
                        <td class="border-right-dark" id="triple-zyalo_ertiga"></td>
                        <td class="border-right-dark" id="triple-bike"></td>

                        <input type="hidden" value="" name="triple[day]" />
                        <input type="hidden" value="" name="triple[location]" />
                        <input type="hidden" value="TRIPLE Fixed" name="triple[itineary]" />
                        <input type="hidden" value="<?= $triple['budget'] ?? ""  ?>" name="triple[budget]" />
                        <input type="hidden" value="<?= $triple['standard'] ?? ""  ?>" name="triple[standard]" />
                        <input type="hidden" value="<?= $triple['deluxe'] ?? ""  ?>" name="triple[deluxe]" />
                        <input type="hidden" value="<?= $triple['super_deluxe'] ?? ""  ?>" name="triple[super_deluxe]" />
                        <input type="hidden" value="<?= $triple['luxury'] ?? "" ?>" name="triple[luxury]" />
                        <input type="hidden" value="<?= $triple['luxury_plus'] ?? "" ?>" name="triple[luxury_plus]" />
                        <input type="hidden" value="<?= $triple['premium'] ?? "" ?>" name="triple[premium]" />
                        <input type="hidden" value="<?= $triple['premium_plus'] ?? "" ?>" name="triple[premium_plus]" />



                        <input type="hidden" value="<?= $triple['fortuner'] ?? "" ?>" name="twin[fortuner]" />
                        <input type="hidden" value="<?= $triple['coach'] ?? ""  ?>" name="triple[coach]" />
                        <input type="hidden" value="<?= $triple['tempo'] ?? ""  ?>" name="triple[tempo]" />
                        <input type="hidden" value="<?= $triple['cryista'] ?? ""  ?>" name="triple[cryista]" />
                        <input type="hidden" value="<?= $triple['innova'] ?? ""  ?>" name="triple[innova]" />
                        <input type="hidden" value="<?= $triple['zyalo_ertiga'] ?? ""  ?>" name="triple[zyalo_ertiga]" />
                        <input type="hidden" value="<?= $triple['bike'] ?? ""  ?>" name="triple[bike]" />
                      </tr>
                      <tr>
                        <td class="border-right-dark sticky-col"></td>
                        <td class="border-right-dark sticky-col"></td>
                        <td class="border-right-dark sticky-col">SINGLE</td>
                        <td class="border-right-dark" id="single-budget">₹<?= $single['budget'] ?? ""  ?></td>
                        <td class="border-right-dark" id="single-standard">₹<?= $single['standard'] ?? ""  ?></td>
                        <td class="border-right-dark" id="single-deluxe">₹<?= $single['deluxe'] ?? ""  ?></td>
                        <td class="border-right-dark" id="single-super-deluxe">₹<?= $single['super_deluxe'] ?? ""  ?></td>
                        <td class="border-right-dark" id="single-luxury">₹<?= $single['luxury'] ?? "" ?></td>
                        <td class="border-right-dark" id="single-luxury_plus">₹<?= $single['luxury_plus'] ?? "" ?></td>
                        <td class="border-right-dark" id="single-premium">₹<?= $single['premium'] ?? "" ?></td>
                        <td class="border-right-dark" id="single-premium_plus">₹<?= $single['premium_plus'] ?? "" ?></td>



                        <td class="border-right-dark" id="single-meal_plan"></td>
                        <td class="border-right-dark" id="single-fortuner"></td>
                        <td class="border-right-dark" id="single-coach"></td>
                        <td class="border-right-dark" id="single-tempo"></td>
                        <td class="border-right-dark" id="single-cryista"></td>
                        <td class="border-right-dark" id="single-innova"></td>
                        <td class="border-right-dark" id="single-zyalo_ertiga"></td>
                        <td class="border-right-dark" id="single-bike"></td>
                        <input type="hidden" value="" name="single[day]" />
                        <input type="hidden" value="" name="single[location]" />
                        <input type="hidden" value="SINGLE Fixed" name="single[itineary]" />
                        <input type="hidden" value="<?= $single['budget'] ?? ""  ?>" name="single[budget]" />
                        <input type="hidden" value="<?= $single['standard'] ?? ""  ?>" name="single[standard]" />
                        <input type="hidden" value="<?= $single['deluxe'] ?? ""  ?>" name="single[deluxe]" />
                        <input type="hidden" value="<?= $single['super_deluxe'] ?? ""  ?>" name="single[super_deluxe]" />
                        <input type="hidden" value="<?= $single['luxury'] ?? "" ?>" name="single[luxury]" />
                        <input type="hidden" value="<?= $single['luxury_plus'] ?? "" ?>" name="single[luxury_plus]" />
                        <input type="hidden" value="<?= $single['premium'] ?? "" ?>" name="single[premium]" />
                        <input type="hidden" value="<?= $single['premium_plus'] ?? "" ?>" name="single[premium_plus]" />
                        <input type="hidden" value="<?= $single['meal_plan'] ?? "" ?>" name="single[meal_plan]" />

                        <input type="hidden" value="<?= $single['fortuner'] ?? "" ?>" name="twin[fortuner]" />
                        <input type="hidden" value="<?= $single['coach'] ?? ""  ?>" name="single[coach]" />
                        <input type="hidden" value="<?= $single['tempo'] ?? ""  ?>" name="single[tempo]" />
                        <input type="hidden" value="<?= $single['cryista'] ?? ""  ?>" name="single[cryista]" />
                        <input type="hidden" value="<?= $single['innova'] ?? ""  ?>" name="single[innova]" />
                        <input type="hidden" value="<?= $single['zyalo_ertiga'] ?? ""  ?>" name="single[zyalo_ertiga]" />
                        <input type="hidden" value="<?= $single['bike'] ?? ""  ?>" name="single[bike]" />
                      </tr>

                      <tr>
                        <td class="border-right-dark sticky-col"></td>
                        <td class="border-right-dark sticky-col"></td>
                        <td class="border-right-dark sticky-col">QUAD SHARING</td>
                        <td class="border-right-dark" id="quad_sharing-budget">₹<?= $quad_sharing['budget'] ?? ""  ?></td>
                        <td class="border-right-dark" id="quad_sharing-standard">₹<?= $quad_sharing['standard'] ?? ""  ?></td>
                        <td class="border-right-dark" id="quad_sharing-deluxe">₹<?= $quad_sharing['deluxe'] ?? ""  ?></td>
                        <td class="border-right-dark" id="quad_sharing-super-deluxe">₹<?= $quad_sharing['super_deluxe'] ?? ""  ?></td>
                        <td class="border-right-dark" id="quad_sharing-luxury">₹<?= $quad_sharing['luxury'] ?? "" ?></td>
                        <td class="border-right-dark" id="quad_sharing-luxury_plus">₹<?= $quad_sharing['luxury_plus'] ?? "" ?></td>
                        <td class="border-right-dark" id="quad_sharing-premium">₹<?= $quad_sharing['premium'] ?? "" ?></td>
                        <td class="border-right-dark" id="quad_sharing-premium_plus">₹<?= $quad_sharing['premium_plus'] ?? "" ?></td>


                        <td class="border-right-dark" id="quad_sharing-meal_plan"></td>
                        <td class="border-right-dark" id="quad_sharing-fortuner"></td>
                        <td class="border-right-dark" id="quad_sharing-coach"></td>
                        <td class="border-right-dark" id="quad_sharing-tempo"></td>
                        <td class="border-right-dark" id="quad_sharing-cryista"></td>
                        <td class="border-right-dark" id="quad_sharing-innova"></td>
                        <td class="border-right-dark" id="quad_sharing-zyalo_ertiga"></td>
                        <td class="border-right-dark" id="quad_sharing-bike"></td>
                        <input type="hidden" value="" name="quad_sharing[day]" />
                        <input type="hidden" value="" name="quad_sharing[location]" />
                        <input type="hidden" value="QUAD SHARING Fixed" name="quad_sharing[itineary]" />
                        <input type="hidden" value="<?= $quad_sharing['budget'] ?? ""  ?>" name="quad_sharing[budget]" />
                        <input type="hidden" value="<?= $quad_sharing['standard'] ?? ""  ?>" name="quad_sharing[standard]" />
                        <input type="hidden" value="<?= $quad_sharing['deluxe'] ?? ""  ?>" name="quad_sharing[deluxe]" />
                        <input type="hidden" value="<?= $quad_sharing['super_deluxe'] ?? ""  ?>" name="quad_sharing[super_deluxe]" />
                        <input type="hidden" value="<?= $quad_sharing['luxury'] ?? "" ?>" name="quad_sharing[luxury]" />
                        <input type="hidden" value="<?= $quad_sharing['luxury_plus'] ?? "" ?>" name="quad_sharing[luxury_plus]" />
                        <input type="hidden" value="<?= $quad_sharing['premium'] ?? "" ?>" name="quad_sharing[premium]" />
                        <input type="hidden" value="<?= $quad_sharing['premium_plus'] ?? "" ?>" name="quad_sharing[premium_plus]" />
                        <input type="hidden" value="<?= $quad_sharing['meal_plan'] ?? "" ?>" name="quad_sharing[meal_plan]" />



                        <input type="hidden" value="<?= $quad_sharing['fortuner'] ?? ""  ?>" name="quad_sharing[fortuner]" />
                        <input type="hidden" value="<?= $quad_sharing['coach'] ?? ""  ?>" name="quad_sharing[coach]" />
                        <input type="hidden" value="<?= $quad_sharing['tempo'] ?? ""  ?>" name="quad_sharing[tempo]" />
                        <input type="hidden" value="<?= $quad_sharing['cryista'] ?? ""  ?>" name="quad_sharing[cryista]" />
                        <input type="hidden" value="<?= $quad_sharing['innova'] ?? ""  ?>" name="quad_sharing[innova]" />
                        <input type="hidden" value="<?= $quad_sharing['zyalo_ertiga'] ?? ""  ?>" name="quad_sharing[zyalo_ertiga]" />
                        <input type="hidden" value="<?= $quad_sharing['bike'] ?? ""  ?>" name="quad_sharing[bike]" />
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
  document.getElementById('flexSwitchCheckChecked').addEventListener('change', function() {
    if (this.checked) {
      this.classList.remove('inactive');
      this.classList.add('active');
    } else {
      this.classList.remove('active');
      this.classList.add('inactive');
    }
  });
  document.getElementById('duration').addEventListener('change', function() {
    const pattern = /(\d+)\s*Nights?\s*(\d+)\s*Days?/i;
    const matches = this.value.match(pattern);
    console.log(matches);
    numberOfRows = parseInt(matches[2], 10);
    //  console.log(this.value.match(/\d+/))
    // var numberOfRows = parseInt(this.value.match(/\d+/)[1]);
    //var numberOfRows = parseInt(this.value);
    var existingTable = document.getElementById('existingTable');

    // Remove rows with the specified class
    var rowsToRemove = existingTable.getElementsByClassName('new-row');
    while (rowsToRemove.length > 0) {
      rowsToRemove[0].parentNode.removeChild(rowsToRemove[0]);
    }

    var insertAfterRow = '';
    var dayval = numberOfRows;
    for (var j = 0; j < numberOfRows; j++) {
      var row = existingTable.insertRow(insertAfterRow.rowIndex + j);
      row.classList.add('new-row');
      row.innerHTML = `
     <td class="border-right-dark sticky-col"><input type="number" class="form-control phone-mask w-px-75" name="detail[${j}][day]" value="${dayval--}" placeholder="Day" required/></td>
     <td class="border-right-dark sticky-col">
      <select class="form-select"  name="detail[${j}][location]" >
        <?= $locationOptions ?>
      </select>
    </td>
		<td class="border-right-dark sticky-col"><textarea class="form-control w-px-300 h-px-75" name="detail[${j}][itineary]" placeholder="Enter Short Itineary" ></textarea></td>
		<td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][budget]" onchange="calculateBudgetSum()" placeholder="" /></td>
		<td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][standard]" onchange="calculateStandardSum()" placeholder="" /></td>
		<td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][deluxe]" onchange="calculateDeluxeSum()"  placeholder="" /></td>
		<td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][super_deluxe]" onchange="calculateSuperDeluxeSum()"  placeholder="" /></td>
    <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][luxury]" onchange="calculateLuxurySum()"  placeholder="" /></td>
    <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][luxury_plus]" onchange="calculateLuxuryPlusSum()"  placeholder="" /></td>
		<td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][premium]" onchange="calculatePremiumSum()"  placeholder="" /></td>
    <td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][premium_plus]" onchange="calculatePremiumPlusSum()"  placeholder=""/></td>

		<td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][meal_plan]" /></td>

		<td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][fortuner]" /></td>
		<td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][coach]" /></td>
		<td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][tempo]" /></td>
		<td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][cryista]" /></td>
		<td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][innova]"  /></td>
		<td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][zyalo_ertiga]" /></td>
		<td class="border-right-dark"><input type="number" class="form-control phone-mask w-px-100" name="detail[${j}][bike]"  /></td>
    `;
    }
  });


  // Function to calculate sum
  function calculateBudgetSum() {
    var budgetInputs = document.querySelectorAll('input[name^="detail"][name$="[budget]"]');
    var budgetSum = 0;
    budgetInputs.forEach(function(input) {
      if (input.value !== '') {
        budgetSum += parseInt(input.value);
      }
    });
    document.getElementById('twin-budget').innerHTML = `₹${Math.round(budgetSum)}`;
    document.getElementById('cwb-budget').innerHTML = `₹${Math.round(budgetSum * 0.4)}`;
    document.getElementById('cnb-budget').innerHTML = `₹${Math.round(budgetSum * 0.25)}`;
    document.getElementById('triple-budget').innerHTML = `₹${Math.round((budgetSum * 0.4) + budgetSum)}`;
    document.getElementById('single-budget').innerHTML = `₹${Math.round(budgetSum * 0.75)}`;
    document.getElementById('quad_sharing-budget').innerHTML = `₹${Math.round((budgetSum * 0.75)  + budgetSum)}`;

    document.querySelector('input[name="twin[budget]"]').value = Math.round(budgetSum);
    document.querySelector('input[name="cwb[budget]"]').value = Math.round(budgetSum * 0.4);
    document.querySelector('input[name="cnb[budget]"]').value = Math.round(budgetSum * 0.25);
    document.querySelector('input[name="triple[budget]"]').value = Math.round((budgetSum * 0.4) + budgetSum);
    document.querySelector('input[name="single[budget]"]').value = Math.round(budgetSum * 0.75);
    document.querySelector('input[name="quad_sharing[budget]"]').value = Math.round((budgetSum * 0.75) + budgetSum);
    return budgetSum;
  }

  function calculateStandardSum() {
    var standardInputs = document.querySelectorAll('input[name^="detail"][name$="[standard]"]');
    var standardSum = 0;
    standardInputs.forEach(function(input) {
      if (input.value !== '') {
        standardSum += parseInt(input.value);
      }
    });
    document.getElementById('twin-standard').innerHTML = `₹${Math.round(standardSum)}`;
    document.getElementById('cwb-standard').innerHTML = `₹${Math.round(standardSum * 0.4)}`;
    document.getElementById('cnb-standard').innerHTML = `₹${Math.round(standardSum * 0.25)}`;
    document.getElementById('triple-standard').innerHTML = `₹${Math.round((standardSum * 0.4) + standardSum)}`;
    document.getElementById('single-standard').innerHTML = `₹${Math.round(standardSum * 0.75)}`;
    document.getElementById('quad_sharing-standard').innerHTML = `₹${Math.round((standardSum * 0.75)  + standardSum)}`;

    document.querySelector('input[name="twin[standard]"]').value = Math.round(standardSum);
    document.querySelector('input[name="cwb[standard]"]').value = Math.round(standardSum * 0.4);
    document.querySelector('input[name="cnb[standard]"]').value = Math.round(standardSum * 0.25);
    document.querySelector('input[name="triple[standard]"]').value = Math.round((standardSum * 0.4) + standardSum);
    document.querySelector('input[name="single[standard]"]').value = Math.round(standardSum * 0.75);
    document.querySelector('input[name="quad_sharing[standard]"]').value = Math.round((standardSum * 0.75) + standardSum);
    return standardSum;
  }

  function calculateDeluxeSum() {
    var deluxeInputs = document.querySelectorAll('input[name^="detail"][name$="[deluxe]"]');
    var deluxeSum = 0;
    deluxeInputs.forEach(function(input) {
      if (input.value !== '') {
        deluxeSum += parseInt(input.value);
      }
    });
    document.getElementById('twin-deluxe').innerHTML = `₹${Math.round(deluxeSum)}`;
    document.getElementById('cwb-deluxe').innerHTML = `₹${Math.round(deluxeSum * 0.4)}`;
    document.getElementById('cnb-deluxe').innerHTML = `₹${Math.round(deluxeSum * 0.25)}`;
    document.getElementById('triple-deluxe').innerHTML = `₹${Math.round((deluxeSum * 0.4) + deluxeSum)}`;
    document.getElementById('single-deluxe').innerHTML = `₹${Math.round(deluxeSum * 0.75)}`;
    document.getElementById('quad_sharing-deluxe').innerHTML = `₹${Math.round((deluxeSum * 0.75)  + deluxeSum)}`;

    document.querySelector('input[name="twin[deluxe]"]').value = Math.round(deluxeSum);
    document.querySelector('input[name="cwb[deluxe]"]').value = Math.round(deluxeSum * 0.4);
    document.querySelector('input[name="cnb[deluxe]"]').value = Math.round(deluxeSum * 0.25);
    document.querySelector('input[name="triple[deluxe]"]').value = Math.round((deluxeSum * 0.4) + deluxeSum);
    document.querySelector('input[name="single[deluxe]"]').value = Math.round(deluxeSum * 0.75);
    document.querySelector('input[name="quad_sharing[deluxe]"]').value = Math.round((deluxeSum * 0.75) + deluxeSum);
    return deluxeSum;
  }

  function calculateSuperDeluxeSum() {
    var super_deluxeInputs = document.querySelectorAll('input[name^="detail"][name$="[super_deluxe]"]');
    var super_deluxeSum = 0;
    super_deluxeInputs.forEach(function(input) {
      if (input.value !== '') {
        super_deluxeSum += parseInt(input.value);
      }
    });
    document.getElementById('twin-super-deluxe').innerHTML = `₹${Math.round(super_deluxeSum)}`;
    document.getElementById('cwb-super-deluxe').innerHTML = `₹${Math.round(super_deluxeSum * 0.4)}`;
    document.getElementById('cnb-super-deluxe').innerHTML = `₹${Math.round(super_deluxeSum * 0.25)}`;
    document.getElementById('triple-super-deluxe').innerHTML = `₹${Math.round((super_deluxeSum * 0.4) + super_deluxeSum)}`;
    document.getElementById('single-super-deluxe').innerHTML = `₹${Math.round(super_deluxeSum * 0.75)}`;
    document.getElementById('quad_sharing-super-deluxe').innerHTML = `₹${Math.round((super_deluxeSum * 0.75)  + super_deluxeSum)}`;

    document.querySelector('input[name="twin[super_deluxe]"]').value = Math.round(super_deluxeSum);
    document.querySelector('input[name="cwb[super_deluxe]"]').value = Math.round(super_deluxeSum * 0.4);
    document.querySelector('input[name="cnb[super_deluxe]"]').value = Math.round(super_deluxeSum * 0.25);
    document.querySelector('input[name="triple[super_deluxe]"]').value = Math.round((super_deluxeSum * 0.4) + super_deluxeSum);
    document.querySelector('input[name="single[super_deluxe]"]').value = Math.round(super_deluxeSum * 0.75);
    document.querySelector('input[name="quad_sharing[super_deluxe]"]').value = Math.round((super_deluxeSum * 0.75) + super_deluxeSum);
    return super_deluxeSum;
  }


  function calculateLuxurySum() {
    var inputs = document.querySelectorAll('input[name^="detail"][name$="[luxury]"]');
    var sum = 0;
    inputs.forEach(function(input) {
      if (input.value !== '') {
        sum += parseInt(input.value);
      }
    });
    document.getElementById('twin-luxury').innerHTML = `₹${Math.round(sum)}`;
    document.getElementById('cwb-luxury').innerHTML = `₹${Math.round(sum * 0.4)}`;
    document.getElementById('cnb-luxury').innerHTML = `₹${Math.round(sum * 0.25)}`;
    document.getElementById('triple-luxury').innerHTML = `₹${Math.round((sum * 0.4) + sum)}`;
    document.getElementById('single-luxury').innerHTML = `₹${Math.round(sum * 0.75)}`;
    document.getElementById('quad_sharing-luxury').innerHTML = `₹${Math.round((sum * 0.75)  + sum)}`;

    document.querySelector('input[name="twin[luxury]"]').value = Math.round(sum);
    document.querySelector('input[name="cwb[luxury]"]').value = Math.round(sum * 0.4);
    document.querySelector('input[name="cnb[luxury]"]').value = Math.round(sum * 0.25);
    document.querySelector('input[name="triple[luxury]"]').value = Math.round((sum * 0.4) + sum);
    document.querySelector('input[name="single[luxury]"]').value = Math.round(sum * 0.75);
    document.querySelector('input[name="quad_sharing[luxury]"]').value = Math.round((sum * 0.75) + sum);
    return sum;
  }

  function calculateLuxuryPlusSum() {
    var inputs = document.querySelectorAll('input[name^="detail"][name$="[luxury_plus]"]');
    var sum = 0;
    inputs.forEach(function(input) {
      if (input.value !== '') {
        sum += parseInt(input.value);
      }
    });
    document.getElementById('twin-luxury_plus').innerHTML = `₹${Math.round(sum)}`;
    document.getElementById('cwb-luxury_plus').innerHTML = `₹${Math.round(sum * 0.4)}`;
    document.getElementById('cnb-luxury_plus').innerHTML = `₹${Math.round(sum * 0.25)}`;
    document.getElementById('triple-luxury_plus').innerHTML = `₹${Math.round((sum * 0.4) + sum)}`;
    document.getElementById('single-luxury_plus').innerHTML = `₹${Math.round(sum * 0.75)}`;
    document.getElementById('quad_sharing-luxury_plus').innerHTML = `₹${Math.round((sum * 0.75)  + sum)}`;

    document.querySelector('input[name="twin[luxury_plus]"]').value = Math.round(sum);
    document.querySelector('input[name="cwb[luxury_plus]"]').value = Math.round(sum * 0.4);
    document.querySelector('input[name="cnb[luxury_plus]"]').value = Math.round(sum * 0.25);
    document.querySelector('input[name="triple[luxury_plus]"]').value = Math.round((sum * 0.4) + sum);
    document.querySelector('input[name="single[luxury_plus]"]').value = Math.round(sum * 0.75);
    document.querySelector('input[name="quad_sharing[luxury_plus]"]').value = Math.round((sum * 0.75) + sum);
    return sum;
  }

  function calculatePremiumSum() {
    var premiumInputs = document.querySelectorAll('input[name^="detail"][name$="[premium]"]');
    var premiumSum = 0;
    premiumInputs.forEach(function(input) {
      if (input.value !== '') {
        premiumSum += parseInt(input.value);
      }
    });
    document.getElementById('twin-premium').innerHTML = `₹${Math.round(premiumSum)}`;
    document.getElementById('cwb-premium').innerHTML = `₹${Math.round(premiumSum * 0.4)}`;
    document.getElementById('cnb-premium').innerHTML = `₹${Math.round(premiumSum * 0.25)}`;
    document.getElementById('triple-premium').innerHTML = `₹${Math.round((premiumSum * 0.4) + premiumSum)}`;
    document.getElementById('single-premium').innerHTML = `₹${Math.round(premiumSum * 0.75)}`;
    document.getElementById('quad_sharing-premium').innerHTML = `₹${Math.round((premiumSum * 0.75)  + premiumSum)}`;

    document.querySelector('input[name="twin[premium]"]').value = Math.round(premiumSum);
    document.querySelector('input[name="cwb[premium]"]').value = Math.round(premiumSum * 0.4);
    document.querySelector('input[name="cnb[premium]"]').value = Math.round(premiumSum * 0.25);
    document.querySelector('input[name="triple[premium]"]').value = Math.round((premiumSum * 0.4) + premiumSum);
    document.querySelector('input[name="single[premium]"]').value = Math.round(premiumSum * 0.75);
    document.querySelector('input[name="quad_sharing[premium]"]').value = Math.round((premiumSum * 0.75) + premiumSum);
    return premiumSum;
  }

  function calculatePremiumPlusSum() {
    var inputs = document.querySelectorAll('input[name^="detail"][name$="[premium_plus]"]');
    var sum = 0;
    inputs.forEach(function(input) {
      if (input.value !== '') {
        sum += parseInt(input.value);
      }
    });
    document.getElementById('twin-premium_plus').innerHTML = `₹${Math.round(sum)}`;
    document.getElementById('cwb-premium_plus').innerHTML = `₹${Math.round(sum * 0.4)}`;
    document.getElementById('cnb-premium_plus').innerHTML = `₹${Math.round(sum * 0.25)}`;
    document.getElementById('triple-premium_plus').innerHTML = `₹${Math.round((sum * 0.4) + sum)}`;
    document.getElementById('single-premium_plus').innerHTML = `₹${Math.round(sum * 0.75)}`;
    document.getElementById('quad_sharing-premium_plus').innerHTML = `₹${Math.round((sum * 0.75)  + sum)}`;

    document.querySelector('input[name="twin[premium_plus]"]').value = Math.round(sum);
    document.querySelector('input[name="cwb[premium_plus]"]').value = Math.round(sum * 0.4);
    document.querySelector('input[name="cnb[premium_plus]"]').value = Math.round(sum * 0.25);
    document.querySelector('input[name="triple[premium_plus]"]').value = Math.round((sum * 0.4) + sum);
    document.querySelector('input[name="single[premium_plus]"]').value = Math.round(sum * 0.75);
    document.querySelector('input[name="quad_sharing[premium_plus]"]').value = Math.round((sum * 0.75) + sum);
    return sum;
  }
</script>
<?php include BASE_PATH . '/includes/footer.php'; ?>