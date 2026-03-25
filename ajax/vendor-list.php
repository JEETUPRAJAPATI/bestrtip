<?php

session_start();
require_once '../config/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$categoryId = $_GET['category_id'] ?? null;

if ($categoryId) {
    $db = getDbInstance();
    $vendors = $db->where('category_id', $categoryId)->get('vendor', null, ['id', 'name']);
    echo json_encode(['status' => 'success', 'vendors' => $vendors]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Category ID is required']);
}
