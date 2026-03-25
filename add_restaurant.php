<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$pid = decryptId($_GET['pid'] ?? '');
$db = getDbInstance();

// Hotel info
$db->where('id', $pid);
$hotel = $db->getOne('properties');

$isEdit = false;
$restaurant = [];
$restaurant_id = '';

// Check if we're editing an existing restaurant
if (isset($_GET['id'])) {
    $isEdit = true;
    $restaurant_id = decryptId($_GET['id']);

    $db->where('id', $restaurant_id);
    $restaurant = $db->getOne('restaurants');

    if (!$restaurant) {
        $_SESSION['failure'] = 'Restaurant not found';
        header('Location: list_restaurant.php?pid=' . encryptId($pid));
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = array(
        'property_id' => $pid,
        'name' => $_POST['name'],
        'manager_name' => $_POST['manager_name'],
        'chef_name' => $_POST['chef_name'],
        'contact_number' => $_POST['contact_number'],
        'updated_at' => date('Y-m-d H:i:s')
    );

    // Handle logo upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = BASE_PATH . '/uploads/restaurants/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileExt = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $fileName = 'logo_' . time() . '.' . $fileExt;
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadPath)) {
            $data['logo'] = '/uploads/restaurants/' . $fileName;

            // Delete old logo if it exists
            if ($isEdit && !empty($restaurant['logo'])) {
                $oldLogoPath = BASE_PATH . $restaurant['logo'];
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }
        }
    }

    if ($isEdit) {
        $db->where('id', $restaurant_id);
        $result = $db->update('restaurants', $data);

        if ($result) {
            $_SESSION['success'] = 'Restaurant updated successfully!';
        } else {
            $_SESSION['failure'] = 'Failed to update restaurant: ' . $db->getLastError();
        }
    } else {
        $data['created_at'] = date('Y-m-d H:i:s');
        $last_id = $db->insert('restaurants', $data);

        if ($last_id) {
            $_SESSION['success'] = 'Restaurant added successfully!';
        } else {
            $_SESSION['failure'] = 'Failed to add restaurant: ' . $db->getLastError();
        }
    }

    header('Location: list_restaurant.php?pid=' . encryptId($pid));
    exit();
}

include BASE_PATH . '/includes/header.php';
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col">
            <h4 class="py-3 mb-4"><?= $isEdit ? 'Edit' : 'Add New' ?> Restaurant</h4>
        </div>
        <div class="col-auto">
            <a href="list_restaurant.php?pid=<?= encryptId($pid) ?>" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?= $_SESSION['success'];
            unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['failure'])): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <?= $_SESSION['failure'];
            unset($_SESSION['failure']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Restaurant Name *</label>
                        <input type="text" name="name" id="name" class="form-control"
                            value="<?= htmlspecialchars($restaurant['name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="logo" class="form-label">Restaurant Logo</label>
                        <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                        <small class="text-muted">Recommended size: 300x300 pixels</small>
                        <?php if ($isEdit && !empty($restaurant['logo'])): ?>
                            <div class="mt-2">
                                <img src="<?= htmlspecialchars($restaurant['logo']) ?>" alt="Current Logo" style="max-height: 100px;">
                                <input type="hidden" name="existing_logo" value="<?= htmlspecialchars($restaurant['logo']) ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="manager_name" class="form-label">Manager Name</label>
                        <input type="text" name="manager_name" id="manager_name" class="form-control"
                            value="<?= htmlspecialchars($restaurant['manager_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="chef_name" class="form-label">Chef Name</label>
                        <input type="text" name="chef_name" id="chef_name" class="form-control"
                            value="<?= htmlspecialchars($restaurant['chef_name'] ?? '') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="contact_number" class="form-label">Contact Number *</label>
                        <input type="tel" name="contact_number" id="contact_number" class="form-control"
                            value="<?= htmlspecialchars($restaurant['contact_number'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> <?= $isEdit ? 'Update' : 'Save' ?> Restaurant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>