<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>Package Booking</title>
  <meta name="description" content="" />
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Platypi:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


  <link href='https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css' rel='stylesheet'>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.js"></script>

  <!-- Core CSS -->
  <link rel="stylesheet" href="assets/vendor/css/core.css" />
  <link rel="stylesheet" href="assets/css/front.min.css" />
  <link rel="stylesheet" href="assets/vendor/css/theme-default.css" />
</head>

<body>
  <!-- <div class="page-wrapper"> -->
  <!-- Top Header -->
  <div class="top-header">
    <div class="block">
      <div class="logo">
        <a href="index.php"><img src="assets/images/logo.png" alt="logo" style="height: 300px;" /></a>
      </div>
      <nav>
        <?php
        $db = getDbInstance();
        ?>
        <ul>
          <?php if (!empty($_SESSION['user_id'])) {
            $db->where('id', $_SESSION['user_id']);
            $data = $db->getOne("agents");
          ?>
            <li><a href="agent_query_generate.php">Query Generate</a></li>
            <li><a href="agent_booking.php">My Booking</a></li>
            <li><a href="agent_query.php">Queries</a></li>
            <li><a href="agent_invoice.php">Invoices</a></li>

            <li>
              <a class="profile-icon" href="profile.php">
                <?php if (!empty($data['logo'])) { ?>
                  <img width="200px" src="<?= BASE_URL . $data['logo'] ?>" alt="Profile" />
                <?php } else { ?>
                  <img src="assets/img/avatars/1.png" alt="Profile" />
                <?php } ?>
              </a>
            </li>

            <li><a href="logout.php">Logout</a></li>

          <?php } else { ?>
            <!-- Default Navigation for Non-Logged-In Users -->
            <li>
              <a href="#"><span class="nav-icon"><img src="/assets/images/nav-item-icon-1.svg" /></span><span>Travel</span></a>
            </li>
            <li>
              <a href="#"><span class="nav-icon"><img src="/assets/images/nav-item-icon-1.svg" /></span><span>Hotel</span></a>
            </li>
            <li>
              <a href="#"><span class="nav-icon"><img src="/assets/images/nav-item-icon-1.svg" /></span><span>Tour</span></a>
            </li>
            <li>
              <a href="#"><span class="nav-icon"><img src="/assets/images/nav-item-icon-1.svg" /></span><span>Blog</span></a>
            </li>
            <li class="login-btn">
              <a href="login.php">
                <span class="nav-icon"><img src="/assets/images/login-icon.svg" /></span><span>Login</span>
              </a>
            </li>
          <?php } ?>
        </ul>
      </nav>
    </div>
  </div>
  <!-- </div> -->


  <div class="layout-wrapper layout-content-navbar" style="display:block;">
    <!-- <div class="layout-container"> -->