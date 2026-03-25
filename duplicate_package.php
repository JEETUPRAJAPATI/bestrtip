<?php
require_once 'config/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['crm'])) {
    $id = decryptId($_GET['crm']);
    $db = getDbInstance();
    $db->where('id', $id);
    $package = $db->getOne('packages');

    if ($package) {
        $new_package_code = generateNewPackageCode($db, $package['package_code']);
        unset($package['id']);
        $package['package_code'] = $new_package_code;
        $package['package_name'] .= ' (Copy)';
        $new_package_id = $db->insert('packages', $package);

        if ($new_package_id) {
            $db->where('package_id', $id);
            $package_details = $db->get('package_details');
            foreach ($package_details as $detail) {
                unset($detail['id']);
                $detail['package_id'] = $new_package_id;
                $db->insert('package_details', $detail);
            }
            $_SESSION['success'] = "Package duplicated successfully!";
            header('Location: package.php');
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
    while ($db->where('package_code', $new_code)->has('packages')) {
        $suffix++;
        $new_code = $base_code . '-DUP' . $suffix;
    }

    return $new_code;
}
