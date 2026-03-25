<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$db = getDbInstance();

if (isset($_GET['order_id'])) {
    $order_id = filter_var($_GET['order_id'], FILTER_VALIDATE_INT);

    if ($order_id) {
        // First delete all items associated with this order
        $db->where('order_id', $order_id);
        $db->delete('purchase_order_items');

        // Then delete the order itself
        $db->where('id', $order_id);
        $result = $db->delete('purchase_orders');

        if ($result) {
            $_SESSION['success'] = 'Purchase order deleted successfully!';
        } else {
            $_SESSION['failure'] = 'Failed to delete purchase order: ' . $db->getLastError();
        }
    } else {
        $_SESSION['failure'] = 'Invalid order ID';
    }
}

header('Location: purchase_list.php');
exit();
