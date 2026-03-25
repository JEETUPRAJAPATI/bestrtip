<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

// Sanitize input
$order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

if (!$order_id || !$status) {
    $_SESSION['failure'] = 'Invalid request';
    header('Location: purchase_list.php');
    exit;
}

$db = getDbInstance();
$db->where('id', $order_id);

// Allowed status values
$allowed_statuses = ['Pending', 'Completed', 'Cancelled', 'Received'];
if (!in_array($status, $allowed_statuses)) {
    $_SESSION['failure'] = 'Invalid status value';
    header('Location: purchase_list.php');
    exit;
}

$data = ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')];
$result = $db->update('purchase_orders', $data);

if ($result) {
    $_SESSION['success'] = 'Order status updated successfully';
} else {
    $_SESSION['failure'] = 'Failed to update order status: ' . $db->getLastError();
}

header('Location: purchase_list.php');
exit;
