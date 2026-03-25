<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Get Input data
$search_string = filter_input(INPUT_GET, 'search_string');
$filter_col = filter_input(INPUT_GET, 'filter_col') ?? 'id';
$order_by = filter_input(INPUT_GET, 'order_by') ?? 'desc';
$page = filter_input(INPUT_GET, 'page') ?? 1;

// Get DB instance
$db = getDbInstance();
$select = ['id', 'title', 'author_name', 'status', 'created_at'];

// Search functionality
if ($search_string) {
    $db->where("$filter_col", '%' . $search_string . '%', 'like');
}

// Sorting
$db->orderBy($filter_col, $order_by);

// Pagination
$db->pageLimit = PAGE_LIMIT;
$blogs = $db->arraybuilder()->paginate('blogs', $page, $select);
$total_pages = $db->totalPages;

include BASE_PATH . '/includes/header.php';
?>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <form>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Search By</label>
                                <select class="form-select" name="filter_col">
                                    <option value="title" <?= $filter_col == 'title' ? 'selected' : '' ?>>Title</option>
                                    <option value="author_name" <?= $filter_col == 'author_name' ? 'selected' : '' ?>>Author</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Enter Text</label>
                                <input class="form-control" name="search_string" type="text" placeholder="Search..." value="<?= $search_string ?>">
                            </div>

                            <div class="col-md">
                                <label class="form-label" style="display: block;">&nbsp;</label>
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <?php include BASE_PATH . '/includes/flash_messages.php'; ?>

            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Blog List</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($blogs): ?>
                                    <?php $count = ($page - 1) * PAGE_LIMIT + 1; ?>
                                    <?php foreach ($blogs as $blog): ?>
                                        <tr>
                                            <td><?= $count++ ?></td>
                                            <td><?= $blog['title'] ?></td>
                                            <td><?= $blog['author_name'] ?></td>
                                            <td><?= $blog['status'] ?></td>
                                            <td><?= date('Y-m-d', strtotime($blog['created_at'])) ?></td>

                                            <td class="border-right-dark">
                                                <div class="dropdown pos-relative">
                                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow toggle-options">
                                                        <i class="bx bx-dots-vertical-rounded"><span></span></i>
                                                    </button>
                                                    <div class="dropdown-menu custom-dd-menu">
                                                        <a href="add-blog.php?id=<?= encryptId($blog['id']) ?>" class="dropdown-item">Edit</a>
                                                        <a href="delete_blog.php?id=<?= encryptId($blog['id']) ?>" class="dropdown-item" onclick="return confirm('Are you sure?')">Delete</a>

                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No blogs found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="text-end">
                        <?= paginationLinks($page, $total_pages, 'blogs.php') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>