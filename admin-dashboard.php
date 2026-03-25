<?php
session_start();
require_once './config/config.php';
require_once 'includes/auth_validate.php';

//Get DB instance. function is defined in config.php
$db = getDbInstance();
 

include_once('includes/header.php');
?>

<div class="layout-page">

  <!-- Content wrapper -->
  <div class="content-wrapper">

    <div id="page-wrapper">
      <div class="row">
        <div class="col-lg-12 p-5">
          <h1 class="page-header text-center">Dashboard</h1>
        </div>
        <!-- /.col-lg-12 -->
      </div>

    </div>

  </div>
</div>

<?php include_once('includes/footer.php'); ?>