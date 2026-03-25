<?php

session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$id = isset($_GET['crm']) && !empty($_GET['crm']) ? decryptId($_GET['crm']) : "";


if (!empty($id)) {
    $db = getDbInstance();
    $db->where('id', $id);

    $data = array(
        'status' => 'Deleted'
    );
    $package_delete = $db->update('taxi', $data);
    $_SESSION['success'] = "Taxi Deleted successfully!";
    header("Location:view-taxi-booking.php");
}
