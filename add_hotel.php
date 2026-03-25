<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data_to_store = array_filter($_POST);
    $imageUploadDir = 'uploads/hotel_images/'; // Make sure this directory exists and is writable
    $uploadedImages = [];
    if (!empty($_FILES['hotel_images']['name'][0])) {
        foreach ($_FILES['hotel_images']['name'] as $key => $name) {
            $tmpName = $_FILES['hotel_images']['tmp_name'][$key];
            $error = $_FILES['hotel_images']['error'][$key];
    
            if ($error === UPLOAD_ERR_OK) {
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $newName = uniqid('hotel_', true) . '.' . $ext;
                $destination = $imageUploadDir . $newName;
    
                if (move_uploaded_file($tmpName, $destination)) {
                    $uploadedImages[] = $newName;
                }
            }
        }
    }
    // Store the images in the DB as JSON or a comma-separated string
    if (!empty($uploadedImages)) {
        $data_to_store['images'] = json_encode($uploadedImages); // You could also use implode(',', $uploadedImages);
    }
  $db = getDbInstance();
  if (isset($_POST['id']) && !empty($_POST['id'])) {
    $db->where('id', $_POST['id']);
    $last_id = $db->update('hotels', $data_to_store);
    $msg = "edited";
  } else {
    $msg = "added";
    $last_id = $db->insert('hotels', $data_to_store);
  }

  if ($last_id) {
    $_SESSION['success'] = "Hotel $msg successfully!";
    header('location: hotel.php');
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
  $data = $db->getOne("hotels");
}

//print_r($rows);die;
include BASE_PATH . '/includes/header.php';
?>
<!-- Layout container -->
<div class="layout-page">

  <!-- Content wrapper -->
  <div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">

      <h4 class="py-3 mb-4"><?=$edit?'Edit':"Add"?> Hotel Information</h4>

      <!-- Basic Layout -->
      <div class="row">
        <div class="col-xl">
          <div class="card mb-4">
            <div class="card-body">
              <form action="" method="post" id="hotel_form" enctype="multipart/form-data">
                <div class="mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-company">Hotel Name</label>
                    <input type="text" class="form-control" name="hotel_name" value="<?php echo xss_clean($edit ? $data['hotel_name'] : ''); ?>" required />
                  </div>

                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Owner Name</label>
                    <input type="text" class="form-control" name="owner_name" value="<?php echo xss_clean($edit ? $data['owner_name'] : ''); ?>" required />
                  </div>


                  <div class="col-md">
                    <label class="form-label" for="basic-default-email">Hotel Rating</label>
                    <div class="input-group">
                      <label class="input-group-text" for="inputGroupSelect01">Options</label>
                      <select class="form-select" id="inputGroupSelect01" name="category" required>
                        <option value="">Choose...</option>
                        <?php $categories =  getCategories();
                        //print_r($categories);
                        foreach ($categories as $category) {
                          $select = ($edit && $category == $data['category']) ? "selected" : "";
                          echo  "<option value=\"$category\" $select>$category</option>";
                        }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Email ID</label>
                    <input type="text" class="form-control phone-mask" name="email_id" value="<?php echo xss_clean($edit ? $data['email_id'] : ''); ?>" required/>
                  </div>
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Mobile No.</label>
                    <input type="number" class="form-control phone-mask" name="mobile" value="<?php echo xss_clean($edit ? $data['mobile'] : ''); ?>" required/>
                  </div>
                  <input type="hidden" name="id" value="<?php echo $id ?>" />
                </div>

                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-company">Location</label>
                    
                    <?php
                    $db = getDbInstance();
                    $sql = "SELECT * FROM location";
                    $result = $db->query($sql);
                    //print_r($result);
                    
                    ?>
                    <select class="form-select" id="inputGroupSelect01" name="location" required>
                        <option value="">Choose...</option>
                        
                         <?php
                         foreach ($result as $item) {
                             $select = ($item["location"] == $data['location']) ? "selected" : "";
                         ?>
                         <option value="<?= $item["location"]; ?>" <?php echo $select;?>><?= $item["location"]; ?></option>
        <?php 
                    }
                         ?>
                      </select>
                  </div>

                  <div class="col-md">
                    <label class="form-label" for="basic-default-company">Website</label>
                    <input type="text" class="form-control" name="website" value="<?php echo xss_clean($edit ? $data['website'] : ''); ?>" />
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="hotel_images">Hotel Images</label>
                    <input type="file" class="form-control" name="hotel_images[]" id="hotel_images" multiple accept="image/*">
                  </div>
                </div>
                <div class="mb-3">
                <label class="form-label">Existing Images</label><br>
                <?php
                if ($edit && !empty($data['images'])) {
                    $images = json_decode($data['images'], true); // or explode(',', $data['images']) if you used a string
                    foreach ($images as $img) {
                        echo "<img src='uploads/hotel_images/$img' width='100' class='m-2' />";
                    }
                }
                ?>
                </div><br/>
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