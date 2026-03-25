<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

// Get Input data from query string
$search_string = filter_input(INPUT_GET, 'search_string');

$order_by = filter_input(INPUT_GET, 'order_by');

// Get current page.
$page = filter_input(INPUT_GET, 'page');
if (!$page) {
    $page = 1;
}

if (!$order_by) {
    $order_by = 'desc';
}

//Get DB instance. i.e instance of MYSQLiDB Library
$db = getDbInstance();
$select = array('id', 'name');

// If search string
if ($search_string) {
    $db->where("name", '%' . $search_string . '%', 'like');
}

//If order by option selected
if ($order_by) {
    $db->orderBy("name", $order_by);
}

// Set pagination limit
$db->pageLimit = PAGE_LIMIT;
$db->where('status', 'Active');


// Get result of the query.
$rows = $db->arraybuilder()->paginate('flight_destination', $page, $select);
$total_pages = $db->totalPages;

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
                    <h4 class="py-3 mb-4">View Destination List</h4>
                </div>
                <div class="col-auto">
                    <a href="add-flight-destination.php" class="btn btn-primary">Add Flight Destination</a>
                </div>
            </div>
            <!-- Basic Layout -->
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="table-responsive text-nowrap border-light border-solid mb-3">
                                <table class="table">
                                    <thead>
                                        <tr class="text-nowrap bg-dark align-middle">
                                            <th class="text-white border-right-white">Destination ID</th>
                                            <th class="text-white border-right-white">Destination Name</th>
                                            <th class="text-white border-right-white">Edit Details</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        <?php
                                        $k = ($page != 1) ? (($page - 1) * PAGE_LIMIT) + 1 : 1;
                                        foreach ($rows as $row) :

                                        ?>
                                            <tr>
                                                <td class="border-right-dark">#<?php echo $k; ?></td>
                                                <td class="border-right-dark"><?php echo xss_clean($row['name']); ?></td>
                                                <td class="border-right-dark"><a href="add-flight-destination.php?id=<?php echo encryptId($row['id']); ?>">Edit Details</a></td>
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