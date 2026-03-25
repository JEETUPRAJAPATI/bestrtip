<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once dirname(__FILE__).'/../config/config.php';
require_once dirname(__FILE__).'/../helpers/helpers.php';
$input = file_get_contents('php://input');
$_POST = json_decode($input, true);
if (isset($_POST['email']) && isset($_POST['mobile']) && isset($_POST['otp']) && !empty($_POST['email']) && !empty($_POST['mobile'])  && !empty($_POST['otp'])) {
    
    $res = sendOTPMessage( base64_decode($_POST['otp']), $_POST['mobile']);
    // echo $res;
    $data = ['success'=> true, 'mobile'=> $_POST['mobile'] ]; // 'otp'=> base64_decode($_POST['otp'])
    echo json_encode($data);
    
}


?>