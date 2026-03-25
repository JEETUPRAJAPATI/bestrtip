<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = getDbInstance();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_to_store = array_filter($_POST);

    // Remove id from data before insert/update
    $id = isset($data_to_store['id']) ? $data_to_store['id'] : null;
    unset($data_to_store['id']);

    if (!empty($id)) {
        // UPDATE
        $db->where('id', $id);
        $last_id = $db->update('properties', $data_to_store);
        $msg = "edited";
    } else {
        // INSERT - Generate unique_id
        $lastProperty = $db->orderBy("id", "DESC")->getOne("properties", "unique_id");
        if ($lastProperty && isset($lastProperty['unique_id'])) {
            $lastNum = (int) filter_var($lastProperty['unique_id'], FILTER_SANITIZE_NUMBER_INT);
            $newUid = 'PR' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newUid = 'PR001';
        }
        $data_to_store['unique_id'] = $newUid;

        $last_id = $db->insert('properties', $data_to_store);
        $msg = "added";
    }

    if ($last_id) {
        $_SESSION['success'] = "Property $msg successfully!";
        header('location: property_list.php');
        exit();
    } else {
        echo 'Insert/Update failed: ' . $db->getLastError();
        exit();
    }
}

$id = isset($_GET['crm']) && !empty($_GET['crm']) ? decryptId($_GET['crm']) : "";
$edit = false;
$data = [];
if (!empty($id)) {
    $edit = true;
    $db->where('id', $id);
    $data = $db->getOne("properties");
}

include BASE_PATH . '/includes/header.php';
?>

<!-- Layout container -->
<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4"><?= $edit ? 'Edit' : "Add" ?> Property</h4>
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="" method="post" id="property_form" enctype="multipart/form-data">

                                <?php if ($edit) { ?>
                                    <div class="mb-3">
                                        <label class="form-label">Property ID</label>
                                        <input type="text" class="form-control" value="<?php echo xss_clean($data['unique_id']); ?>" readonly>
                                    </div>
                                <?php } ?>

                                <div class="mb-3">
                                    <label class="form-label">Hotel Name</label>
                                    <input type="text" class="form-control" name="hotel_name" value="<?php echo xss_clean($edit ? $data['hotel_name'] : ''); ?>" required />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" required><?php echo xss_clean($edit ? $data['address'] : ''); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Number of Rooms</label>
                                    <input type="number" class="form-control" name="no_of_rooms" value="<?php echo xss_clean($edit ? $data['no_of_rooms'] : ''); ?>" required />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Reservation Manager</label>
                                    <input type="text" class="form-control" name="reservation_manager" value="<?php echo xss_clean($edit ? $data['reservation_manager'] : ''); ?>" />
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo xss_clean($edit ? $data['email'] : ''); ?>" />
                                    </div>
                                    <div class="col-md">
                                        <label class="form-label">Alternate Email</label>
                                        <input type="email" class="form-control" name="alternate_email" value="<?php echo xss_clean($edit ? $data['alternate_email'] : ''); ?>" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md">
                                        <label class="form-label">Mobile No.</label>
                                        <input type="text" class="form-control" name="mobile_no" value="<?php echo xss_clean($edit ? $data['mobile_no'] : ''); ?>" />
                                    </div>
                                    <div class="col-md">
                                        <label class="form-label">Alternate Mobile No.</label>
                                        <input type="text" class="form-control" name="alternate_mobile_no" value="<?php echo xss_clean($edit ? $data['alternate_mobile_no'] : ''); ?>" />
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">GST No.</label>
                                    <input type="text" class="form-control" name="gst_no" value="<?php echo xss_clean($edit ? $data['gst_no'] : ''); ?>" />
                                </div>

                                <input type="hidden" name="id" value="<?php echo $id; ?>" />
                                <button type="submit" class="btn btn-primary">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include BASE_PATH . '/includes/footer.php'; ?>