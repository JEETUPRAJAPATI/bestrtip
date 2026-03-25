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

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database Instance
$db = getDbInstance();

// Handle POST and PUT Requests for creating/updating overview section
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

    $id = (isset($_GET['id']) && !empty($_GET['id'])) ? decryptId($_GET['id']) : "";

    if ($_POST['_method'] === 'PUT' && $id) {
        // echo "<pre>";
        // print_r($_POST);
        // echo $_SERVER['REQUEST_METHOD'];
        // echo "</pre>";
        // die();
        // Update overview_section
        $data = ['description' => $description];
        $db->where('id', $id);
        $isUpdated = $db->update('overview_section', $data);

        if ($isUpdated) {
            // Handle image updates for editing
            $target_dir = "uploads/";
            $image_files = $_FILES['images'];

            if (isset($image_files) && !empty($image_files['name'][0])) {
                foreach ($image_files['name'] as $key => $name) {
                    $tmp_name = $image_files['tmp_name'][$key];
                    $size = $image_files['size'][$key];
                    $error = $image_files['error'][$key];

                    if ($error === UPLOAD_ERR_OK && $size <= 10000000) {
                        $target_file = $target_dir . time() . "_" . basename($name);
                        if (move_uploaded_file($tmp_name, $target_file)) {
                            // Save new image path to overview_section_images
                            $imageData = [
                                'overview_section_id' => $id,
                                'image_path' => $target_file,
                            ];
                            $db->insert('overview_section_images', $imageData);
                        }
                    }
                }
            }

            $message = "Overview section updated successfully!";
            echo "<script>alert('$message'); window.location.href = 'view-overview-section.php';</script>";
        } else {
            echo "<script>alert('Error updating overview section!');</script>";
        }
    } elseif ($_POST['_method']  === 'POST') {
        // Insert new overview_section
        $data = ['description' => $description];
        $sectionId = $db->insert('overview_section', $data);

        if ($sectionId) {
            // Handle multiple image uploads for new section
            $target_dir = "uploads/";
            $image_files = $_FILES['images'];

            if (isset($image_files) && !empty($image_files['name'][0])) {
                foreach ($image_files['name'] as $key => $name) {
                    $tmp_name = $image_files['tmp_name'][$key];
                    $size = $image_files['size'][$key];
                    $error = $image_files['error'][$key];

                    if ($error === UPLOAD_ERR_OK && $size <= 10000000) {
                        $target_file = $target_dir . time() . "_" . basename($name);
                        if (move_uploaded_file($tmp_name, $target_file)) {
                            // Save image path to overview_section_images
                            $imageData = [
                                'overview_section_id' => $sectionId,
                                'image_path' => $target_file,
                            ];
                            $db->insert('overview_section_images', $imageData);
                        }
                    }
                }
            }

            $message = "Overview section created successfully!";
            echo "<script>alert('$message'); window.location.href = 'view-overview-section.php';</script>";
        } else {
            echo "<script>alert('Error creating overview section!');</script>";
        }
    }
}

// Fetch data for edit (if PUT request with ID)
$id = (isset($_GET['id']) && !empty($_GET['id'])) ? decryptId($_GET['id']) : "";
$edit = false;

if (!empty($id)) {
    $edit = true;
    $db->where('id', $id);
    $data = $db->getOne("overview_section");

    // Get associated images
    $db->where('overview_section_id', $id);
    $images = $db->get("overview_section_images");
}

?>

<!-- Frontend Form -->
<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4"><?= $edit ? 'Edit' : 'Add' ?> Overview Section</h4>
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <?php if ($edit): ?>
                            <input type="hidden" name="_method" value="PUT">
                        <?php endif; ?>
                        <div class="row mb-3">
                            <div class="col-md">
                                <label class="form-label">Product Description</label>
                                <textarea class="form-control" name="description" rows="10"><?= isset($data['description']) ? $data['description'] : ''; ?></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md">
                                <label class="form-label">Upload Images</label>
                                <input type="file" name="images[]" class="form-control" multiple />
                                <?php if ($edit && isset($images)): ?>
                                    <div class="mt-3">
                                        <?php foreach ($images as $image): ?>
                                            <div style="display: inline-block; margin-right: 10px; text-align: center;">
                                                <img src="<?= $image['image_path']; ?>" height="100px" width="100px" style="display: block;" />
                                                <a href="delete-image.php?id=<?= $image['id']; ?>&section_id=<?= $id; ?>" class="btn btn-sm btn-danger mt-1">Delete</a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><?= $edit ? 'Update' : 'Save' ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>