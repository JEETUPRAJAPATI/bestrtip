<?php

session_start();
require_once '../config/config.php';

$db = getDbInstance();
$taxi_id = isset($_POST['taxi_id']) ? intval($_POST['taxi_id']) : 0;

if ($taxi_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid package ID']);
    exit;
}
$db->where('status', 'Active');
$db->where('id', $taxi_id);
$latestOverviewSection = $db->getOne('taxi');

if (!$latestOverviewSection) {
    echo json_encode(['success' => false, 'message' => 'No active overview found']);
    exit;
}
$db->where('taxi_id', $taxi_id);
$overviewSectionImages = $db->get('overview_section_images', null, ['image_path']);
// echo "<pre>";
// print_r($overviewSectionImages);
// die();
$overviewHtml = '';
if ($latestOverviewSection) {
    $overviewHtml = "<h2>Overview</h2><p>" . htmlspecialchars($latestOverviewSection['description']) . "</p>";
} else {
    $overviewHtml = "<p>No overview available.</p>";
}

$galleryHtml = '';
if (!empty($overviewSectionImages)) {
    $galleryHtml = '<div class="l-img">';
    $galleryHtml .= '<img src="' . htmlspecialchars($overviewSectionImages[0]['image_path']) . '" alt="gallery" />';
    $galleryHtml .= '</div><div class="s-img">';
    for ($i = 1; $i < count($overviewSectionImages); $i++) {
        $galleryHtml .= '<div class="col"><img src="' . htmlspecialchars($overviewSectionImages[$i]['image_path']) . '" alt="gallery" /></div>';
    }
    $galleryHtml .= '</div>';
} else {
    $galleryHtml = '<p>No images available.</p>';
}
echo json_encode([
    'success' => true,
    'overviewHtml' => $overviewHtml,
    'galleryHtml' => $galleryHtml
]);
exit;
