<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $data_to_store = array_filter($_POST);

  $db = getDbInstance();
  if (isset($_POST['id']) && !empty($_POST['id'])) {
    $db->where('id', $_POST['id']);
    $last_id = $db->update('services', $data_to_store);
    $msg = "edited";
  } else {
    $msg = "added";
    $last_id = $db->insert('services', $data_to_store);
  }

  if ($last_id) {
    $_SESSION['success'] = "Service $msg successfully!";
    header('location: service.php');
    exit();
  } else {
    echo 'insert failed: ' . $db->getLastError();
    exit();
  }
}

$id = isset($_GET['crm']) && !empty($_GET['crm']) ? decryptId($_GET['crm']) : "";
$edit = false;
if (!empty($id)) {
  $edit = true;
  $db = getDbInstance();
  $db->where('id', $id);
  $data = $db->getOne("services");
}

$service_type = ['Cumulative', 'Per Person', 'Per Service'];

include BASE_PATH . '/includes/header.php';
?>
<!-- Layout container -->
<div class="layout-page">

  <!-- Content wrapper -->
  <div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">

      <h4 class="py-3 mb-4"><?=$edit?'Edit':"Add"?> Service Information</h4>

      <!-- Basic Layout -->
      <div class="row">
        <div class="col-xl">
          <div class="card mb-4">
            <div class="card-body">
              <form action="" method="post" id="hotel_form" enctype="multipart/form-data">
               
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Service Name</label>
                    <input type="text" class="form-control" name="name" value="<?php echo xss_clean($edit ? $data['name'] : ''); ?>" required />
                  </div>
                 <div class="col-md">
                    <label class="form-label" for="basic-default-email">Service Type</label>
                    <div class="input-group">
                      <label class="input-group-text" for="inputGroupSelect01">Options</label>
                      <select class="form-select" id="inputGroupSelect01" name="type" required>
                        <option value="">Choose...</option>
                        <?php 
                        foreach ($service_type as $service_t) {
                          $select = ($edit && $service_t == $data['type']) ? "selected" : "";
                          echo  "<option value=\"$service_t\" $select>$service_t</option>";
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Amount</label>
                    <input type="text" class="form-control phone-mask" name="amount" value="<?php echo xss_clean($edit ? $data['amount'] : ''); ?>" required/>
                  </div>
                 
                  <input type="hidden" name="id" value="<?php echo $id ?>" />
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
<?php include BASE_PATH . '/includes/footer.php'; ?>