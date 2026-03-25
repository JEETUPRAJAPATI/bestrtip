<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Get Input data
$search_string = filter_input(INPUT_GET, 'search_string');
$filter_col = filter_input(INPUT_GET, 'filter_col') ?? 'vendor.name';
$order_by = filter_input(INPUT_GET, 'order_by') ?? 'desc';
$page = filter_input(INPUT_GET, 'page') ?? 1;

// Get DB instance
$db = getDbInstance();
$select = [
    'vendor.id',
    'vendor.email',
    'vendor.mobile',
    'vendor.name AS vendor_name',
    'vendor_category.name AS category_name',
    'vendor.created_at'
];

// JOIN
$db->join('vendor_category', 'vendor.category_id = vendor_category.id', 'LEFT');


// Search functionality
if ($search_string) {
    $db->where($filter_col, '%' . $search_string . '%', 'like');
}
// Sorting
$db->orderBy($filter_col, $order_by);
// Pagination
$vendors = $db->arraybuilder()->paginate('vendor', $page, $select);
$total_pages = $db->totalPages;

include BASE_PATH . '/includes/header.php';
?>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Search Form -->
            <form method="get">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Search By</label>
                                <select class="form-select" name="filter_col">
                                    <option value="vendor.name" <?= $filter_col == 'vendor.name' ? 'selected' : '' ?>>Vendor Name</option>
                                    <option value="vendor_category.name" <?= $filter_col == 'vendor_category.name' ? 'selected' : '' ?>>Category Name</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Enter Text</label>
                                <input class="form-control" name="search_string" type="text" placeholder="Search...">
                            </div>
                            <div class="col-md">
                                <label class="form-label" style="display: block;">&nbsp;</label>
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Page Title & Add Button -->
            <div class="row">
                <div class="col">
                    <h4 class="py-3 mb-4">Vendor List</h4>
                </div>
                <div class="col-auto">
                    <a href="vendor-category.php" class="btn btn-primary">Category</a>
                    <a href="add-vendor.php" class="btn btn-primary">Add Vendor</a>
                </div>
            </div>

            <?php include BASE_PATH . '/includes/flash_messages.php'; ?>

            <!-- Vendor Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Vendor Name</th>
                                    <th>Vendor Email</th>
                                    <th>Vendor Mobile</th>

                                    <th>Vendor Category</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($vendors): ?>
                                    <?php $count = ($page - 1) * PAGE_LIMIT + 1; ?>
                                    <?php foreach ($vendors as $vendor): ?>
                                        <tr>
                                            <td><?= $count++ ?></td>
                                            <td><?= htmlspecialchars($vendor['vendor_name']) ?></td>
                                            <td><?= isset($vendor['category_name']) ? $vendor['category_name'] : '-' ?></td>
                                            <td><?= !empty($vendor['email']) ? $vendor['email'] : '-' ?></td>
                                            <td><?= !empty($vendor['mobile']) ? '+91' . $vendor['mobile'] : '-' ?></td>

                                            <td><?= date('Y-m-d', strtotime($vendor['created_at'])) ?></td>
                                            <td>
                                                <a href="add-vendor.php?id=<?= encryptId($vendor['id']) ?>" class="dropdown-item">Edit</a>
                                                <a href="delete_vendor.php?id=<?= encryptId($vendor['id']) ?>" class="dropdown-item" onclick="return confirm('Are you sure you want to delete this vendor?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No Vendors found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="text-end mt-3">
                        <?= paginationLinks($page, $total_pages, 'vendor.php') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>