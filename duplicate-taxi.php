<?php
require_once 'config/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['crm'])) {
    $id = decryptId($_GET['crm']);
    $db = getDbInstance();
    $db->where('id', $id);
    $package = $db->getOne('taxi');

    if ($package) {
        $new_package_code = generateNewPackageCode($db, $package['package_code']);
        unset($package['id']);
        $package['package_code'] = $new_package_code;
        $package['package_name'] .= ' (Copy)';
        $new_package_id = $db->insert('taxi', $package);

        if ($new_package_id) {
            $db->where('taxi_id', $id);
            $taxi_details = $db->get('taxi_details');
            foreach ($taxi_details as $detail) {
                unset($detail['id']);
                $detail['taxi_id'] = $new_package_id;
                $db->insert('taxi_details', $detail);
            }
            $_SESSION['success'] = "taxi duplicated successfully!";
            header('Location: view-taxi-booking.php');
            exit();
        } else {
            echo 'Insert failed: ' . $db->getLastError();
            exit();
        }
    } else {
        echo "Package not found!";
        exit();
    }
} else {
    echo "Invalid request!";
    exit();
}
function generateNewPackageCode($db, $base_code)
{
    $suffix = 1;
    $new_code = $base_code . '-DUP' . $suffix;
    while ($db->where('package_code', $new_code)->has('taxi')) {
        $suffix++;
        $new_code = $base_code . '-DUP' . $suffix;
    }

    return $new_code;
}
