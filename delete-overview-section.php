<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

if (isset($_GET['id'])) {
    $sectionId = decryptId($_GET['id']);
    $db = getDbInstance();
    $db->startTransaction();

    try {
        $db->where('overview_section_id', $sectionId);
        $images = $db->get('overview_section_images');
        foreach ($images as $image) {
            if (file_exists($image['image_path'])) {
                unlink($image['image_path']);
            }
        }

        $db->where('overview_section_id', $sectionId);
        $db->delete('overview_section_images');
        $db->where('id', $sectionId);
        $db->delete('overview_section');
        $db->commit();

        $_SESSION['message'] = 'Overview section and its images were deleted successfully.';
        header('Location: view-overview-section.php');
        exit;
    } catch (Exception $e) {
        $db->rollback();
        $_SESSION['message'] = 'Error deleting overview section: ' . $e->getMessage();
        header('Location: view-overview-section.php');
        exit;
    }
} else {
    $_SESSION['message'] = 'Invalid section ID.';
    header('Location: view-overview-section.php');
    exit;
}
