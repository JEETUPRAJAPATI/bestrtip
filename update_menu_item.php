<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

// Initialize response array
$response = ['status' => 'error', 'message' => ''];

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit();
}

// Get input data
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$item_name = filter_input(INPUT_POST, 'item_name', FILTER_SANITIZE_STRING);
$category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);

// Validate inputs
if (!$id || !$item_name || !$category_id || !$type || $price === false) {
    $response['message'] = 'All fields are required and must be valid';
    echo json_encode($response);
    exit();
}

$db = getDbInstance();

// Check if the menu item exists and belongs to the restaurant
$db->where('id', $id);
$menu_item = $db->getOne('restaurant_menu');

if (!$menu_item) {
    $response['message'] = 'Menu item not found';
    echo json_encode($response);
    exit();
}

// Check if the category exists and belongs to the same restaurant
$db->where('id', $category_id);
$db->where('restaurant_id', $menu_item['restaurant_id']);
$category = $db->getOne('menu_categories');

if (!$category) {
    $response['message'] = 'Invalid category selected';
    echo json_encode($response);
    exit();
}

// Validate type
if (!in_array($type, ['Veg', 'Non-Veg'])) {
    $response['message'] = 'Invalid food type';
    echo json_encode($response);
    exit();
}

// Validate price
if ($price <= 0) {
    $response['message'] = 'Price must be greater than 0';
    echo json_encode($response);
    exit();
}

// Prepare update data
$update_data = [
    'item_name' => $item_name,
    'category_id' => $category_id,
    'type' => $type,
    'price' => $price
];

// Update the menu item
$db->where('id', $id);
$result = $db->update('restaurant_menu', $update_data);

if ($result) {
    $response['status'] = 'success';
    $response['message'] = 'Menu item updated successfully';
} else {
    $response['message'] = 'Failed to update menu item: ' . $db->getLastError();
}

echo json_encode($response);
exit();
