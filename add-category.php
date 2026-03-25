<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

// Get Input data from query string
$category = filter_input(INPUT_GET, 'category');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($category) {
     
    $db = getDbInstance();
    $data = Array ("name" =>$category);
    
    if (  isset($_GET['id']) && !empty($_GET['id'])  ) {
        $db->where('id', decryptId($_GET['id']) );
        $id = $db->update ('category',  $data);
    } else {
       $id = $db->insert ('category',  $data); 
    }
    
    if($id) {
        if ( isset($_GET['id']) && !empty($_GET['id']) ) {
            $message =  "Record updated successfully";
        } else {
            $message =  "New record created successfully";
        }
        
        echo "<script type='text/javascript'>
            window.onload = function() {
                showPopup('successPopup', "."'"."$message"."'".");
            };
          </script>";
        
    } else {
        $str =  'insert failed: ' . $db->getLastError();
        $message = str_replace("'", "", $str);
        echo "<script type='text/javascript'>
            window.onload = function() {
                showPopup('errorPopup', "."'"."$message"."'".");
            };
          </script>";
    }


}

$id = ( isset($_GET['id']) && !empty($_GET['id']) )? decryptId($_GET['id']) : "";
$edit = false;

if (!empty($id)) {
  $edit = true;
  $db = getDbInstance();
  $db->where('id', $id);
  $data = $db->getOne("category");
}

include BASE_PATH . '/includes/header.php';
?>

        <!-- Layout container -->
        <div class="layout-page">
  
            <!-- Content wrapper -->
            <div class="content-wrapper">
              <!-- Content -->
  
                <div class="container-xxl flex-grow-1 container-p-y">
                  
                <h4 class="py-3 mb-4">Add Category</h4>
  
                <!-- Basic Layout -->
                  <div class="row">
                    <div class="col-xl">
                      <div class="card mb-4">
                        <div class="card-body">
                          <form>
                            <div class="mb-3">
                              <label class="form-label" for="basic-default-company">Category</label>
                              <input type="text" name="category" class="form-control" placeholder="Please enter destination name" value="<?php echo isset($data['name'])?$data['name']:'' ?>" />
                              <input type="hidden" name="id" value ="<?php echo ( isset($data['id']) && !empty($data['id']) ) ? encryptId($data['id']):'' ?>" >
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
<?php 
include BASE_PATH . '/includes/footer.php';
?>
