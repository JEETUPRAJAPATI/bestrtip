<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

// Get Input data from query string
$search_string = filter_input(INPUT_GET, 'search_string');
$filter_col = filter_input(INPUT_GET, 'filter_col');
$order_by = filter_input(INPUT_GET, 'order_by');
$page = filter_input(INPUT_GET, 'page');
if (!$page) $page = 1;

if (!$filter_col) $filter_col = 'id';
if (!$order_by) $order_by = 'desc';

$db = getDbInstance();
$select = array('id', 'unique_id', 'hotel_name', 'reservation_manager', 'email', 'mobile_no', 'created_at');

if ($search_string) {
    $db->where($filter_col, '%' . $search_string . '%', 'like');
}

if ($order_by) {
    $db->orderBy($filter_col, $order_by);
}

$db->pageLimit = PAGE_LIMIT;
$rows = $db->arraybuilder()->paginate('properties', $page, $select);
$total_pages = $db->totalPages;

include BASE_PATH . '/includes/header.php';
?>
<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <form method="get" action="">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <label class="form-label">Search by</label>
                                <div class="input-group">
                                    <label class="input-group-text">Options</label>
                                    <select class="form-select" name="filter_col">
                                        <option value="hotel_name" <?= ($filter_col == 'hotel_name') ? 'selected' : '' ?>>Hotel Name</option>
                                        <option value="reservation_manager" <?= ($filter_col == 'reservation_manager') ? 'selected' : '' ?>>Reservation Manager</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Enter Text</label>
                                <input class="form-control" name="search_string" type="text" placeholder="Search..." value="<?= $search_string ?>">
                            </div>
                            <div class="col-md d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Search</button>
                                <a href="property_list.php" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col">
                    <h4 class="py-3 mb-4">View Property Information</h4>
                </div>
                <div class="col-auto">
                    <a href="add_property.php" class="btn btn-primary">Add Property</a>
                </div>
            </div>
            <?php include BASE_PATH . '/includes/flash_messages.php'; ?>
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="table-responsive text-nowrap border-light border-solid mb-3">
                                <table class="table">
                                    <thead>
                                        <tr class="text-nowrap bg-dark align-middle">
                                            <th class="text-white">#</th>
                                            <th class="text-white">Unique Id</th>
                                            <th class="text-white">Hotel Name</th>
                                            <th class="text-white">Reservation Manager</th>
                                            <th class="text-white">Email</th>
                                            <th class="text-white">Mobile No</th>
                                            <th class="text-white">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $k = ($page != 1) ? (($page - 1) * PAGE_LIMIT) + 1 : 1; ?>
                                        <?php foreach ($rows as $row): ?>
                                            <tr>
                                                <td><?= $k ?></td>
                                                <td><?= xss_clean($row['unique_id']) ?></td>
                                                <td><?= xss_clean($row['hotel_name']) ?></td>
                                                <td><?= xss_clean($row['reservation_manager']) ?></td>
                                                <td><?= xss_clean($row['email']) ?></td>
                                                <td><?= xss_clean($row['mobile_no']) ?></td>
                                                <td>
                                                    <a class="btn btn-sm btn-warning" href="add_property.php?crm=<?= encryptId($row['id']) ?>">Edit</a>
                                                    <a class="btn btn-sm btn-danger" href="delete_property.php?crm=<?= encryptId($row['id']) ?>" onclick="return confirm('Are you sure?')">Delete</a>
                                                    <a class="btn btn-sm btn-primary" href="view_property_summary.php?crm=<?= encryptId($row['id']) ?>">
                                                        View Summary
                                                    </a>
                                                    <a class="btn btn-sm btn-info" href="view_property_calendar.php?crm=<?= encryptId($row['id']) ?>">
                                                        View Calendar
                                                    </a>
                                                    <a class="btn btn-sm btn-success" href="restaurant_dashboard.php?pid=<?= encryptId($row['id']) ?>">
                                                        <i class="bx bx-restaurant"></i> Manage Restaurant
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php $k++;
                                        endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <?= paginationLinks($page, $total_pages, 'property_list.php') ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include BASE_PATH . '/includes/footer.php'; ?>