<?php
session_start();
require_once '../config/config.php';
$db = getDbInstance();
$results = $db->get("vehicles");
//print_r($result);
//$result = $conn->query($sql);

$vehicles = [];

foreach ($results as $row) {
        //print_r($row);
        $vehicles[] = [
            'id' => (int)$row['id'],
            'name' => $row['driver_name'],
            'contactNo' => $row['mobile'],
            'completedTrips' => 14,
            'Experience' => $row['experience'],
            'carnumber' => $row['vehicle_number'],
            'email' => $row['email'],
            'rating' => 4,
            'encryptedTrips' => 'TRP-2345-ABC',
            'image' => $row['exterior_images']
        ];
}

header('Content-Type: application/json');
echo json_encode($vehicles);
?>