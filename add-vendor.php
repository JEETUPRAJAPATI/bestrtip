<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = getDbInstance();
// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_to_store = array_filter($_POST);

    $vendor_name = $data_to_store['name'] ?? '';
    $email = strtolower(trim($data_to_store['email'] ?? ''));
    $mobile = strtolower(trim($data_to_store['mobile'] ?? '')); // use strtolower in case mobile has alphanumeric
    $category_id = $data_to_store['category_id'] ?? '';
    $id = $data_to_store['id'] ?? null;

    // Check for uniqueness
    $db->where('email', $email);
    if ($id) $db->where('id', $id, '!=');
    $existing_email = $db->getOne('vendor');

    $db->where('mobile', $mobile);
    if ($id) $db->where('id', $id, '!=');
    $existing_mobile = $db->getOne('vendor');

    if ($existing_email) {
        $_SESSION['failure'] = "Email already exists!";
        header('location: vendor.php');
        exit();
    }

    if ($existing_mobile) {
        $_SESSION['failure'] = "Mobile number already exists!";
        header('location: vendor.php');
        exit();
    }

    $store_data = [
        'name' => $vendor_name,
        'email' => $email,
        'mobile' => $mobile,
        'category_id' => $category_id
    ];

    if ($id) {
        // Edit
        $msg = "edited";
        $db->where('id', $id);
        $db->update('vendor', $store_data);
    } else {
        // Add
        $msg = "added";
        $db->insert('vendor', $store_data);
    }

    $_SESSION['success'] = "Vendor $msg successfully!";
    header('location: vendor.php');
    exit();
}


// Get data for edit
$id = isset($_GET['id']) && !empty($_GET['id']) ? decryptId($_GET['id']) : '';
$edit = false;
$data = [];

if (!empty($id)) {
    $edit = true;
    $db->where('id', $id);
    $data = $db->getOne("vendor");
}

// Get active categories
$db->where('status', 'active');
$categories = $db->get("vendor_category", null, 'id, name');

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
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4">
                <span class="text-muted fw-light">Vendor /</span>
                <?= $edit ? 'Edit' : 'Add' ?> Vendor
            </h4>

            <div class="row">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="" method="post">

                                <!-- Vendor Name -->
                                <div class="mb-3">
                                    <label class="form-label">Vendor Name</label>
                                    <input type="text" name="name" class="form-control" required
                                        value="<?= $edit ? htmlspecialchars($data['name']) : '' ?>">
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" id="email" class="form-control" value="<?= $data['email'] ?? '' ?>" required placeholder="Enter your email">
                                    </div>
                                    <div class="col">
                                        <label for="mobile">Mobile No</label>
                                        <input type="tel" name="mobile" id="mobile" class="form-control" value="<?= $data['mobile'] ?? '' ?>" required pattern="[0-9]{10}" maxlength="10" placeholder="Enter 10-digit mobile number">
                                    </div>
                                </div>

                                <!-- Vendor Category -->
                                <div class="mb-3">
                                    <label class="form-label">Vendor Category</label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">-- Select Category --</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= $edit && $cat['id'] == $data['category_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <!-- Hidden ID -->
                                <input type="hidden" name="id" value="<?= $id ?>">

                                <!-- Submit -->
                                <button type="submit" class="btn btn-primary">Save</button>
                                <a href="vendors.php" class="btn btn-secondary">Cancel</a>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const mobile = document.getElementById('mobile').value;

        // Simple regex for email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            alert('Please enter a valid email.');
            e.preventDefault();
        }

        if (!/^\d{10}$/.test(mobile)) {
            alert('Please enter a valid 10-digit mobile number.');
            e.preventDefault();
        }
    });
    $(document).ready(function() {
        $('select').select2();
    });
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>