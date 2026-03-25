<?php

session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$id = isset($_GET['id']) && !empty($_GET['id']) ? decryptId($_GET['id']) : "";

// echo $id;
// die();
if (!empty($id)) {
    $db = getDbInstance();
    $db->where('id', $id);

    $data = array(
        'status' => 'Inactive'
    );
    $package_delete = $db->update('flight_lists', $data);
    $_SESSION['success'] = "Flight Deleted successfully!";
    header("Location:view-flight-booking.php");
}
