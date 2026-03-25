<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'], $_POST['status'])) {
    $sectionId = $_POST['id'];
    $status = $_POST['status'];

    // Initialize the database instance
    $db = getDbInstance();

    // Update the status in the overview_section table
    $data = [
        'status' => $status
    ];

    $db->where('id', $sectionId);
    $updated = $db->update('overview_section', $data);

    if ($updated) {
        // Redirect back to the overview sections page with success message
        $_SESSION['message'] = 'Status updated successfully!';
    } else {
        $_SESSION['message'] = 'Failed to update status.';
    }

    header('Location: view-overview-section.php');
    exit;
} else {
    // Redirect back with error message if the form is not submitted correctly
    $_SESSION['message'] = 'Invalid request.';
    header('Location: view-overview-section.php');
    exit;
}
