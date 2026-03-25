<?php
session_start();

require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$db = getDbInstance();

// Set header for JSON response
header('Content-Type: application/json');

// Sanitize input
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request'
    ]);
    exit();
}

// Check if this is a category or subcategory
$db->where('id', $id);
$item = $db->getOne('menu_categories');

if (!$item) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Category not found'
    ]);
    exit();
}

// Start transaction
$db->startTransaction();

try {
    if (empty($item['subcategory'])) {
        // This is a main category - delete all its subcategories and menu items

        // 1. Get all subcategories under this category
        $db->where('category', $item['category']);
        $subcategories = $db->get('menu_categories', null, 'id');

        // 2. Delete all menu items in these categories
        if (!empty($subcategories)) {
            $subcategory_ids = array_column($subcategories, 'id');
            $db->where('category_id', $subcategory_ids, 'IN');
            $db->delete('restaurant_menu');
        }

        // 3. Delete all subcategories
        $db->where('category', $item['category']);
        $db->delete('menu_categories');

        // 4. Delete the main category
        $db->where('id', $id);
        $db->delete('menu_categories');
    } else {
        // This is a subcategory - delete it and its menu items

        // 1. Delete all menu items in this subcategory
        $db->where('category_id', $id);
        $db->delete('restaurant_menu');

        // 2. Delete the subcategory
        $db->where('id', $id);
        $db->delete('menu_categories');
    }

    $db->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Deleted successfully'
    ]);
} catch (Exception $e) {
    $db->rollback();

    echo json_encode([
        'status' => 'error',
        'message' => 'Delete failed: ' . $e->getMessage()
    ]);
}

exit();
