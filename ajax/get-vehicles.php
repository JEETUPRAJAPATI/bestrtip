<?php
session_start();
require_once '../config/config.php';
$db = getDbInstance();
$results = $db->get("carlist");
//print_r($result);
//$result = $conn->query($sql);

$vehicles = [];

foreach ($results as $row) {
        //print_r($row);
        $vehicles[] = [
            'id' => (int)$row['id'],
            'bag' => (int)$row['bag'],
            'count' => (int)$row['passenger'],
            'name' => $row['name'],
            'type' => 'SVU',
            'people' => (int)$row['passenger'],
            'image' => $row['image']
        ];
}

header('Content-Type: application/json');
echo json_encode($vehicles);
?>