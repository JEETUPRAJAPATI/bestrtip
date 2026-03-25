<?php

session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$id = isset($_GET['id']) && !empty($_GET['id']) ? decryptId($_GET['id']) : "";

if (!empty($id)) {
    $db = getDbInstance();

    // First, get the file path if it exists
    $db->where('id', $id);
    $bill_file = $db->getValue('expense', 'bill_upload');

    // Delete the expense
    $db->where('id', $id);
    $delete = $db->delete('expense');

    if ($delete) {
        // Delete the uploaded bill file if it exists
        if ($bill_file && file_exists($bill_file)) {
            unlink($bill_file);
        }

        $_SESSION['success'] = "Expense permanently deleted successfully!";
    } else {
        $_SESSION['failure'] = "Failed to delete expense.";
    }

    header("Location: expense.php");
    exit;
}
