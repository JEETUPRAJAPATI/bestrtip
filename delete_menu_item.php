<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$db = getDbInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $item_id = $_POST['id'];

    // First get restaurant_id for redirect
    $db->where('id', $item_id);
    $item = $db->getOne('restaurant_menu');

    if ($item) {
        $db->where('id', $item_id);
        $result = $db->delete('restaurant_menu');

        if ($result) {
            $_SESSION['success'] = 'Menu item deleted successfully!';
        } else {
            $_SESSION['failure'] = 'Failed to delete menu item: ' . $db->getLastError();
        }

        // Redirect back to the same restaurant
        header('Location: add_menu.php?pid=' . encryptId($item['property_id']) . '&rid=' . $item['restaurant_id']);
        exit();
    }
}

// If we got here something went wrong
$_SESSION['failure'] = 'Invalid request';
header('Location: dashboard.php');
exit();
