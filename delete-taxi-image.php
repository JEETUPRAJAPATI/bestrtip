<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

// Database Instance
$db = getDbInstance();

// Handle GET request for image deletion
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $image_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $section_id = filter_input(INPUT_GET, 'section_id', FILTER_VALIDATE_INT);
    $encryptedId = encryptId($section_id);  // Use the correct $section_id here
    // echo "<pre>";
    // echo "section", $section_id, "</br>";
    // echo "encrypt", $encryptedId, "</br>";
    // echo "image id", $image_id;
    // echo "</pre>";
    // die();
    if ($image_id && $section_id) {
        // Get image path from database
        $db->where('id', $image_id);
        $image = $db->getOne('overview_section_images');

        if ($image) {
            // Delete the image file from the server
            $image_path = $image['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path); // Delete the image from the folder
            }

            // Delete the image record from the database
            $db->where('id', $image_id);
            $db->delete('overview_section_images');

            // Redirect back to the edit page
            header("Location: add-taxi-booking.php?crm=$encryptedId");
            exit;
        } else {
            echo "<script>alert('Image not found!'); window.location.href = 'add-taxi-booking.php?crm=$encryptedId';</script>";
        }
    } else {
        echo "<script>alert('Invalid request!'); window.location.href = 'add-taxi-booking.php?crm=$encryptedId';</script>";
    }
}
