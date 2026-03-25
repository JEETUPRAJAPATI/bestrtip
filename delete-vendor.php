<?php

session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$id = isset($_GET['id']) && !empty($_GET['id']) ? decryptId($_GET['id']) : "";

if (!empty($id)) {
    $db = getDbInstance();
    $db->where('id', $id);
    $delete = $db->delete('vendor');
    if ($delete) {
        $_SESSION['success'] = "Vendor permanently deleted successfully!";
    } else {
        $_SESSION['failure'] = "Failed to delete vendor.";
    }
    header("Location: vendor.php");
    exit;
}
