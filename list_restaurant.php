<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$pid = decryptId($_GET['pid'] ?? '');
$db = getDbInstance();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $db->where('id', $id);
    if ($db->delete('restaurants')) {
        $_SESSION['success'] = 'Restaurant deleted successfully!';
    } else {
        $_SESSION['failure'] = 'Failed to delete restaurant';
    }
    header('Location: list_restaurant.php?pid=' . encryptId($pid));
    exit;
}

// Get filter parameters
$filter_col = $_GET['filter_col'] ?? 'name';
$search_string = $_GET['search_string'] ?? '';

// Validate filter column
$allowed_columns = ['name', 'manager_name', 'chef_name', 'contact_number'];
if (!in_array($filter_col, $allowed_columns)) {
    $filter_col = 'name';
}

// Build the query
$db->where('property_id', $pid);

// Apply search filter if search string is provided
if (!empty($search_string)) {
    $db->where($filter_col, '%' . $search_string . '%', 'LIKE');
}

// Get pagination parameters
$page = $_GET['page'] ?? 1;
$page = max(1, (int)$page); // Ensure page is at least 1
$records_per_page = 10;
$db->pageLimit = $records_per_page;

// Fetch paginated restaurants
$restaurants = $db->arraybuilder()->paginate('restaurants', $page);

// Get total pages for pagination
$total_pages = $db->totalPages;

include BASE_PATH . '/includes/header.php';
?>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <form method="get" action="">
                <input type="hidden" name="pid" value="<?= htmlspecialchars($_GET['pid']) ?>">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-5">
                                <label class="form-label">Search by</label>
                                <div class="input-group">
                                    <label class="input-group-text">Options</label>
                                    <select class="form-select" name="filter_col">
                                        <option value="name" <?= ($filter_col == 'name') ? 'selected' : '' ?>>Restaurant Name</option>
                                        <option value="manager_name" <?= ($filter_col == 'manager_name') ? 'selected' : '' ?>>Manager Name</option>
                                        <option value="chef_name" <?= ($filter_col == 'chef_name') ? 'selected' : '' ?>>Chef Name</option>
                                        <option value="contact_number" <?= ($filter_col == 'contact_number') ? 'selected' : '' ?>>Contact Number</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Enter Text</label>
                                <input class="form-control" name="search_string" type="text" placeholder="Search..." value="<?= htmlspecialchars($search_string) ?>">
                            </div>
                            <div class="col-md d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Search</button>
                                <a href="list_restaurant.php?pid=<?= encryptId($pid) ?>" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="py-3 mb-0">Restaurant List</h4>
                <a href="add_restaurant.php?pid=<?= encryptId($pid) ?>" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Add Restaurant
                </a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success'];
                    unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['failure'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['failure'];
                    unset($_SESSION['failure']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Manager</th>
                                    <th>Chef</th>
                                    <th>Contact</th>
                                    <th>Logo</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($restaurants)): ?>
                                    <?php $i = 1 + (($page - 1) * $records_per_page);
                                    foreach ($restaurants as $restaurant): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= htmlspecialchars($restaurant['name']) ?></td>
                                            <td><?= htmlspecialchars($restaurant['manager_name']) ?></td>
                                            <td><?= htmlspecialchars($restaurant['chef_name']) ?></td>
                                            <td><?= htmlspecialchars($restaurant['contact_number']) ?></td>
                                            <td>
                                                <?php if (!empty($restaurant['logo'])): ?>
                                                    <img src="<?= htmlspecialchars($restaurant['logo']) ?>" alt="Logo" style="width: 107px;"  class="h-10 w-10 rounded-full object-cover">
                                                <?php else: ?>
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <span class="text-xs text-gray-500">No Logo</span>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="add_restaurant.php?pid=<?= encryptId($pid) ?>&id=<?= encryptId($restaurant['id']) ?>" class="btn btn-sm btn-info" title="Edit">
                                                    <i class="fas fa-edit"></i>Edit
                                                </a>
                                                <form action="" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this restaurant?');">
                                                    <input type="hidden" name="delete_id" value="<?= $restaurant['id'] ?>">
                                                    <button class="btn btn-sm btn-danger" type="submit" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No restaurants found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end mt-3">
                        <?= paginationLinks($page, $total_pages, 'list_restaurant.php') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>