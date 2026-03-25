<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$db = getDbInstance();
$db->orderBy("name", "asc");
$categoryData = $db->get("category");

$db = getDbInstance();
$db->orderBy("name", "asc");
$destinationData = $db->get("destination");

// Get Input data from query string
$search_string = filter_input(INPUT_GET, 'search_string');
$filter_col = filter_input(INPUT_GET, 'filter_col');
$order_by = filter_input(INPUT_GET, 'order_by');

// Get current page.
$page = filter_input(INPUT_GET, 'page');
if (!$page) {
  $page = 1;
}

if (!$filter_col) {
  $filter_col = 'id';
}
if (!$order_by) {
  $order_by = 'desc';
}

//Get DB instance. i.e instance of MYSQLiDB Library
$db = getDbInstance();
// $select = array('id', 'package_code', 'package_name', 'permit', 'guide', 'duration',  'status', 'created_at', 'updated_at');

// If search string
if ($search_string) {
  $db->where("$filter_col", '%' . $search_string . '%', 'like');
}

//If order by option selected
if ($order_by) {
  $db->orderBy($filter_col, $order_by);
}

// Set pagination limit
$db->pageLimit = PAGE_LIMIT;
$db->where('status', 'Active');


// Get result of the query.
$rows = $db->arraybuilder()->paginate('fixed_package', $page);
$total_pages = $db->totalPages;

// Function to get the name by id
function getNameById($array, $id)
{
  foreach ($array as $item) {
    if ($item['id'] == $id) {
      return $item['name'];
    }
  }
  return null; // Return null if no matching ID is found
}

include BASE_PATH . '/includes/header.php';
?>

<!-- Layout container -->
<div class="layout-page">

  <!-- Content wrapper -->
  <div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
      <div class="card mb-4">
        <div class="card-body">
          <form>
            <div class="row mb-3">
              <div class="col-md-5">
                <label class="form-label">Search by</label>
                <div class="input-group">
                  <label class="input-group-text">Options</label>
                  <select class="form-select" name="filter_col">
                    <option selected="">Choose...</option>
                    <option value="id">Product ID</option>
                    <option value="name">Product Name</option>
                    <option value="tour_category">Tour Category</option>
                  </select>
                </div>
              </div>
              <div class="col-md-5">
                <label class="form-label">Enter Text</label>
                <input class="form-control" name="search_string" type="text" placeholder="Search...">
              </div>
              <div class="col-md">
                <label class="form-label" style="display: block;">&nbsp;</label>
                <button type="submit" class="btn btn-primary">Search</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <h4 class="py-3 mb-4">View Package List</h4>

      <!-- Basic Layout -->
      <div class="row">
        <div class="col-xl">
          <div class="card mb-4">
            <div class="card-body">
              <div class="table-responsive text-nowrap border-light border-solid mb-3">
                <table class="table">
                  <thead>
                    <tr class="text-nowrap bg-dark align-middle">
                      <th class="text-white border-right-white">Product ID</th>
                      <th class="text-white border-right-white">Product Name</th>
                      <th class="text-white border-right-white">Tour Start</th>
                      <th class="text-white border-right-white">Tour End</th>
                      <th class="text-white border-right-white">Tour Start Date</th>
                      <th class="text-white border-right-white">Tour End Date</th>
                      <th class="text-white border-right-white">Tour Category</th>
                      <th class="text-white border-right-white">Inventory</th>
                      <th class="text-white border-right-white">Website Link</th>
                      <th class="text-white border-right-white">PDF Link</th>
                      <th class="text-white border-right-white">Edit</th>
                    </tr>
                  </thead>
                  <tbody class="table-border-bottom-0">
                    <?php
                    $k = ($page != 1) ? (($page - 1) * PAGE_LIMIT) + 1 : 1;
                    foreach ($rows as $row) :

                    ?>
                      <tr>
                        <td class="border-right-dark">#<?php echo xss_clean($row['id']); ?></td>
                        <td class="border-right-dark"><?php echo xss_clean($row['name']); ?></td>
                        <td class="border-right-dark"><?php echo xss_clean(getNameById($destinationData, $row['tour_start'])); ?></td>
                        <td class="border-right-dark"><?php echo xss_clean(getNameById($destinationData, $row['tour_end'])); ?></td>
                        <td class="border-right-dark"><?php echo xss_clean($row['tour_start_date']); ?></td>
                        <td class="border-right-dark"><?php echo xss_clean($row['tour_end_date']); ?></td>
                        <td class="border-right-dark"><?php echo xss_clean(getNameById($categoryData, $row['tour_category'])); ?></td>
                        <td class="border-right-dark"><?php echo $row['add_itineary']; ?></td>
                        <td class="border-right-dark"><?php echo xss_clean($row['website_link']); ?></td>
                        <td class="border-right-dark"><?php echo xss_clean($row['pdf_link']); ?></td>
                        <td class="border-right-dark"><a href="add-fixed-package.php?id=<?php echo encryptId($row['id']); ?>">Edit Details</a></td>
                      </tr>
                    <?php
                      $k++;
                    endforeach;
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- / Content -->
  </div>
</div>
</div>
</div>
<script src="../assets/js/custom-script.js"></script>
</body>

</html>