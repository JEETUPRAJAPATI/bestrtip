<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = getDbInstance();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_to_store = array_filter($_POST);
    $title = $data_to_store['title'] ?? '';
    $description = $data_to_store['description'] ?? '';
    $blog_category = $data_to_store['blog_category'] ?? '';
    $author_name = $data_to_store['author_name'] ?? '';

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $msg = "edited";
        $db->where('id', $_POST['id']);
        $blog_id = $db->update('blogs', $data_to_store);
    } else {
        $msg = "added";
        $blog_id = $db->insert('blogs', $data_to_store);
    }

    if ($blog_id) {
        // Handle image uploads
        $target_dir = "uploads/blogs/";

        // Ensure the directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Check if an image file is uploaded
        $image_file = $_FILES['image'] ?? null;

        if ($image_file && !empty($image_file['name'])) {
            $tmp_name = $image_file['tmp_name'];
            $size = $image_file['size'];
            $error = $image_file['error'];

            // Validate the image
            if (
                $error === UPLOAD_ERR_OK && $size <= 10000000
            ) {
                $target_file = $target_dir . time() . "_" . basename($image_file['name']);

                // Move the uploaded file to the target directory
                if (move_uploaded_file(
                    $tmp_name,
                    $target_file
                )) {
                    // Remove the old image if it exists
                    $db->where('id', $blog_id);
                    $oldImage = $db->getValue('blogs', 'image');

                    if ($oldImage && file_exists($oldImage)) {
                        unlink($oldImage); // Delete old image file from the server
                    }

                    // Update the blog table with the new image path
                    $updateData = ['image' => $target_file];
                    $db->where('id', $blog_id);
                    $updated = $db->update(
                        'blogs',
                        $updateData
                    );

                    if (!$updated) {
                        die("Image update failed: " . $db->getLastError());
                    }
                }
            }
        }

        $_SESSION['success'] = "Blog $msg successfully!";
        header('location: view-blog.php');
        exit();
    } else {
        echo 'Insert failed: ' . $db->getLastError();
        exit();
    }
}

$id = isset($_GET['id']) && !empty($_GET['id']) ? decryptId($_GET['id']) : "";
$edit = false;
if (!empty($id)) {
    $edit = true;
    $db->where('id', $id);
    $data = $db->getOne("blogs");
}



include BASE_PATH . '/includes/header.php';
?>
<style>
    .select2-selection__choice {
        background-color: #007bff !important;
        color: #fff !important;
        padding: 6px 12px !important;
        border-radius: 15px !important;
        display: flex !important;
        align-items: center !important;
        font-size: 14px !important;
        margin: 4px !important;
    }
</style>
<div class="layout-page">


    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Blog/</span> <?= $edit ? 'Edit' : "Add" ?> Blog</h4>
            <h4 class="py-3 mb-4">Add Blog</h4>
            <!-- Basic Layout -->
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="" method="post" id="hotel_form" enctype="multipart/form-data">


                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-company">Blog Name</label>
                                    <input type="text" class="form-control" name="title" value="<?php echo xss_clean($edit ? $data['title'] : ''); ?>" required />
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md">
                                        <label class="form-label" for="basic-default-phone">Blog Category</label>
                                        <input type="text" id="basic-default-phone" class="form-control phone-mask" name="category" value="<?php echo xss_clean($edit ? $data['category'] : ''); ?>" required />
                                    </div>
                                    <div class="col-md">
                                        <label class="form-label" for="basic-default-phone">Author Name</label>
                                        <input type="text" id="basic-default-phone" class="form-control phone-mask" name="author_name" value="<?php echo xss_clean($edit ? $data['author_name'] : ''); ?>" required />
                                    </div>
                                </div>

                                <!-- Frontend Form -->
                                <div class="content-wrapper">
                                    <div class="flex-grow-1 container-p-y">
                                        <div class="row mb-3">
                                            <div class="col-md">
                                                <label class="form-label">Blog Description</label>
                                                <textarea class="form-control" name="description" rows="10"><?= isset($data['description']) ? $data['description'] : ''; ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md">
                                                <label class="form-label">Upload Image</label>
                                                <input type="file" name="image" class="form-control" accept="image/*" />

                                                <?php if ($edit && isset($image)): ?>
                                                    <div class="mt-3">
                                                        <img src="<?= $image['image']; ?>" height="100px" width="100px" style="display: block;" />
                                                        <a href="delete-image.php?id=<?= $image['id']; ?>&section_id=<?= $id; ?>" class="btn btn-sm btn-danger mt-1">Delete</a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Overview Section -->

                                <input type="hidden" name="id" value="<?php echo $id ?>" />
                                <button type="submit" class="btn btn-primary">SAVE</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
</div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $("#agents").select2({
            placeholder: "Choose agents...",
            allowClear: true
        });
    });
</script>
<?php include BASE_PATH . '/includes/footer.php'; ?>