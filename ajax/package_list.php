<?php
session_start();
require_once '../config/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$agent_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;  // Ensure agent_id is an integer
$default_categories = getCategories();
$db = getDbInstance();

// Fetch agent-specific packages


// Fetch general packages (not assigned to any specific agent)
$db->where('duration', $_POST['duration']);
$db->where('traveling_from', $_POST['traveling_from']);
$db->where('destination', $_POST['destination']);
$db->where('status', 'Active');
$db->where("(agents IS NULL OR agents = '')");
$general_results = $db->get("packages");

if (!empty($agent_id)) {
    $db->where('duration', $_POST['duration']);
    $db->where('status', 'Active');
    $db->where('agents', "%{$agent_id}%", 'LIKE');
    $agent_specific_results = $db->get("packages");

    // Ensure $agent_specific_results is always an array
    $agent_specific_results = !empty($agent_specific_results) ? $agent_specific_results : [];

    $results = array_merge($agent_specific_results, $general_results);
} else {
    $results = $general_results;
}

// SELECT  * FROM packages WHERE  duration = '8 Nights 9 Days'  AND traveling_from = '2'  AND destination = '1'  AND status = 'Active'  AND (agents IS NULL OR agents = '')

// echo "<pre>";
// echo $db->getLastQuery();
// print_r($results);
// echo "</pre>";
if (!empty($results)): ?>
    <?php foreach ($results as $result): ?>
        <tr>
            <td>
                <div class="form-check">
                    <input name="package_name"
                        onClick="return setPackageId(
        <?= $result['id'] ?>,
        '<?= addslashes($result['package_name']) ?>',
        '<?= !empty($result['destination']) ? addslashes($result['destination']) : '' ?>',
        '<?= !empty($result['traveling_from']) ? addslashes($result['traveling_from']) : '' ?>'
    )"
                        class="form-check-input" type="radio" value="<?= $result['id'] ?>" id="defaultRadio1">

                </div>
            </td>
            <td>
                <span class="fw-medium">#00<?= $result['id'] ?></span>
            </td>
            <td>
                <span class="fw-bold"><?= $result['package_name'] ?></span>
                <?php if (!empty($result['agents']) && strpos($result['agents'], strval($agent_id)) !== false) : ?>
                    <span class="badge bg-success ms-2 px-2 py-1 small ">Your Package</span>
                <?php endif; ?>
            </td>
            <td><?= $result['duration'] ?></td>
            <td>
                <select class="form-select" onchange="return setCategory(this.value, <?= $result['id'] ?>);">
                    <option>Choose Hotel Category</option>
                    <?php
                    foreach ($default_categories as $category) {
                        echo "<option value=\"$category\">$category</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="5" class="text-center text-danger fw-bold">No packages found.</td>
    </tr>
<?php endif; ?>