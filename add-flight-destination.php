<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

// Get Input data from query string
$destination = filter_input(INPUT_GET, 'destination');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($destination) {

    $db = getDbInstance();
    $data = array("name" => $destination);
    // print_r($data);
    // die();
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $db->where('id', decryptId($_GET['id']));
        $id = $db->update('flight_destination',  $data);
    } else {
        $id = $db->insert('flight_destination',  $data);
    }

    if ($id) {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $_SESSION['success'] = "Record updated successfully";
        } else {
            $_SESSION['success'] = "New record created successfully";
        }
        header('location: view-flight-destination.php');
        exit();
    } else {
        echo 'insert failed: ' . $db->getLastError();
        exit();
    }
}

$id = (isset($_GET['id']) && !empty($_GET['id'])) ? decryptId($_GET['id']) : "";
$edit = false;

if (!empty($id)) {
    $edit = true;
    $db = getDbInstance();
    $db->where('id', $id);
    $data = $db->getOne("flight_destination");
}

include BASE_PATH . '/includes/header.php';
?>
<div class="layout-page">

    <!-- Content wrapper -->
    <!-- Layout container -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">

            <h4 class="py-3 mb-4">Add Fixed Package</h4>

            <!-- Basic Layout -->
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form>
                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-company">Destination</label>
                                    <input type="text" name="destination" class="form-control" placeholder="Please enter destination name" value="<?php echo isset($data['name']) ? $data['name'] : '' ?>" />
                                    <input type="hidden" name="id" value="<?php echo (isset($data['id']) && !empty($data['id'])) ? encryptId($data['id']) : '' ?>">
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
<?php include BASE_PATH . '/includes/footer.php'; ?>