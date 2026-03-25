<?php
session_start();
require_once '../config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  isset($_POST['data_save_form']) && $_POST['data_save_form'] == 'hotel') {
    $data_to_store = array_filter($_POST);
    $db = getDbInstance();
    $save_data = [];
    $save_data["hotel_details"] = json_encode($data_to_store['hotel'] ?? []);
    $save_data["updated_by"] = $_SESSION['user_id']; 
    $_POST['agent_query_id'];
    $db->where('id', $_POST['agent_query_id']);
    $last_id = $db->update('agent_queries', $save_data);
    $_SESSION['success'] = "Hotels changed successfully.";
    echo "Hotels changed successfully.";
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  isset($_POST['data_save_form']) && $_POST['data_save_form'] == 'driver') {
    $data_to_store = array_filter($_POST);
    $db = getDbInstance();
    $save_data = [];
    $save_data["driver_details"] = json_encode($data_to_store['transport'] ?? []);
    $save_data["updated_by"] = $_SESSION['user_id']; 
    $_POST['agent_query_driver_id'];
    $db->where('id', $_POST['agent_query_driver_id']);
    $last_id = $db->update('agent_queries', $save_data);
    $_SESSION['success'] = "Driver changed successfully.";
    echo "Driver changed successfully.";
} else {
    echo "404";
}
