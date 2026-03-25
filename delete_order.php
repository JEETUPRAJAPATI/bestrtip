<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($id)) {
    $_SESSION['failure'] = "Order ID is missing";
    header('location: order_list.php');
    exit();
}

$db = getDbInstance();

// First delete order items
$db->where('order_id', $id);
$db->delete('restaurant_order_items');

// Then delete the order
$db->where('id', $id);
$result = $db->delete('restaurant_orders');

if ($result) {
    $_SESSION['success'] = "Order deleted successfully!";
} else {
    $_SESSION['failure'] = "Failed to delete order: " . $db->getLastError();
}

header('location: order_list.php');
exit();
