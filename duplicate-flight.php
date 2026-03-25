<?php
require_once 'config/config.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['crm'])) {
    $id = decryptId($_GET['crm']);
    $db = getDbInstance();

    // Fetch flight details
    $db->where('id', $id);
    $flight = $db->getOne('flight_lists');

    if ($flight) {
        // Remove primary key
        unset($flight['id']);

        // Update timestamps
        $flight['created_at'] = date('Y-m-d H:i:s');
        $flight['updated_at'] = date('Y-m-d H:i:s');

        // Insert new flight
        $new_flight_id = $db->insert('flight_lists', $flight);

        if ($new_flight_id) {
            // Fetch related flight details
            $db->where('flight_id', $id);
            $flight_details = $db->get('flight_details');

            if (!empty($flight_details)) {
                foreach ($flight_details as $detail) {
                    unset($detail['id']); // Remove primary key
                    $detail['flight_id'] = $new_flight_id; // Assign new flight ID
                    $db->insert('flight_details', $detail);
                }
            }

            $_SESSION['success'] = "Flight duplicated successfully!";
            header('Location: view-flight-booking.php');
            exit();
        } else {
            echo 'Insert failed: ' . $db->getLastError();
            exit();
        }
    } else {
        echo "Flight not found!";
        exit();
    }
} else {
    echo "Invalid request!";
    exit();
}
