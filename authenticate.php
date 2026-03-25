<?php
require_once './config/config.php';
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
} else {
	die('Method Not allowed');
}
