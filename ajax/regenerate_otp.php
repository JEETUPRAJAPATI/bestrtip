<?php
session_start();
require_once '../config/config.php'; 
$db = getDbInstance();
$db->where('id',  $_POST['agent_id']);
$data = $db->getOne("agents");

$db = getDbInstance();
if($_POST['type']=='mobile'){
    $data_to_store['mobile_otp'] = generateOTP();
    sendOTPMessage($data_to_store['mobile_otp'], $data['mobile']);
    $db->where('id', $_POST['agent_id']);    
    $db->update('agents', $data_to_store);
    echo "Successfully sent otp on your mobile";
}else if($_POST['type']=='email'){
    $data_to_store['email_otp'] = generateOTP();
    sendEmail( $data_to_store['email_otp'], $data['email_id']);
    $db->where('id', $_POST['agent_id']);
    $db->update('agents', $data_to_store);
    echo "Successfully sent otp on your email";
} 
 
?>
 