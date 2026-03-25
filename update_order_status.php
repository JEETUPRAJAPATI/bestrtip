<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$order_id = $_POST['order_id'] ?? '';
$status = $_POST['status'] ?? '';

if (empty($order_id)) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit();
}

$db = getDbInstance();
$db->where('id', $order_id);
$result = $db->update('restaurant_orders', ['status' => $status]);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}
