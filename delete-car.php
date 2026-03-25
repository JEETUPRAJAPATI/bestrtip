<?php

session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$id = isset($_GET['id']) && !empty($_GET['id']) ? decryptId($_GET['id']) : "";

if (!empty($id)) {
    $db = getDbInstance();
    $db->where('id', $id);
    $car = $db->getOne('carlist', 'name');

    if ($car) {
        $carName = strtolower(trim($car['name']));
        $carName = preg_replace('/\s+/', '_', $carName);

        if (preg_match('/^[a-z0-9_]+$/', $carName)) {
            $checkColumnSQL = "SHOW COLUMNS FROM `taxi_details` LIKE '$carName'";
            $columnExists = $db->rawQueryOne($checkColumnSQL);

            if ($columnExists) {
                $dropColumnSQL = "ALTER TABLE `taxi_details` DROP COLUMN `$carName`";
                $db->rawQuery($dropColumnSQL);
            }
        }
    }
    $db->where('id', $id);
    $package_delete = $db->delete('carlist');

    if ($package_delete) {
        $_SESSION['success'] = "Car deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete the car!";
    }

    header("Location: view-car.php");
    exit();
}
