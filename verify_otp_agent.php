<?php
session_start();
require_once 'config/config.php'; 
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $data_to_store = array_filter($_POST); 

    $db = getDbInstance();
    $db->where('id', $_SESSION['agent_id']); 
    $data = $db->getOne("agents");
    if($data){
        if($data['mobile_otp'] != $data_to_store['mobile_otp']){
            $_SESSION['failure'] = "Mobile OTP miss match, please try again"; 
        }elseif($data['email_otp'] != $data_to_store['email_otp']){
            $_SESSION['failure'] = "Email OTP miss match, please try again"; 
        }else{
            $agent_data = ['status'=>'Inactive'];
            $db = getDbInstance();
            $db->where('id', $_SESSION['agent_id']);
            $db->update('agents', $agent_data);
            $_SESSION['success'] = "Agent successfully registered!";
            unset($_SESSION['agent_id']);
            header('location: login.php');
            exit(); 
        }

    }else{
        echo 'insert failed: ' . $db->getLastError();
    } 
}

$db = getDbInstance();
$db->where('id', $_SESSION['agent_id']);
$data = $db->getOne("agents");

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="login-page-wrapper">
    <?php include BASE_PATH . '/includes/flash_messages.php'; ?>
        <form action="" method="post" id="guide_form" enctype="multipart/form-data">
            <div class="block">
                <div class="logo-area"><img src="assets/img/black-logo.png" alt="logo" /></div>
                <div class="login-form">
                    <div class="row mb-3">
                        <div class="col-md text-center login-head">Verify OTP Account</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md">
                            <label class="form-label" for="basic-default-phone">Please Enter Mobile OTP (<?=$data['mobile'] ?>)</label>
                            <input type="text" name="mobile_otp" id="basic-default-phone" class="form-control phone-mask">
                            <div class="text-end mt-2" ><a href="javascript:void(0)" id="regenerate-motp" data-type="mobile">Regenerte Mobile OTP</a></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md">
                            <label class="form-label" for="basic-default-phone">Please Enter Email OTP (<?=$data['email_id'] ?>)</label>
                            <input type="text" name="email_otp" id="basic-default-phone" class="form-control phone-mask">
                            <div class="text-end mt-2" ><a href="javascript:void(0)" id="regenerate-eotp" data-type="email">Regenerte Email OTP</a></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md custom-login-btn">
                            <button type="submit" class="btn btn-primary">Sign Up</button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function() {
            $('#regenerate-motp , #regenerate-eotp').click(function(e) {                
                let type = $(this).attr("data-type");
                let agent_id = "<?=$_SESSION['agent_id']?>";
                $.ajax({
                    url: 'ajax/regenerate_otp.php',
                    type: 'POST',
                    dataType: 'html',
                    data:{type:type,agent_id:agent_id},
                    success: function(response){
                        alert(response); 
                    },
                });
            });

        });
    </script> 
</body>

</html>