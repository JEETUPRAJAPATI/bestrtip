<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$db = getDbInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = array(
        'category' => $_POST['category'],
        'subcategory' => $_POST['subcategory']
    );

    $db->where('id', $_POST['id']);
    $result = $db->update('menu_categories', $data);

    if ($result) {
        $_SESSION['success'] = 'Category updated successfully!';
    } else {
        $_SESSION['failure'] = 'Failed to update category: ' . $db->getLastError();
    }

    // Redirect back to the referring page
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
