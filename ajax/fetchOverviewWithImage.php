<?php

session_start();
require_once '../config/config.php';

$db = getDbInstance();
$packageId = isset($_POST['package_id']) ? intval($_POST['package_id']) : 0;
// echo $packageId;
// echo "fd";
// die;
if ($packageId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid package ID']);
    exit;
}
$db->where('status', 'Active');
$db->where('id', $packageId);
$latestOverviewSection = $db->getOne('packages');

if (!$latestOverviewSection) {
    echo json_encode(['success' => false, 'message' => 'No active overview found']);
    exit;
}
$db->where('package_id', $packageId);
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
    
   $galleryHtml .='<div class="relative w-full max-w-4xl overflow-hidden">
    <div id="carouselExampleIndicators" class="carousel carousel-theme slide">
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
          <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
          <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">';
          for ($i = 0; $i < count($overviewSectionImages); $i++) {
        if($i==0){
            $active = 'active';
        }
        $galleryHtml .= '<div class="carousel-item '.$active.'">
            <img src="'.htmlspecialchars($overviewSectionImages[$i]['image_path']).'" class="d-block w-100" alt="...">
          </div>';
     }
        $galleryHtml .='</div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
</div>
<div class="thumbnail-container grid grid-cols-3 sm:grid-cols-4 md:flex md:flex-wrap gap-2 md:gap-3 mt-4">';
 for ($i = 0; $i < count($overviewSectionImages); $i++) {
        $galleryHtml .= '
            <img src="'.htmlspecialchars($overviewSectionImages[$i]['image_path']).'" class="thumbnail w-20 h-20 object-cover rounded-md cursor-pointer" alt="...">
          ';
     }
    
$galleryHtml .='</div>';
    // $galleryHtml = '<div class="l-img">';
    // $galleryHtml .= '<img src="' . htmlspecialchars($overviewSectionImages[0]['image_path']) . '" alt="gallery" />';
    // $galleryHtml .= '</div><div class="s-img">';
    // for ($i = 1; $i < count($overviewSectionImages); $i++) {
    //     //$galleryHtml .= '<div class="col"><img src="' . htmlspecialchars($overviewSectionImages[$i]['image_path']) . '" alt="gallery" /></div>';
    //     $galleryHtml .= '<div class="carousel-item">
    //         <img src="'.htmlspecialchars($overviewSectionImages[$i]['image_path']).'" class="d-block w-100" alt="...">
    //       </div>';
    // }
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




