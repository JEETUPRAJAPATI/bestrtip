<?php

session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$id = isset($_GET['id']) && !empty($_GET['id']) ? decryptId($_GET['id']) : "";


if (!empty($id)) {
    $db = getDbInstance();
    $db->where('id', $id);

    $data = array(
        'status' => 'Inactive'
    );
    $blog = $db->update('blogs', $data);
    $_SESSION['success'] = "Blog Deleted successfully!";
    header("Location:view-blog.php");
}
