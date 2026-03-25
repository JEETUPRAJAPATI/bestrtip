<?php
session_start();
require_once '../config/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$edit = true;
$db = getDbInstance();
$db->where('id', $_SESSION['user_id']);
$data = $db->getOne("vehicles");


?>
<link rel="stylesheet" href="../assets/vendor/css/core.css" />
<link rel="stylesheet" href="../assets/css/front.min.css" />
<link rel="stylesheet" href="../assets/vendor/css/theme-default.css" />


<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
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
                                            <img src="?php echo htmlspecialchars($image); ?>" class="img-thumbnail me-2" width="100">
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
<script>
    $(document).ready(function() {
        $("#carFacilities").select2({
            placeholder: "Choose Car Facilities...",
            allowClear: true
        });
    });
</script>

<?php include BASE_PATH . '../includes/agent_footer.php'; ?>