<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';


$db = getDbInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_to_store = array_filter($_POST);
    $data_to_store['description'] = isset($_POST['description']) ? explode('Powered by', $_POST['description'])[0] : '';

    $name = trim(strtolower($data_to_store['name'] ?? ''));
    $passenger = $data_to_store['passenger'] ?? '';
    $bag = $data_to_store['bag'] ?? '';

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $msg = "edited";
        $db->where('id', $_POST['id']);
        $blog_id = $db->update('carlist', $data_to_store);
    } else {
        $msg = "added";
        $car_id = $db->insert('carlist', $data_to_store);
        $columnName = strtolower(str_replace(' ', '_', $name));
        if ($car_id) {
            if (preg_match('/^[a-z0-9_]+$/', $columnName)) {
                $checkColumnSQL = "SHOW COLUMNS FROM `taxi_details` LIKE '$columnName'";
                $columnExists = $db->rawQueryOne($checkColumnSQL);
                if (!$columnExists) {
                    $addColumnSQL = "ALTER TABLE `taxi_details` ADD COLUMN `$columnName` INT(11) DEFAULT NULL";
                    $db->rawQuery($addColumnSQL);
                }
            }
        }
    }
    function uploadImages($file, $target_dir)
    {
        if ($file && !empty($file['name'])) {
            $tmp_name = $file['tmp_name'];
            $size = $file['size'];
            $error = $file['error'];

            if ($error === UPLOAD_ERR_OK && $size <= 10000000) {
                $image_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array(strtolower($image_extension), $allowed_types)) {
                    $target_file = $target_dir . time() . "_" . basename($file['name']);

                    if (move_uploaded_file($tmp_name, $target_file)) {
                        return $target_file;
                    }
                } else {
                    die("Invalid image format. Allowed: JPG, JPEG, PNG, GIF");
                }
            }
        }
        return null;
    }
    $carId = !empty($_POST['id']) ? $_POST['id'] : $car_id;
    if ($carId) {
        $target_dir = "uploads/car/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        // Process main image
        $image_file = $_FILES['image'] ?? null;
        $image_path = uploadImages($image_file, $target_dir);

        // Process cover image
        $cover_image_file = $_FILES['cover_image'] ?? null;
        $cover_image_path = uploadImages($cover_image_file, $target_dir);

        if ($image_path || $cover_image_path) {
            $db->where('id', $carId);
            $oldImages = $db->getOne('carlist', ['image', 'cover_image']);

            // Delete old images if new ones are uploaded
            if ($image_path && $oldImages['image'] && file_exists($oldImages['image'])) {
                unlink($oldImages['image']);
            }
            if ($cover_image_path && $oldImages['cover_image'] && file_exists($oldImages['cover_image'])) {
                unlink($oldImages['cover_image']);
            }

            // Update database
            $updateData = [];
            if ($image_path) {
                $updateData['image'] = $image_path;
            }
            if ($cover_image_path) {
                $updateData['cover_image'] = $cover_image_path;
            }

            $db->where('id', $carId);
            $updated = $db->update('carlist', $updateData);

            if (!$updated) {
                die("Image update failed: " . $db->getLastError());
            }
        }
        $_SESSION['success'] = "Car $msg successfully!";
        header('location: view-car.php');
        exit();
    } else {
        echo 'Insert failed: ' . $db->getLastError();
        exit();
    }
}
$id = isset($_GET['id']) && !empty($_GET['id']) ? decryptId($_GET['id']) : "";
$edit = false;
if (!empty($id)) {
    $edit = true;
    $db->where('id', $id);
    $data = $db->getOne("carlist");
}



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
</style>
<div class="layout-page">


    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Car/</span> <?= $edit ? 'Edit' : "Add" ?> Car</h4>
            <h4 class="py-3 mb-4">Add Car</h4>
            <!-- Basic Layout -->
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="" method="post" id="hotel_form" enctype="multipart/form-data">


                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-company">Car Name</label>
                                    <input type="text" class="form-control" name="name" value="<?php echo xss_clean($edit ? $data['name'] : ''); ?>" required />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-company">Car Description</label>
                                    <textarea rows="10" class="form-control" name="description"><?php echo isset($data['description']) ? $data['description'] : ''; ?>      </textarea>

                                </div>
                                <div class="row mb-3">
                                    <div class="col-md">
                                        <label class="form-label">Upload Image</label>
                                        <input type="file" name="image" class="form-control" accept="image/*" />

                                        <?php if ($edit && isset($data)): ?>
                                            <div class="mt-3">
                                                <img src="<?= $data['image']; ?>" height="100px" width="100px" style="display: block;" />
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md">
                                        <label class="form-label">Upload Cover Image <span>(formate 233*233)</span></label>
                                        <input type="file" name="cover_image" class="form-control" accept="image/*" />

                                        <?php if ($edit && isset($data)): ?>
                                            <div class="mt-3">
                                                <img src="<?= $data['cover_image']; ?>" height="100px" width="100px" style="display: block;" />
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md">
                                        <label class="form-label" for="basic-default-phone">Passenger</label>
                                        <input type="text" id="basic-default-phone" class="form-control phone-mask" name="passenger" value="<?php echo xss_clean($edit ? $data['passenger'] : ''); ?>" required />
                                    </div>
                                    <div class="col-md">
                                        <label class="form-label" for="basic-default-phone">Bag</label>
                                        <input type="text" id="basic-default-phone" class="form-control phone-mask" name="bag" value="<?php echo xss_clean($edit ? $data['bag'] : ''); ?>" required />
                                    </div>
                                </div>


                                <!-- End Overview Section -->

                                <input type="hidden" name="id" value="<?php echo $id ?>" />
                                <button type="submit" class="btn btn-primary">SAVE</button>
                            </form>
                        </div>
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
</script>
<?php include BASE_PATH . '/includes/footer.php'; ?>