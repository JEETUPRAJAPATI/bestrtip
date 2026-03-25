<?php
require_once '../config/config.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = filter_input(INPUT_POST, 'user_name');
    $passwd = filter_input(INPUT_POST, 'password');

    //Get DB instance.
    $db = getDbInstance();

    $db->where("user_name", $username);
    $db->where("status", 'Active');

    $row = $db->get('agents');

    if ($db->count >= 1) {

        $db_password = $row[0]['password'];

        if (password_verify($passwd, $db_password)) {
            $_SESSION['user_logged_in'] = TRUE;
            $_SESSION['admin_type'] = $row[0]['type'];
            $_SESSION['user_id'] = $row[0]['id'];
            $_SESSION['full_name'] = $row[0]['full_name'];
            $_SESSION['user_name'] = $row[0]['user_name'];
            // print_r($_SESSION['admin_type']);
            // die();
            if ($_SESSION['admin_type'] == 'Admin') {
                header('Location:admin-dashboard.php');
            } else {
                header('Location:index.php');
            }
        } else {
            $_SESSION['login_failure'] = "Invalid user name or password";
            header('Location:login.php');
        }

        exit;
    } else {
        $_SESSION['login_failure'] = "Invalid user name or password";
        header('Location:login.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Signup</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 900px;
            margin-top: 50px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .image-section img {
            width: 100%;
            height: 100%;
            border-radius: 10px 0 0 10px;
        }

        .form-section {
            padding: 30px;
        }

        .btn-register {
            background-color: #fdbf00;
            border: none;
            padding: 10px;
        }

        .preview-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 image-section">
                <img src="https://t4.ftcdn.net/jpg/10/01/90/67/360_F_1001906743_wIUb9PnD3SvcE7b9TMjQzcMUGCjumzrC.jpg" alt="Driver Image">
            </div>
            <div class="col-md-6 form-section">
                <h4 class="text-center">Signup to Drive</h4>
                <form method="post" action="#" enctype="multipart/form-data">

                    <!-- Mobile Number Section -->
                    <div id="mobileSection">
                        <label class="form-label">Enter Mobile Number</label>
                        <div class="d-flex">
                            <select class="form-select w-auto me-2">
                                <option value="+91">🇮🇳 +91</option>
                            </select>
                            <input type="tel" id="mobileNumber" class="form-control" placeholder="Enter mobile number" pattern="[0-9]{10}" maxlength="10">
                        </div>
                        <button id="sendOtpBtn" class="btn btn-primary w-100 mt-3" type="button" onclick="sendOTP()">Send OTP</button>
                    </div>

                    <!-- OTP Section (Initially Hidden) -->
                    <div id="otpSection" class="d-none mt-3">
                        <p class="text-center">Enter the 6-digit OTP sent to your number</p>
                        <div class="d-flex justify-content-center gap-2">
                            <input type="text" class="otp-input form-control text-center" maxlength="1" oninput="moveToNext(this, 0)">
                            <input type="text" class="otp-input form-control text-center" maxlength="1" oninput="moveToNext(this, 1)">
                            <input type="text" class="otp-input form-control text-center" maxlength="1" oninput="moveToNext(this, 2)">
                            <input type="text" class="otp-input form-control text-center" maxlength="1" oninput="moveToNext(this, 3)">
                            <input type="text" class="otp-input form-control text-center" maxlength="1" oninput="moveToNext(this, 4)">
                            <input type="text" class="otp-input form-control text-center" maxlength="1" oninput="moveToNext(this, 5)">
                        </div>
                        <button class="btn btn-primary w-100 mt-3" type="button" onclick="verifyOTP()">Submit</button>
                        <p class="text-center mt-2">
                            Didn't receive OTP?
                            <span id="resendOtp" class="text-primary text-muted" onclick="resendOTP()">Resend in <span id="countdown">60</span>s</span>
                        </p>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>

<script>
    function sendOTP() {
        let mobileNumber = document.getElementById("mobileNumber").value;

        if (mobileNumber.length !== 10 || isNaN(mobileNumber)) {
            alert("Please enter a valid 10-digit mobile number.");
            return;
        }

        $.ajax({
            url: 'send_otp.php',
            type: 'POST',
            data: {
                mobileNumber: mobileNumber
            },
            dataType: "json",
            success: function(response) {
                if (response.error) {
                    alert(response.error);
                } else {
                    document.getElementById("mobileNumber").setAttribute("disabled", true);
                    document.getElementById("mobileSection").classList.add("d-none");
                    document.getElementById("otpSection").classList.remove("d-none");
                    startCountdown();
                }
            },
            error: function() {
                alert("Failed to send OTP. Please try again.");
            }
        });
    }

    function verifyOTP() {
        let otpInputs = document.querySelectorAll(".otp-input");
        let otp = "";

        otpInputs.forEach(input => otp += input.value);

        if (otp.length !== 6 || isNaN(otp)) {
            alert("Please enter a valid 6-digit OTP.");
            return;
        }

        $.ajax({
            url: 'verify_otp.php',
            type: 'POST',
            data: {
                otp: otp,
                mobileNumber: document.getElementById("mobileNumber").value
            },
            dataType: "json",
            success: function(response) {
                if (response.error) {
                    alert(response.error);
                } else {
                    window.location.href = 'driver_dashboard.php';
                }
            },
            error: function() {
                alert("Failed to verify OTP. Please try again.");
            }
        });
    }

    function startCountdown() {
        let time = 60;
        let countdownEl = document.getElementById("countdown");
        countdownEl.innerText = time;
        document.getElementById("resendOtp").classList.add("text-muted");

        let interval = setInterval(() => {
            time--;
            countdownEl.innerText = time;
            if (time === 0) {
                clearInterval(interval);
                document.getElementById("resendOtp").classList.remove("text-muted");
            }
        }, 1000);
    }

    function resendOTP() {
        document.getElementById("resendOtp").classList.add("text-muted");
        startCountdown();
    }

    function moveToNext(input, index) {
        let inputs = document.querySelectorAll(".otp-input");
        if (input.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    }
</script>

</html>