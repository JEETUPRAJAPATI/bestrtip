<?php
session_start();
require_once './config/config.php';
require_once './includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // $data_to_store = array_filter($_POST);
  $db = getDbInstance();
  // echo "<pre>";
  // print_r($data_to_store);
  // die();

  $driver_name = $_POST['driver_name'] ?? '';
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

  $isUpdate = isset($_POST['id']) && !empty($_POST['id']);

  if ($isUpdate) {
    // Update existing vehicle
    $db->where('id', $_POST['id']);
    $last_id = $db->update('vehicles', $data_to_store);
    $msg = "edited";
  } else {
    // Insert new vehicle
    $last_id = $db->insert('vehicles', $data_to_store);
    $msg = "added";
  }

  if ($last_id) {
    // Handle image uploads
    $target_dir = "../uploads/vehicles/";

    // Ensure the directory exists
    if (!is_dir($target_dir)) {
      mkdir($target_dir, 0777, true);
    }

    $exteriorImages = [];
    $interiorImages = [];

    // Function to process uploaded images
    function processImages($files, $target_dir)
    {
      $imagePaths = [];
      if (!empty($files['name'][0])) {
        foreach ($files['name'] as $key => $name) {
          $tmp_name = $files['tmp_name'][$key];
          $size = $files['size'][$key];
          $error = $files['error'][$key];

          if ($error === UPLOAD_ERR_OK && $size <= 5000000) { // Limit: 5MB per image
            $filename = time() . "_" . basename($name);
            $target_file = $target_dir . $filename;
            if (move_uploaded_file($tmp_name, $target_file)) {
              $imagePaths[] = $target_file;
            }
          }
        }
      }
      return $imagePaths;
    }

    // Process exterior and interior images
    if (isset($_FILES['exteriorImages'])) {
      $exteriorImages = processImages($_FILES['exteriorImages'], $target_dir);
    }
    if (isset($_FILES['interiorImages'])) {
      $interiorImages = processImages($_FILES['interiorImages'], $target_dir);
    }

    // Convert image paths to comma-separated strings
    $updateData = [
      'exterior_images' => !empty($exteriorImages) ? implode(',', $exteriorImages) : '',
      'interior_images' => !empty($interiorImages) ? implode(',', $interiorImages) : ''
    ];

    // Update the vehicle record with image paths only if there are new images
    if (!empty($updateData['exterior_images']) || !empty($updateData['interior_images'])) {
      $db->where('id', $last_id);
      $db->update('vehicles', $updateData);
    }

    $_SESSION['success'] = "Vehicle $msg successfully!";
    header('location: vehicle.php');
    exit();
  } else {
    echo 'Insert/Update failed: ' . $db->getLastError();
    exit();
  }
}

$id = isset($_GET['crm']) && !empty($_GET['crm']) ? decryptId($_GET['crm']) : "";
$edit = false;
if (!empty($id)) {
  $edit = true;
  $db = getDbInstance();
  $db->where('id', $id);
  $data = $db->getOne("vehicles");
}

$transportation = setTransportation();

require_once 'includes/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

<style>
  .image-section img {
    width: 100%;
    height: 100%;
    border-radius: 10px 0 0 10px;
  }

  .preview-img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 5px;
    margin-right: 5px;
  }
</style>
<!-- Layout container -->
<div class="layout-page">

  <!-- Content wrapper -->
  <div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="py-3 mb-4"><?= $edit ? 'Edit' : "Add" ?> Vehicle Details</h4>
      <!-- Basic Layout -->
      <div class="row">
        <div class="col-xl">
          <div class="card mb-4">
            <div class="card-body">
              <form action="" method="post" id="customer_form" enctype="multipart/form-data">
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-company">Driver Name</label>
                    <input type="text" class="form-control" name="driver_name" value="<?php echo xss_clean($edit ? $data['driver_name'] : ''); ?>" required />
                  </div>

                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Vehicle Type</label>
                    <select name="vehicle_type" class="form-select" id="inputGroupSelect01" required>
                      <option value="">Select Vehicle</option>
                      <?php
                      foreach ($transportation as $key => $transport) {
                        $selected = ($key == $data['vehicle_type']) ? 'selected' : "";
                        echo "<option value=\"$key\" $selected>$key</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Vehicle Number</label>
                    <input type="text" class="form-control phone-mask" name="vehicle_number" value="<?php echo xss_clean($edit ? $data['vehicle_number'] : ''); ?>" required />
                  </div>
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Mobile No.</label>
                    <input type="number" class="form-control phone-mask" name="mobile" value="<?php echo xss_clean($edit ? $data['mobile'] : ''); ?>" required />
                  </div>
                  <input type="hidden" name="id" value="<?php echo $id ?>" />
                </div>

                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Email</label>
                    <input type="email" class="form-control mb-2" name="email" value="<?php echo xss_clean($edit ? $data['email'] : ''); ?>" placeholder="Email ID">
                  </div>
                  <div class="col-md">

                    <label class="form-label" for="basic-default-phone">Age</label>
                    <input type="number" class="form-control mb-2" name="age" value="<?php echo xss_clean($edit ? $data['age'] : ''); ?>" placeholder="Age">
                  </div>

                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Experience</label>
                    <input type="text" class="form-control mb-2" name="experience" value="<?php echo xss_clean($edit ? $data['experience'] : ''); ?>" placeholder="Experience">
                  </div>
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Facilities</label>
                    <?php
                    $selectedFacilities = $edit ? explode(',', $data['facilities'] ?? '') : [];
                    ?>
                    <select id="carFacilities" class="form-select" name="facilities[]" multiple>
                      <option value="tick" <?php echo in_array('tick', $selectedFacilities) ? 'selected' : ''; ?>>Ticks</option>
                      <option value="oxygen" <?php echo in_array('oxygen', $selectedFacilities) ? 'selected' : ''; ?>>Oxygen</option>
                      <option value="water" <?php echo in_array('water', $selectedFacilities) ? 'selected' : ''; ?>>Water Bottle</option>
                      <option value="first_aid" <?php echo in_array('first_aid', $selectedFacilities) ? 'selected' : ''; ?>>First Aid Kit</option>
                      <option value="snacks" <?php echo in_array('snacks', $selectedFacilities) ? 'selected' : ''; ?>>Snacks</option>
                    </select>
                  </div>
                </div>
                <?php
                $exteriorImages = $edit && !empty($data['exterior_images']) ? explode(',', $data['exterior_images']) : [];
                $interiorImages = $edit && !empty($data['interior_images']) ? explode(',', $data['interior_images']) : [];
                ?>
                <div class="mb-3">
                  <label class="mt-2">Exterior Picture (4)</label>
                  <input type="file" class="form-control mb-2" multiple id="exteriorImages" name="exteriorImages[]" accept="image/*">
                  <div id="exteriorPreview" class="d-flex">
                    <?php foreach ($exteriorImages as $image): ?>
                      <img src="<?php echo htmlspecialchars($image); ?>" class="img-thumbnail me-2" width="100">
                    <?php endforeach; ?>
                  </div>

                  <label class="mt-2">Interior Picture (5)</label>
                  <input type="file" class="form-control mb-2" multiple id="interiorImages" name="interiorImages[]" accept="image/*">
                  <div id="interiorPreview" class="d-flex">
                    <?php foreach ($interiorImages as $image): ?>
                      <img src="<?php echo htmlspecialchars($image); ?>" class="img-thumbnail me-2" width="100">
                    <?php endforeach; ?>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary">Save</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- / Content -->
  </div>
</div>
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
<?php include BASE_PATH . '/includes/footer.php'; ?>