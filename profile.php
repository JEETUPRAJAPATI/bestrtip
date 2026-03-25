<?php
session_start();
require_once './config/config.php';
require_once './includes/auth_validate.php';
is_agent_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $data_to_store = array_filter($_POST);

  $db = getDbInstance();
  $uploadImage = ['success' => true, 'msg' => ''];
  if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) { 
    $uploadImage = uploadImage($_FILES['logo']);
  }

  if (isset($uploadImage['success']) && $uploadImage['success'] === true) {

    if (!empty($uploadImage['msg'])) {
      $data_to_store['logo'] = $uploadImage['msg'];
    }
    $db->where('id', $_SESSION['user_id']);
    $last_id = $db->update('agents', $data_to_store);
    $msg = "edited";

    if ($last_id) {
      $_SESSION['success'] = "User details $msg successfully!";
    } else {
      echo 'insert failed: ' . $db->getLastError();
      exit();
    }
  } else {
    $_SESSION['failure'] = $uploadImage['msg'];
  }
}

$edit = true;
$db = getDbInstance();
$db->where('id', $_SESSION['user_id']);
$data = $db->getOne("agents");

require_once 'includes/agent_header.php';
?>
<!-- Layout container -->
<div class="layout-page">
  <!-- Content wrapper -->
  <div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="py-3 mb-4">Edit Profile</h4>
      <?php include BASE_PATH . '/includes/flash_messages.php'; ?>
      <!-- Basic Layout -->
      <div class="row">
        <div class="col-xl">
          <div class="card mb-4">
            <div class="card-body">
              <form action="" method="post" id="hotel_form" enctype="multipart/form-data">
                <div class="mb-3">
                  <label class="form-label" for="basic-default-company">Full Name</label>
                  <input type="text" disabled class="form-control" value="<?php echo xss_clean($edit ? $data['full_name'] : ''); ?>" placeholder="Full name" />
                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Email ID</label>
                    <input type="text" disabled id="basic-default-phone" value="<?php echo xss_clean($edit ? $data['email_id'] : ''); ?>" class="form-control phone-mask" value="emailid@gmail.com" />
                  </div>
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Mobile No.</label>
                    <input type="text" id="basic-default-phone" name="mobile" value="<?php echo xss_clean($edit ? $data['mobile'] : ''); ?>" class="form-control phone-mask" value="9999995555" />
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Company Name</label>
                    <input type="text" name="company_name" value="<?php echo xss_clean($edit ? $data['company_name'] : ''); ?>" class="form-control phone-mask" />
                  </div>
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">GST No.</label>
                    <input type="text" class="form-control phone-mask" name="gst_no" value="<?php echo xss_clean($edit ? $data['gst_no'] : ''); ?>" />
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label" for="basic-default-email">Complete Address</label>
                  <input type="text" class="form-control phone-mask" name="complete_address" value="<?php echo xss_clean($edit ? $data['complete_address'] : ''); ?>" />
                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Country</label>
                    <input type="text" class="form-control phone-mask" name="country" value="<?php echo xss_clean($edit ? $data['country'] : ''); ?>" />
                  </div>
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">State</label>
                    <input type="text" class="form-control phone-mask" name="state" value="<?php echo xss_clean($edit ? $data['state'] : ''); ?>" />
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Logo</label>
                    <input type="file" class="form-control phone-mask" name="logo" />
                  </div>
                  <div class="col-md"> 
                  <?php if(!empty($data['logo'])){ ?>
                  <img width="200px" src="<?=BASE_URL.$data['logo']?>" ?>
                  <?php } ?>
                
                </div>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- / Content -->
  </div>
</div>

<?php include BASE_PATH . '/includes/agent_footer.php'; ?>