<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once dirname(__FILE__).'/../config/config.php';
$input = file_get_contents('php://input');
$_POST = json_decode($input, true);

    // if ( (isset($_POST['destinationto']) && !empty($_POST['destinationto']) ) && isset($_POST['destinationfrom']) && !empty($_POST['destinationfrom']) && isset($_POST['newOrderDates']) && !empty($_POST['newOrderDates'])) {
        
    //     $db = getDbInstance();
    //     $db->where('tour_start', $_POST['destinationfrom']);
    //     $db->where('tour_end', $_POST['destinationto']);
    //     $db->where('tour_start_date', $_POST['newOrderDates']);
    //     $db->orderBy("name","asc");
    //     $packageData =  $db->get("fixed_package") ;
    //     if (!empty($packageData)) {
    //         echo json_encode($packageData); // Send the data back as a JSON response
    //     } else {
    //         echo json_encode([]); // Return an empty array if no data found
    //     }
    // } else 
    if ( (isset($_POST['destinationto']) && !empty($_POST['destinationto']) ) && isset($_POST['destinationfrom']) && !empty($_POST['destinationfrom']) ) {
        
        $db = getDbInstance();
        $db->where('tour_start', $_POST['destinationfrom']);
        $db->where('tour_end', $_POST['destinationto']);
        $db->where('tour_category', $_POST['category']);
        $db->orderBy("name","asc");
        $packageData =  $db->get("fixed_package") ;
        if (!empty($packageData)) {
            echo json_encode($packageData); // Send the data back as a JSON response
        } else {
            echo json_encode([]); // Return an empty array if no data found
        }
    } 

?>