<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Get input data safely
$search_string = filter_input(INPUT_GET, 'search_string', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$filter_col = filter_input(INPUT_GET, 'filter_col', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$order_by = filter_input(INPUT_GET, 'order_by', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

// Set defaults
$page = $page ?: 1;
$filter_col = !empty($filter_col) ? "f.$filter_col" : "f.id"; // Default column
$order_by = ($order_by === 'ASC') ? 'ASC' : 'DESC'; // Default to DESC

$db = getDbInstance();

// **Apply Search Filter**
if (!empty($search_string)) {
  $db->where($filter_col, '%' . $search_string . '%', 'LIKE');
}

// **Ensure $filter_col is valid**
$valid_columns = ['f.id', 'f.name', 'f.price', 'f.date']; // Allowed columns
if (!in_array($filter_col, $valid_columns)) {
  $filter_col = 'f.id'; // Default if invalid
}

// **Apply Joins**
$db->join("flight_destination d", "d.id = f.destination", "LEFT");
$db->join("flight_destination tf", "tf.id = f.from", "LEFT");

// **Order Results**
$db->orderBy($filter_col, $order_by);

// **Fetch Data with Pagination**
$rows = $db->paginate('flight_lists f', $page, 'f.*, d.name AS destination_name, tf.name AS from_name');
$total_pages = $db->totalPages;

// **Fetch Flight Details Separately**
$flight_details = [];
$flight_ids = array_column($rows, 'id'); // Extract flight IDs
if (!empty($flight_ids)) {
  $db->where('flight_id', $flight_ids, 'IN');
  $details = $db->get('flight_details');

  foreach ($details as $detail) {
    $flight_details[$detail['flight_id']][] = $detail;
  }
}
// echo "<pre>";
// print_r($flight_details);
// die();
include BASE_PATH . '/includes/header.php';
?>




<div class="layout-page">
  <div class="content-wrapper">
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
                    <option value="id">Flight ID</option>
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
      <div class="row">
        <div class="col">
          <h4 class="py-3 mb-4">View Flight List</h4>
        </div>
        <div class="col-auto">
          <a href="flight-booking.php" class="btn btn-primary">Add Flight</a>
        </div>
      </div>
      <div class="row">
        <div class="col-xl">
          <div class="card mb-4">
            <div class="card-body">
              <div class="table-responsive text-nowrap border-light border-solid mb-3">
                <table class="table">
                  <thead>
                    <tr class="text-nowrap bg-dark align-middle">
                      <th class="text-white">ID</th>
                      <th class="text-white">Flight Logo</th>
                      <th class="text-white">From</th>
                      <th class="text-white">Destination</th>
                      <th class="text-white">Summary</th>
                      <th class="text-white">Status</th>
                      <th class="text-white">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $p = 1;
                    foreach ($rows as $row) :
                    ?>
                      <tr>
                        <td>#<?php echo $p++; ?></td>

                        <!-- Flight Logo -->
                        <td>
                          <?php if (!empty($row['flight_logo'])) : ?>
                            <img src="<?php echo htmlspecialchars($row['flight_logo'], ENT_QUOTES, 'UTF-8'); ?>" alt="Flight Logo" width="50">
                          <?php else : ?>
                            N/A
                          <?php endif; ?>
                        </td>

                        <td><?php echo xss_clean($row['from_name']); ?></td>
                        <td><?php echo xss_clean($row['destination_name']); ?></td>

                        <!-- Summary -->
                        <td>
                          <ul class="list-unstyled">
                            <?php if (!empty($flight_details[$row['id']])) : ?>
                              <?php foreach ($flight_details[$row['id']] as $detail) : ?>
                                <li>
                                  <strong>Flight Number:</strong> <?php echo htmlspecialchars($detail['flight_number'], ENT_QUOTES, 'UTF-8'); ?><br>
                                  <strong>Departure:</strong> <?php echo date("d M Y, h:i A", strtotime($detail['departure_datetime'])); ?><br>
                                  <strong>Arrival:</strong> <?php echo date("d M Y, h:i A", strtotime($detail['arrival_datetime'])); ?><br>
                                  <strong>Price:</strong> ₹<?php echo number_format($detail['price'], 2); ?>
                                </li>
                                <hr>
                              <?php endforeach; ?>
                            <?php else : ?>
                              No flight details available
                            <?php endif; ?>
                          </ul>
                        </td>

                        <!-- Status -->
                        <td>
                          <?php echo $row['status'] === 'Active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?>
                        </td>

                        <!-- Actions -->
                        <td>
                          <a href="flight-booking.php?id=<?php echo encryptId($row['id']); ?>">Edit</a><br>
                          <a href="delete-flight.php?id=<?php echo encryptId($row['id']); ?>" onclick="return confirm('Are you sure?')">Delete</a><br>
                          <a href="duplicate-flight.php?crm=<?php echo encryptId($row['id']); ?>" onclick="return confirm('Duplicate this record?')">Duplicate</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> <!-- /Content -->
</div>
<script src="../assets/js/custom-script.js"></script>
</body>

</html>