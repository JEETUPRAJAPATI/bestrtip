<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Get decrypted ID
$id = isset($_GET['crm']) && !empty($_GET['crm']) ? decryptId($_GET['crm']) : "";

if (!empty($id)) {
    $db = getDbInstance();
    $db->where('id', $id);

    $property_deleted = $db->delete('properties');

    if ($property_deleted) {
        $_SESSION['success'] = "Property deleted successfully!";
    } else {
        $_SESSION['failure'] = "Failed to delete property: " . $db->getLastError();
    }
    header("Location: property_list.php");
    exit();
} else {
    $_SESSION['failure'] = "Invalid property ID.";
    header("Location: property_list.php");
    exit();
}
