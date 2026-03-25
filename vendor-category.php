<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Get Input data
$search_string = filter_input(INPUT_GET, 'search_string');
$filter_col = filter_input(INPUT_GET, 'filter_col') ?? 'name';
$order_by = filter_input(INPUT_GET, 'order_by') ?? 'desc';
$page = filter_input(INPUT_GET, 'page') ?? 1;

// Get DB instance
$db = getDbInstance();
$select = [
    'id',
    'name'
];

// Search functionality
if ($search_string) {
    $db->where($filter_col, '%' . $search_string . '%', 'like');
}
// Sorting
$db->orderBy($filter_col, $order_by);
// Pagination
$vendors = $db->arraybuilder()->paginate('vendor_category', $page, $select);
$total_pages = $db->totalPages;

include BASE_PATH . '/includes/header.php';
?>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Page Title & Add Button -->
            <div class="row">
                <div class="col">
                    <h4 class="py-3 mb-4">Vendor Category List</h4>
                </div>
                <div class="col-auto">
                    <a href="add-vendor-category.php" class="btn btn-primary">Add Category</a>
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
                                    <th>Category</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($vendors): ?>
                                    <?php $count = ($page - 1) * PAGE_LIMIT + 1; ?>
                                    <?php foreach ($vendors as $vendor): ?>
                                        <tr>
                                            <td><?= $count++ ?></td>
                                            <td><?= htmlspecialchars($vendor['name']) ?></td>
                                            <td>
                                                <a href="add-vendor-category.php?id=<?= encryptId($vendor['id']) ?>" class="dropdown-item">Edit</a>
                                                <a href="delete-vendor-category.php?id=<?= encryptId($vendor['id']) ?>" class="dropdown-item" onclick="return confirm('Are you sure you want to delete this vendor?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No category found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="text-end mt-3">
                        <?= paginationLinks($page, $total_pages, 'category.php') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>