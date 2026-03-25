<?php

session_start();
require_once '../config/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$db = getDbInstance();
$package_id = isset($_POST['package_id']) ? intval($_POST['package_id']) : 0;

if ($package_id == 0) {
  echo json_encode(['success' => false, 'message' => 'Invalid package ID']);
  exit;
}

// Fetch package details
$db->where('package_id', $package_id);
$db->orderBy('id', 'desc');
$latestPackage = $db->get('package_details');

if (!$latestPackage) {
  echo json_encode(['success' => false, 'message' => 'No transport options available for this package.']);
  exit;
}

// Initialize transport price mapping
$transportPrices = [
  'coach' => 0,
  'tempo' => 0,
  'cryista' => 0,
  'innova' => 0,
  'fortuner' => 0,
  'zyalo_ertiga' => 0,
  'eco' => 0
];

// Calculate total transport prices
foreach ($latestPackage as $package) {
  foreach ($transportPrices as $key => $price) {
    if (!empty($package[$key])) {
      $transportPrices[$key] += $package[$key];
    }
  }
}

// Transport display names
$transportOptions = [
  'coach' => 'Coach',
  'tempo' => 'Tempo',
  'cryista' => 'Cryista',
  'fortuner' => 'Fortuner',
  'innova' => 'Innova',
  'zyalo_ertiga' => 'Zyalo Ertiga',
  'eco' => 'Eco'
];

// HTML for the transport selection
$transportHtml = '<h2>Select Transportation</h2><div class="transport-main">';

foreach ($transportPrices as $key => $price) {
  if ($price > 0) { // Display only available transports
    $label = $transportOptions[$key] ?? ucfirst($key);
    $transportHtml .= '
        <div class="col-md add-room-toggle-btn">
            <label class="form-label">' . htmlspecialchars(str_replace("Fixed", "", $label)) . '</label>
            <div class="input-group">
                <button class="btn border-lighter add-custom-padding decrement" type="button">-</button>
                <input
                    data-amount="' . htmlspecialchars($price) . '"
                    name="person[' . htmlspecialchars(str_replace("Fixed", "", $label)) . ']"
                    type="text" class="form-control text-center quantity" value="0">
                <button class="btn border-lighter add-custom-padding increment" type="button">+</button>
            </div>
        </div>';
  }
}

$transportHtml .= '</div>';

// Return JSON response
echo json_encode([
  'success' => true,
  'transportHtml' => $transportHtml
]);
exit;
