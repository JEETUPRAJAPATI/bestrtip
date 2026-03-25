<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Decrypt and validate the ID from GET parameter
$id = isset($_GET['id']) && !empty($_GET['id']) ? decryptId($_GET['id']) : '';


if (!empty($id)) {
    $db = getDbInstance();

    // Get the uploaded bill file path
    $db->where('id', $id);
    // Delete the sales record
    $db->where('id', $id);
    $deleted = $db->delete('sales');

    if ($deleted) {
        $_SESSION['success'] = "Sales record deleted successfully.";
    } else {
        $_SESSION['failure'] = "Failed to delete sales record.";
    }
} else {
    $_SESSION['failure'] = "Invalid sales ID.";
}

header("Location: sales.php");
exit;
