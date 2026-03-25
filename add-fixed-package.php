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

$db = getDbInstance();
$db->orderBy("name", "asc");
$categoryData = $db->get("category");

$db = getDbInstance();
$db->orderBy("name", "asc");
$destinationData = $db->get("destination");
// print_r($destinationData);exit;
if (isset($_POST) && isset($_POST['name'])) {
  // print_r($_POST);exit;

  $target_dir = "uploads/";

  $target_file = $target_dir . time() . basename($_FILES["image"]["name"]);
  if (isset($_FILES["image"]) && !empty($_FILES["image"]["name"]) &&  $_FILES["image"]["size"] > 10000000) {  // check 10mb
    echo "<script type='text/javascript'>
            window.onload = function() {
                showPopup('successPopup', " . "'" . "$message" . "'" . ");
            };
          </script>";
  } else {
    $target_file = '';
    if (isset($_FILES) && isset($_FILES["image"]) && !empty($_FILES["image"]["name"])) {

      $target_file = $target_dir . time() . basename($_FILES["image"]["name"]);
      //   echo $target_file;
      move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }
    $data = [
      "name" => $_POST['name'],
      "description" => $_POST['description'],
      "tour_start" => $_POST['tour_start'],
      "tour_end" => $_POST['tour_end'],
      "tour_start_date" => json_encode($_POST['tour_start_date']),
      "tour_end_date" => json_encode($_POST['tour_end_date']),
      "website_link" => isset($_POST['website_link']) ? $_POST['website_link'] : '',
      "pdf_link" => isset($_POST['pdf_link']) ? $_POST['pdf_link'] : '',
      "tour_category" => $_POST['tour_category'],
      "inventory" => isset($_POST['inventory']) ? $_POST['inventory'] : '',
      "commision_1" => isset($_POST['commision_1']) ? $_POST['commision_1'] : 0,
      "solo_riding_on_double_room" => isset($_POST['solo_riding_on_double_room']) ? $_POST['solo_riding_on_double_room'] : 0,
      "double_riding_on_double_room" => isset($_POST['double_riding_on_double_room']) ? $_POST['double_riding_on_double_room'] : 0,
      "solo_riding_on_single_room" => isset($_POST['solo_riding_on_single_room']) ? $_POST['solo_riding_on_single_room'] : 0,
      "double_riding_on_single_room" => isset($_POST['double_riding_on_single_room']) ? $_POST['double_riding_on_single_room'] : 0,
      "three_rider_2_bike_1_triple_room" => isset($_POST['three_rider_2_bike_1_triple_room']) ? $_POST['three_rider_2_bike_1_triple_room'] : 0,
      "three_rider_3_bike_1_triple_room" => isset($_POST['three_rider_3_bike_1_triple_room']) ? $_POST['three_rider_3_bike_1_triple_room'] : 0,
      "agent_commision_1" => isset($_POST['agent_commision_1']) ? $_POST['agent_commision_1'] : 0,
      "spcl_agent_commision_1" => isset($_POST['spcl_agent_commision_1']) ? $_POST['spcl_agent_commision_1'] : 0,
      "commision_2" => isset($_POST['commision_2']) ? $_POST['commision_2'] : 0,
      "dbl_twin" => isset($_POST['dbl_twin']) ? $_POST['dbl_twin'] : 0,
      "single_sharing" => isset($_POST['single_sharing']) ? $_POST['single_sharing'] : 0,
      "extra_bed_18" => isset($_POST['extra_bed_18']) ? $_POST['extra_bed_18'] : 0,
      "child_no_bed" => isset($_POST['child_no_bed']) ? $_POST['child_no_bed'] : 0,
      "child_with_bed" => isset($_POST['child_with_bed']) ? $_POST['child_with_bed'] : 0,
      "agent_commision_2" => isset($_POST['agent_commision_2']) ? $_POST['agent_commision_2'] : 0,
      "spcl_agent_commision_2" => isset($_POST['spcl_agent_commision_2']) ? $_POST['spcl_agent_commision_2'] : 0,
      "add_inclusions" => isset($_POST['add_inclusions']) ?  (explode('Powered by', $_POST['add_inclusions']))[0] : '',
      "add_exclusions" => isset($_POST['add_exclusions']) ? (explode('Powered by', $_POST['add_exclusions']))[0] : '',
      "add_itineary" => isset($_POST['add_itineary']) ? (explode('Powered by', $_POST['add_itineary']))[0] : '',
    ];
    if (isset($target_file) && !empty($target_file)) {
      $data['image'] = $target_file;
    }
    // print_r($_POST['add_itineary']);
    // print_r($data);exit;
    $db = getDbInstance();
    if (isset($_GET['id']) && !empty($_GET['id'])) {
      $db->where('id', decryptId($_GET['id']));
      $id = $db->update('fixed_package',  $data);
      $message =  "Record updated successfully";
    } else {
      $id = $db->insert('fixed_package',  $data);
      $message =  "New record created successfully";
    }
    echo "<script type='text/javascript'>
            window.onload = function() {
                showPopup('successPopup', " . "'" . "$message" . "'" . ");
            };
          </script>";
  }
}

$id = (isset($_GET['id']) && !empty($_GET['id'])) ? decryptId($_GET['id']) : "";
$edit = false;

if (!empty($id)) {
  $edit = true;
  //   echo '------------------',$id;
  $db = getDbInstance();
  $db->where('id', $id);
  $data = $db->getOne("fixed_package");
  //   print_r($_POST);exit;

}

include BASE_PATH . '/includes/header.php';
?>
<style>
  .app-brand-logo {
    display: block;
    text-align: center;
    padding-top: 20px;

    img {
      height: 75px;
    }
  }

  .border-right-white {
    border-right: solid 1px rgba(255, 255, 255, 0.6);
  }

  .border-right-dark {
    border-right: solid 1px rgba(0, 0, 0, 0.2);
  }

  .sticky-col {
    position: -webkit-sticky;
    position: sticky;
    background-color: white;
  }

  .search-module {
    display: flex;
  }

  .pos-relative {
    position: relative !important;
  }

  .custom-dd-menu {
    position: absolute;
    right: 0;
    display: none;

    &.display-block {
      display: block;
    }
  }

  .bx-dots-vertical-rounded {
    display: block;
    width: 5px;
    height: 18px;
    position: relative;
    padding-left: 33px;

    span {
      display: inline-block;
      position: relative;
      width: 4px;
      height: 4px;
      background: #000;
      border-radius: 10px;
      top: -12px;

      &::after,
      &::before {
        position: absolute;
        width: 4px;
        height: 4px;
        content: '';
        background: #000;
        left: 0;
        border-radius: 10px;
        top: 6px;
      }

      &::after {
        top: 12px;
      }
    }
  }

  .remove-more-btn {
    position: absolute;
    margin-top: -25px;
    right: 25px;
    text-transform: uppercase;
    font-size: 12px;
    cursor: pointer;
    color: red;
    text-decoration: underline;
  }

  .add-more-row {
    .col-md {
      text-align: right;
    }

    span {
      color: #566a7f;
      cursor: pointer;
    }
  }
</style>


<!-- Layout container -->
<div class="layout-page">

  <!-- Content wrapper -->
  <div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">

      <h4 class="py-3 mb-4">Add Fixed Package</h4>

      <!-- Basic Layout -->
      <div class="row">
        <div class="col-xl">
          <div class="card mb-4">
            <div class="card-body">
              <form method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                  <input type="hidden" value="<?php echo (isset($data['id']) && !empty($data['id'])) ? encryptId($data['id']) : '' ?>" name="id">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-company">Product Name</label>
                    <input type="text" value="<?php echo isset($data['name']) ? $data['name'] : '' ?>" name="name" class="form-control" placeholder="" required />
                  </div>
                  <div class="col-md">
                    <label class="form-label" for="basic-default-company">Product Image</label>
                    <input type="file" value="<?php echo isset($data['image']) ? $data['image'] : '' ?>" name="image" class="form-control" />

                    <?php if (isset($data['image']) && !empty($data['image'])) { ?>
                      <img src="https://agent.go2ladakh.in/<?php echo $data['image'] ?> " height=200px width=150px>
                    <?php } ?>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-company">Product Description</label>
                    <textarea class="form-control" name="description"><?php echo isset($data['description']) ? $data['description'] : ''; ?></textarea>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Tour Start</label>
                    <select class="form-select" name="tour_start" required>
                      <option disabled selected>Tour Start From</option>
                      <?php
                      foreach ($destinationData as $rows) :
                        $selected = (isset($data['tour_start']) && $data['tour_start'] == $rows['id']) ? 'selected' : '';

                      ?>
                        <option value="<?php echo $rows['id']; ?>" <?php echo $selected; ?>><?php echo ucfirst($rows['name']); ?></option>
                      <?php
                      endforeach;
                      ?>
                    </select>
                  </div>
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Tour End</label>
                    <select class="form-select" name="tour_end" required>
                      <option disabled selected>Tour End</option>
                      <?php
                      foreach ($destinationData as $rows) :
                        $selected = (isset($data['tour_end']) && $data['tour_end'] == $rows['id']) ? 'selected' : '';
                      ?>
                        <option value="<?php echo $rows['id']; ?>" <?php echo $selected; ?>><?php echo ucfirst($rows['name']); ?></option>
                      <?php
                      endforeach;
                      ?>
                    </select>
                  </div>
                </div>
                <?php
                if (isset($data) && !empty($data)) {
                  $data['tour_start_date'] = JSON_DECODE($data['tour_start_date']);
                  $data['tour_end_date'] = JSON_DECODE($data['tour_end_date']);
                  for ($i = 0; $i < count($data['tour_start_date']); $i++) { ?>
                    <div id="repeater-fields">
                      <div class="row mb-3 repeat-box">
                        <div class="col-md ">
                          <label class="form-label" for="basic-default-phone">Tour Start Date</label>
                          <input class="form-control" name="tour_start_date[]" value="<?php echo isset($data['tour_start_date'][$i]) ? $data['tour_start_date'][$i] : '' ?>" type="date" value="2021-06-18">
                        </div>
                        <div class="col-md">
                          <label class="form-label" for="basic-default-phone">Tour End Date</label>
                          <?php if ($i > 0) { ?>
                            <div class="col-md remove-more-btn" onclick="removeRow(this)">Remove</div>
                          <?php } ?>
                          <input class="form-control" name="tour_end_date[]" value="<?php echo isset($data['tour_end_date'][$i]) ? $data['tour_end_date'][$i] : '' ?>" type="date" value="2021-06-18">
                        </div>
                      </div>
                    </div>
                  <?php }
                } else { ?>
                  <div id="repeater-fields">
                    <div class="row mb-3 repeat-box">
                      <div class="col-md ">
                        <label class="form-label" for="basic-default-phone">Tour Start Date</label>
                        <input class="form-control" name="tour_start_date[]" type="date">
                      </div>
                      <div class="col-md">
                        <label class="form-label" for="basic-default-phone">Tour End Date</label>
                        <input class="form-control" name="tour_end_date[]" type="date">
                      </div>
                    </div>
                  </div>
                <?php } ?>
                <div class="row mb-3 add-more-row" id="add-more-date">
                  <div class="col-md">
                    <span class="add-more-btn">+ Add More</span>
                  </div>
                </div>


                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Website Link</label>
                    <input type="text" class="form-control phone-mask" name="website_link" value="<?php echo isset($data['website_link']) ? $data['website_link'] : '' ?>" placeholder="" />
                  </div>
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">PDF Link</label>
                    <input type="text" class="form-control phone-mask" name="pdf_link" value="<?php echo isset($data['pdf_link']) ? $data['pdf_link'] : '' ?>" placeholder="" />
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Tour Category</label>
                    <select class="form-select" name="tour_category" onchange="toggleDivs()" id="categoryDropdown" required>
                      <option disabled selected>Tour Category</option>
                      <?php
                      foreach ($categoryData as $rows) :
                        $selected = (isset($data['tour_category']) && $data['tour_category'] == $rows['id']) ? 'selected' : '';
                      ?>
                        <option value="<?php echo $rows['id']; ?>" <?php echo $selected; ?>><?php echo ucfirst($rows['name']); ?></option>
                      <?php
                      endforeach;
                      ?>
                    </select>
                  </div>
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Inventory</label>
                    <input type="text" name="inventory" value="<?php echo isset($data['inventory']) ? $data['inventory'] : '' ?>" class="form-control phone-mask" placeholder="" />
                  </div>
                </div>
                <div class="row hiddenCategory" id="bike_trips">
                  <div class="col-xl">
                    <div class="mb-4">
                      <div class="table-responsive border-light border-solid mb-3">
                        <table class="table">
                          <thead>
                            <tr class="bg-dark align-middle">
                              <th class="text-white border-right-white">Package Price</th>
                              <th class="text-white border-right-white">Commission</th>
                              <th class="text-white border-right-white">Solo riding on Double Room </th>
                              <th class="text-white border-right-white">Double riding on Double Room</th>
                              <th class="text-white border-right-white">Solo riding Single Room</th>
                              <th class="text-white border-right-white">Double riding Single Room</th>
                              <th class="text-white border-right-white">3 Rider 2. bike 1 Triple room </th>
                              <th class="text-white border-right-white">3 Rider 3 bike 1 Triple room </th>
                            </tr>
                          </thead>
                          <tbody class="table-border-bottom-0">
                            <tr>
                              <td class="border-right-dark">Price</td>
                              <td class="border-right-dark"><input type="text" id="commision_1" name="commision_1" value="<?php echo isset($data['commision_1']) ? $data['commision_1'] : '' ?>" class="form-control phone-mask" placeholder="0" /></td>
                              <td class="border-right-dark"><input type="text" id="solo_riding_on_double_room" name="solo_riding_on_double_room" value="<?php echo isset($data['solo_riding_on_double_room']) ? $data['solo_riding_on_double_room'] : '' ?>" class="form-control phone-mask" placeholder="20000" /></td>
                              <td class="border-right-dark"><input type="text" id="double_riding_on_double_room" name="double_riding_on_double_room" value="<?php echo isset($data['double_riding_on_double_room']) ? $data['double_riding_on_double_room'] : '' ?>" class="form-control phone-mask" placeholder="20000" /></td>
                              <td class="border-right-dark"><input type="text" id="solo_riding_on_single_room" name="solo_riding_on_single_room" value="<?php echo isset($data['solo_riding_on_single_room']) ? $data['solo_riding_on_single_room'] : '' ?>" class="form-control phone-mask" placeholder="20000" /></td>
                              <td class="border-right-dark"><input type="text" id="double_riding_on_single_room" name="double_riding_on_single_room" value="<?php echo isset($data['double_riding_on_single_room']) ? $data['double_riding_on_single_room'] : '' ?>" class="form-control phone-mask" placeholder="20000" /></td>
                              <td class="border-right-dark"><input type="text" id="three_rider_2_bike_1_triple_room" name="three_rider_2_bike_1_triple_room" value="<?php echo isset($data['three_rider_2_bike_1_triple_room']) ? $data['three_rider_2_bike_1_triple_room'] : '' ?>" class="form-control phone-mask" placeholder="20000" /></td>
                              <td class="border-right-dark"><input type="text" id="three_rider_3_bike_1_triple_room" name="three_rider_3_bike_1_triple_room" value="<?php echo isset($data['three_rider_3_bike_1_triple_room']) ? $data['three_rider_3_bike_1_triple_room'] : '' ?>" class="form-control phone-mask" placeholder="20000" /></td>
                            </tr>
                            <tr>
                              <td class="border-right-dark">Agent price</td>
                              <td class="border-right-dark"><input type="text" id="agent_commision_1" onkeyup="calculateAgentPrices1()" name="agent_commision_1" value="<?php echo isset($data['agent_commision_1']) ? $data['agent_commision_1'] : '' ?>" class="form-control phone-mask" placeholder="10" /></td>
                              <td class="border-right-dark" id="agent_solo_riding_on_double_room"></td>
                              <td class="border-right-dark" id="agent_double_riding_on_double_room"></td>
                              <td class="border-right-dark" id="agent_solo_riding_on_single_room"></td>
                              <td class="border-right-dark" id="agent_double_riding_on_single_room"></td>
                              <td class="border-right-dark" id="agent_three_rider_2_bike_1_triple_room"></td>
                              <td class="border-right-dark" id="agent_three_rider_3_bike_1_triple_room"></td>
                            </tr>
                            <tr>
                              <td class="border-right-dark">Special agent price</td>
                              <td class="border-right-dark"><input type="text" id="spcl_agent_commision_1" onchange="calculateSpclAgentPrices1()" name="spcl_agent_commision_1" value="<?php echo isset($data['spcl_agent_commision_1']) ? $data['spcl_agent_commision_1'] : '' ?>" class="form-control phone-mask" placeholder="20" /></td>
                              <td class="border-right-dark" id="spcl_agent_solo_riding_on_double_room"></td>
                              <td class="border-right-dark" id="spcl_agent_double_riding_on_double_room"></td>
                              <td class="border-right-dark" id="spcl_agent_solo_riding_on_single_room"></td>
                              <td class="border-right-dark" id="spcl_agent_double_riding_on_single_room"></td>
                              <td class="border-right-dark" id="spcl_agent_three_rider_2_bike_1_triple_room"></td>
                              <td class="border-right-dark" id="spcl_agent_three_rider_3_bike_1_triple_room"></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row" id="otherss">
                  <div class="col-xl">
                    <div class="mb-4">
                      <div class="table-responsive border-light border-solid mb-3">
                        <table class="table">
                          <thead>
                            <tr class="bg-dark align-middle">
                              <th class="text-white border-right-white">Package Price</th>
                              <th class="text-white border-right-white">Commission</th>
                              <th class="text-white border-right-white">DBL/ TWIN</th>
                              <th class="text-white border-right-white">Single sharing</th>
                              <th class="text-white border-right-white">Extra bed 18+</th>
                              <th class="text-white border-right-white">Child No bed</th>
                              <th class="text-white border-right-white">Child with bed</th>
                            </tr>
                          </thead>
                          <tbody class="table-border-bottom-0">
                            <tr>
                              <td class="border-right-dark">Price</td>
                              <td class="border-right-dark"><input type="text" id="commision_2" name="commision_2" value="<?php echo isset($data['commision_2']) ? $data['commision_2'] : '' ?>" class="form-control phone-mask" placeholder="0" /></td>
                              <td class="border-right-dark"><input type="text" id="dbl_twin" name="dbl_twin" value="<?php echo isset($data['dbl_twin']) ? $data['dbl_twin'] : '' ?>" class="form-control phone-mask" placeholder="20000" /></td>
                              <td class="border-right-dark"><input type="text" id="single_sharing" name="single_sharing" value="<?php echo isset($data['single_sharing']) ? $data['single_sharing'] : '' ?>" class="form-control phone-mask" placeholder="20000" /></td>
                              <td class="border-right-dark"><input type="text" id="extra_bed_18" name="extra_bed_18" value="<?php echo isset($data['extra_bed_18']) ? $data['extra_bed_18'] : '' ?>" class="form-control phone-mask" placeholder="20000" /></td>
                              <td class="border-right-dark"><input type="text" id="child_no_bed" name="child_no_bed" value="<?php echo isset($data['child_no_bed']) ? $data['child_no_bed'] : '' ?>" class="form-control phone-mask" placeholder="20000" /></td>
                              <td class="border-right-dark"><input type="text" id="child_with_bed" name="child_with_bed" value="<?php echo isset($data['child_with_bed']) ? $data['child_with_bed'] : '' ?>" class="form-control phone-mask" placeholder="20000" /></td>
                            </tr>
                            <tr>
                              <td class="border-right-dark">Agent price</td>
                              <td class="border-right-dark"><input type="text" onkeyup="calculatePriceAgent2()" id="agent_commision_2" name="agent_commision_2" value="<?php echo isset($data['agent_commision_2']) ? $data['agent_commision_2'] : '' ?>" class="form-control phone-mask" placeholder="10" /></td>
                              <td class="border-right-dark" id="agent_dbl_twin"></td>
                              <td class="border-right-dark" id="agent_single_sharing"></td>
                              <td class="border-right-dark" id="agent_extra_bed_18"></td>
                              <td class="border-right-dark" id="agent_child_no_bed"></td>
                              <td class="border-right-dark" id="agent_child_with_bed"></td>
                            </tr>
                            <tr>
                              <td class="border-right-dark">Special agent price</td>
                              <td class="border-right-dark"><input type="text" onkeyup="calculatePriceSpclAgent2()" id="spcl_agent_commision_2" name="spcl_agent_commision_2" value="<?php echo isset($data['spcl_agent_commision_2']) ? $data['spcl_agent_commision_2'] : '' ?>" class="form-control phone-mask" placeholder="20" /></td>
                              <td class="border-right-dark" id="spcl_agent_dbl_twin"></td>
                              <td class="border-right-dark" id="spcl_agent_single_sharing"></td>
                              <td class="border-right-dark" id="spcl_agent_extra_bed_18"></td>
                              <td class="border-right-dark" id="spcl_agent_child_no_bed"></td>
                              <td class="border-right-dark" id="spcl_agent_child_with_bed"></td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Add Inclusions <i class="small-font-style">(Please add each paragraph in a separate line)</i></label>
                    <textarea id="froala-editor" class="form-control" name="add_inclusions">  <?php echo isset($data['add_inclusions']) ? $data['add_inclusions'] : ''; ?>           </textarea>
                  </div>
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Add Exclusions <i class="small-font-style">(Please add each paragraph in a separate line)</i></label>
                    <textarea id="froala-editor" class="form-control" name="add_exclusions">  <?php echo isset($data['add_exclusions']) ? $data['add_exclusions'] : ''; ?>  </textarea>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Add Itineary <i class="small-font-style">(Please add each paragraph in a separate line)</i></label>
                    <textarea id="froala-editor" class="form-control" name="add_itineary">     <?php echo isset($data['add_itineary']) ? $data['add_itineary'] : ''; ?>      </textarea>
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

<script>
  new FroalaEditor('#froala-editor');

  function toggleDivs() {
    // Get the value of the selected category
    const categoryValue = document.getElementById('categoryDropdown').value;
    console.log(' cccccccc', categoryValue)

    // Get the divs
    const bikeTripDiv = document.getElementById('bike_trips');
    const othersDiv = document.getElementById('otherss');

    // Show/hide divs based on selected value
    if (categoryValue === "1") {
      console.log('111111111111')
      bikeTripDiv.classList.remove('hiddenCategory');
      othersDiv.classList.add('hiddenCategory');
    } else {
      console.log('00000000000000000')
      bikeTripDiv.classList.add('hiddenCategory');
      othersDiv.classList.remove('hiddenCategory');
    }
  }
  toggleDivs();

  function calculateAgentPrices1() {
    // Get values from the input fields
    const commission = parseFloat(document.getElementById('agent_commision_1').value) || 0;
    const soloDoubleRoom = parseFloat(document.getElementById('solo_riding_on_double_room').value) || 0;
    const doubleDoubleRoom = parseFloat(document.getElementById('double_riding_on_double_room').value) || 0;
    const soloSingleRoom = parseFloat(document.getElementById('solo_riding_on_single_room').value) || 0;
    const doubleSingleRoom = parseFloat(document.getElementById('double_riding_on_single_room').value) || 0;
    const threeRider2Bike = parseFloat(document.getElementById('three_rider_2_bike_1_triple_room').value) || 0;
    const threeRider3Bike = parseFloat(document.getElementById('three_rider_3_bike_1_triple_room').value) || 0;

    // Calculate the prices after deducting the commission
    const soloDoubleRoomPrice = soloDoubleRoom - (soloDoubleRoom * (commission / 100));
    const doubleDoubleRoomPrice = doubleDoubleRoom - (doubleDoubleRoom * (commission / 100));
    const soloSingleRoomPrice = soloSingleRoom - (soloSingleRoom * (commission / 100));
    const doubleSingleRoomPrice = doubleSingleRoom - (doubleSingleRoom * (commission / 100));
    const threeRider2BikePrice = threeRider2Bike - (threeRider2Bike * (commission / 100));
    const threeRider3BikePrice = threeRider3Bike - (threeRider3Bike * (commission / 100));

    // Set the calculated prices in the corresponding table cells
    document.getElementById('agent_solo_riding_on_double_room').textContent = soloDoubleRoomPrice.toFixed(2);
    document.getElementById('agent_double_riding_on_double_room').textContent = doubleDoubleRoomPrice.toFixed(2);
    document.getElementById('agent_solo_riding_on_single_room').textContent = soloSingleRoomPrice.toFixed(2);
    document.getElementById('agent_double_riding_on_single_room').textContent = doubleSingleRoomPrice.toFixed(2);
    document.getElementById('agent_three_rider_2_bike_1_triple_room').textContent = threeRider2BikePrice.toFixed(2);
    document.getElementById('agent_three_rider_3_bike_1_triple_room').textContent = threeRider3BikePrice.toFixed(2);
  }

  function calculateSpclAgentPrices1() {
    // Get values from the input fields
    const commission = parseFloat(document.getElementById('spcl_agent_commision_1').value) || 0;
    const soloDoubleRoom = parseFloat(document.getElementById('solo_riding_on_double_room').value) || 0;
    const doubleDoubleRoom = parseFloat(document.getElementById('double_riding_on_double_room').value) || 0;
    const soloSingleRoom = parseFloat(document.getElementById('solo_riding_on_single_room').value) || 0;
    const doubleSingleRoom = parseFloat(document.getElementById('double_riding_on_single_room').value) || 0;
    const threeRider2Bike = parseFloat(document.getElementById('three_rider_2_bike_1_triple_room').value) || 0;
    const threeRider3Bike = parseFloat(document.getElementById('three_rider_3_bike_1_triple_room').value) || 0;

    // Calculate the prices after deducting the commission
    const soloDoubleRoomPrice = soloDoubleRoom - (soloDoubleRoom * (commission / 100));
    const doubleDoubleRoomPrice = doubleDoubleRoom - (doubleDoubleRoom * (commission / 100));
    const soloSingleRoomPrice = soloSingleRoom - (soloSingleRoom * (commission / 100));
    const doubleSingleRoomPrice = doubleSingleRoom - (doubleSingleRoom * (commission / 100));
    const threeRider2BikePrice = threeRider2Bike - (threeRider2Bike * (commission / 100));
    const threeRider3BikePrice = threeRider3Bike - (threeRider3Bike * (commission / 100));

    // Set the calculated prices in the corresponding table cells
    document.getElementById('spcl_agent_solo_riding_on_double_room').textContent = soloDoubleRoomPrice.toFixed(2);
    document.getElementById('spcl_agent_double_riding_on_double_room').textContent = doubleDoubleRoomPrice.toFixed(2);
    document.getElementById('spcl_agent_solo_riding_on_single_room').textContent = soloSingleRoomPrice.toFixed(2);
    document.getElementById('spcl_agent_double_riding_on_single_room').textContent = doubleSingleRoomPrice.toFixed(2);
    document.getElementById('spcl_agent_three_rider_2_bike_1_triple_room').textContent = threeRider2BikePrice.toFixed(2);
    document.getElementById('spcl_agent_three_rider_3_bike_1_triple_room').textContent = threeRider3BikePrice.toFixed(2);
  }

  function calculatePriceAgent2() {
    console.log('callagent2');
    // Get values from the input fields
    const commission = parseFloat(document.getElementById('agent_commision_2').value) || 0;
    const dblTwin = parseFloat(document.getElementById('dbl_twin').value) || 0;
    const singleSharing = parseFloat(document.getElementById('single_sharing').value) || 0;
    const extraBed18 = parseFloat(document.getElementById('extra_bed_18').value) || 0;
    const childNoBed = parseFloat(document.getElementById('child_no_bed').value) || 0;
    const childWithBed = parseFloat(document.getElementById('child_with_bed').value) || 0;

    // Calculate the values after deducting the commission
    const dblTwinPrice = dblTwin - (dblTwin * (commission / 100));
    const singleSharingPrice = singleSharing - (singleSharing * (commission / 100));
    const extraBed18Price = extraBed18 - (extraBed18 * (commission / 100));
    const childNoBedPrice = childNoBed - (childNoBed * (commission / 100));
    const childWithBedPrice = childWithBed - (childWithBed * (commission / 100));

    // Set the values in the corresponding fields
    document.getElementById('agent_dbl_twin').textContent = dblTwinPrice.toFixed(2);
    document.getElementById('agent_single_sharing').textContent = singleSharingPrice.toFixed(2);
    document.getElementById('agent_extra_bed_18').textContent = extraBed18Price.toFixed(2);
    document.getElementById('agent_child_no_bed').textContent = childNoBedPrice.toFixed(2);
    document.getElementById('agent_child_with_bed').textContent = childWithBedPrice.toFixed(2);
  }

  function calculatePriceSpclAgent2() {
    console.log('call spcl agent2');
    // Get values from the input fields
    const commission = parseFloat(document.getElementById('spcl_agent_commision_2').value) || 0;
    const dblTwin = parseFloat(document.getElementById('dbl_twin').value) || 0;
    const singleSharing = parseFloat(document.getElementById('single_sharing').value) || 0;
    const extraBed18 = parseFloat(document.getElementById('extra_bed_18').value) || 0;
    const childNoBed = parseFloat(document.getElementById('child_no_bed').value) || 0;
    const childWithBed = parseFloat(document.getElementById('child_with_bed').value) || 0;

    // Calculate the values after deducting the commission
    const dblTwinPrice = dblTwin - (dblTwin * (commission / 100));
    const singleSharingPrice = singleSharing - (singleSharing * (commission / 100));
    const extraBed18Price = extraBed18 - (extraBed18 * (commission / 100));
    const childNoBedPrice = childNoBed - (childNoBed * (commission / 100));
    const childWithBedPrice = childWithBed - (childWithBed * (commission / 100));

    // Set the values in the corresponding fields
    document.getElementById('spcl_agent_dbl_twin').textContent = dblTwinPrice.toFixed(2);
    document.getElementById('spcl_agent_single_sharing').textContent = singleSharingPrice.toFixed(2);
    document.getElementById('spcl_agent_extra_bed_18').textContent = extraBed18Price.toFixed(2);
    document.getElementById('spcl_agent_child_no_bed').textContent = childNoBedPrice.toFixed(2);
    document.getElementById('spcl_agent_child_with_bed').textContent = childWithBedPrice.toFixed(2);
  }
</script>

<script>
  let toggleOptBtn = document.querySelectorAll('.toggle-options');

  for (i = 0; i < toggleOptBtn.length; i++) {
    toggleOptBtn[i].addEventListener('click', function() {
      if (!this.parentNode.querySelector('.custom-dd-menu').classList.contains('display-block')) {
        this.parentNode.querySelector('.custom-dd-menu').classList.add('display-block');
      } else {
        this.parentNode.querySelector('.custom-dd-menu').classList.remove('display-block');
      }
    });
  }


  let addMoreBtn = document.getElementById('add-more-date');

  function addRow() {
    document.querySelector('#repeater-fields').insertAdjacentHTML(
      'beforeend',
      `<div class="row mb-3 repeat-box">
                    <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Tour Start Date</label>
                    <input class="form-control" name="tour_start_date[]" type="date" >
                    </div>
                    <div class="col-md">
                    <label class="form-label" for="basic-default-phone">Tour End Date</label>
                    <div class="col-md remove-more-btn" onclick="removeRow(this)">Remove</div>
                    <input class="form-control" name="tour_end_date[]" type="date" >
                    </div>
                </div>`
    )
  }
  addMoreBtn.addEventListener('click', addRow);

  function removeRow(input) {
    input.parentNode.parentNode.remove()
  }
</script>


<?php
if (!empty($id)) {
  echo "<script type='text/javascript'>
            window.onload = function() {
                calculateAgentPrices1();
                calculateSpclAgentPrices1();
                calculatePriceSpclAgent2();
                calculatePriceAgent2();
            };
          </script>";
}

include BASE_PATH . '/includes/footer.php';
?>