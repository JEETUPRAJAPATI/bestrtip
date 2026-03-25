<?php
session_start();
require_once 'config/config.php'; 
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $data_to_store = array_filter($_POST);
   
    $db = getDbInstance();
    $data_to_store['password'] = password_hash($data_to_store['password'], PASSWORD_DEFAULT);
    $data_to_store['status'] = 'Incomplete';
    $data_to_store['email_otp'] = generateOTP();
    sendEmail( $data_to_store['email_otp'], $data_to_store['email_id']);

    $data_to_store['mobile_otp'] = generateOTP();
    sendOTPMessage($data_to_store['mobile_otp'], $data_to_store['mobile']);
    $dbA = getDbInstance();
    $dbA->orderBy('id', 'desc');
    $agent_last = $dbA->getOne("agents");
    if($package_last){
      $data_to_store['agent_code'] = sprintf("AG%04d", $agent_last['id'] + 1);
    }else{
      $data_to_store['agent_code'] = sprintf("AG%04d",  1);
    }
	
    $last_id = $db->insert('agents', $data_to_store);

    if ($last_id) {
        $_SESSION['agent_id'] = $last_id;        
        header('location: verify_otp_agent.php');
        exit();
    } else {
        echo 'insert failed: ' . $db->getLastError();
        exit();
    }
}
 
$edit = false;
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Agent Register</title>
    <meta name="description" content="" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" />
    <link rel="stylesheet" href="assets/css/login-register.min.css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" />
</head>

<body>
    <div class="login-page-wrapper">
    <?php include BASE_PATH . '/includes/flash_messages.php'; ?>
        <form action="" method="post" id="guide_form" enctype="multipart/form-data">
            <div class="block">
                <div class="logo-area"><img src="assets/img/black-logo.png" alt="logo" /></div>
                <div class="login-form">
                    <div class="row mb-3">
                        <div class="col-md text-center login-head">Create an Account</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md">
                            <label class="form-label" for="basic-default-phone">Email id</label>
                            <input type="text" id="basic-default-phone" name="email_id" class="form-control phone-mask">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md">
                            <label class="form-label" for="basic-default-phone">Full Name</label>
                            <input type="text" id="basic-default-phone" name="full_name" class="form-control phone-mask">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md">
                            <label class="form-label" for="basic-default-phone">Username</label>
                            <input type="text" id="basic-default-phone" name="user_name" class="form-control phone-mask">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md">
                            <label class="form-label" for="basic-default-phone">Password</label>
                            <input type="password" id="basic-default-phone" name="password" class="form-control phone-mask">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md">
                            <label class="form-label" for="basic-default-phone">Mobile No.</label>
                            <input type="number" id="basic-default-phone" name="mobile" class="form-control phone-mask">
                        </div>
                    </div>
                    <input type ="hidden" value="AG<?php echo date('dmYHis');?>">
                    <!--<div class="row mb-3">
                    <!--    <div class="col-md">-->
                    <!--        <label class="form-label" for="basic-default-phone">Agent Code</label>-->
                    <!--        <input type="text" id="basic-default-phone" name="agent_code" class="form-control phone-mask">-->
                    <!--    </div>-->
                    <!--</div>-->

                        

                    <div class="row">
                        <div class="col-md custom-login-btn">
                            <button type="submit" class="btn btn-primary">Next</button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

</body>

</html>