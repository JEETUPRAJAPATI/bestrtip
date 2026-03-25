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
    $category_id = $data_to_store['id'] ?? '';

    $store_data = [
        'name' => $vendor_name,
    ];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Edit
        $msg = "edited";
        $db->where('id', $_POST['id']);
        $db->update('vendor_category', $store_data);
    } else {
        // Add
        $msg = "added";
        $db->insert('vendor_category', $store_data);
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
    $data = $db->getOne("vendor_category");
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
                                    <label class="form-label">Category Name</label>
                                    <input type="text" name="name" class="form-control" required
                                        value="<?= $edit ? htmlspecialchars($data['name']) : '' ?>">
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
    $(document).ready(function() {
        $('select').select2();
    });
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>