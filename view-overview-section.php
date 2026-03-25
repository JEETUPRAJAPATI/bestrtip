<style>
    /* Notification styling */
    .notification {
        margin-top: 10px;
        background-color: #007bff;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-size: 14px;
        font-weight: bold;
        display: none;
        z-index: 9999;
        opacity: 0;
        transform: translateY(-20px);
        transition: opacity 0.5s ease, transform 0.3s ease;
    }

    .notification.show {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    .notification.hide {
        opacity: 0;
        transform: translateY(-20px);
    }
</style>

<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

// Get data from overview_section table
$db = getDbInstance();
$db->orderBy("id", "asc");
$overviewSections = $db->get("overview_section");
// echo "<pre>";
// print_r($overviewSections);
// echo "</pre>";
// die();
// Get data from overview_section_images table
$db = getDbInstance();
$db->orderBy("overview_section_id", "asc");
$overviewSectionImages = $db->get("overview_section_images");

// Function to get images by overview_section_id
function getImagesBySectionId($images, $sectionId)
{
    $result = [];
    foreach ($images as $image) {
        if ($image['overview_section_id'] == $sectionId) {
            $result[] = $image['image_path'];
        }
    }
    return $result;
}

include BASE_PATH . '/includes/header.php';
?>

<!-- Layout container -->
<div class="layout-page">

    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4">View Overview Sections</h4>
            <!-- Basic Layout -->
            <div class="row">

                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-body">
                            <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-info notification" id="top-right-notification">
                                    <?php echo $_SESSION['message'];
                                    unset($_SESSION['message']); ?>
                                </div>
                            <?php endif; ?>
                            <div class="table-responsive text-nowrap border-light border-solid mb-3">
                                <table class="table">
                                    <thead>
                                        <tr class="text-nowrap bg-dark align-middle">
                                            <th class="text-white border-right-white">Section ID</th>
                                            <th class="text-white border-right-white">Description</th>
                                            <th class="text-white border-right-white">Images</th>
                                            <th class="text-white border-right-white">Status</th>
                                            <th class="text-white border-right-white">Edit</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        <?php foreach ($overviewSections as $section): ?>
                                            <tr>
                                                <td class="border-right-dark">#<?php echo xss_clean($section['id']); ?></td>
                                                <td class="border-right-dark" style="max-width: 27em;"><?php echo xss_clean($section['description']); ?></td>
                                                <td class="border-right-dark">
                                                    <?php
                                                    $images = getImagesBySectionId($overviewSectionImages, $section['id']);
                                                    if (!empty($images)) {
                                                        foreach ($images as $image) {
                                                            echo '<img src="' . xss_clean($image) . '" alt="Image" style="width: 50px; height: 50px; margin-right: 5px;">';
                                                        }
                                                    } else {
                                                        echo 'No images available';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="border-right-dark">
                                                    <form action="update-overview-status.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="id" value="<?php echo xss_clean($section['id']); ?>">
                                                        <select name="status" class="form-control" onchange="this.form.submit()">
                                                            <option value="active" <?php echo ($section['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                                            <option value="draft" <?php echo ($section['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td class="border-right-dark">
                                                    <a href="add-overview-section.php?id=<?php echo encryptId($section['id']); ?>">Edit Details</a> |
                                                    <a href="delete-overview-section.php?id=<?php echo encryptId($section['id']); ?>" onclick="return confirm('Are you sure you want to delete this overview section?')">Delete</a>
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
        <!-- / Content -->
    </div>
</div>
<script>
    window.onload = function() {
        var notification = document.getElementById('top-right-notification');

        if (notification) {
            // Show the notification with the smooth transition
            notification.classList.add('show');

            // Hide the notification after 3 seconds (with a smooth transition)
            setTimeout(function() {
                notification.classList.add('hide');

                // After the transition, remove the 'show' and 'hide' classes
                setTimeout(function() {
                    notification.classList.remove('show', 'hide');
                }, 500); // Wait for the opacity transition to finish
            }, 3000); // Notification stays visible for 3 seconds
        }
    };
</script>
</body>

</html>