<?php
session_start();
require_once 'config/config.php';

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === TRUE) {
	header('Location:index.php');
}


?>

<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
	<title>Agent Login</title>
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
		<form action="authenticate.php" method="post" id="guide_form" enctype="multipart/form-data">
			<div class="block">
				<div class="logo-area"><img src="assets/img/black-logo.png" alt="logo" /></div>
				<div class="login-form">
					<div class="row mb-3">
						<div class="col-md text-center login-head">Log in to Go2ladakh</div>
					</div>
					<div class="row mb-3">
						<div class="col-md">
							<label class="form-label" for="basic-default-phone">User Name</label>
							<input type="text" id="basic-default-phone" class="form-control phone-mask" name="user_name">
						</div>
					</div>
					<div class="row mb-3">
						<div class="col-md">
							<label class="form-label" for="basic-default-phone">Password</label>
							<input type="password" id="basic-default-phone" class="form-control phone-mask" name="password">
						</div>
					</div>
					<div class="row">
						<div class="col-md custom-login-btn">
							<button type="submit" class="btn btn-primary">Login</button>
							<div style="display: block; padding-top: 15px">
								<a href="forget_password.php">Forgotten account?</a>
								<a href="register_agent.php" style="display: inline-block; padding-left: 10px">Register your account</a> </br>

							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</body>

</html>