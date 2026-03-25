<?php
session_start();
require_once '../config/config.php';
// require_once BASE_PATH . '/includes/auth_validate.php';

$db = getDbInstance();
$db->where('taxi_id', $_POST['package_id']);
$db->where('itineary', ['TWIN Fixed', 'CWB Fixed', 'CNB Fixed', 'TRIPLE Fixed', 'SINGLE Fixed', 'QUAD SHARING Fixed'], "NOT IN");
$results = $db->get("taxi_details");
$tour_date =  date('d-m-Y', strtotime($_POST['tour_date']));
foreach ($results as $key => $result):

?>

    <tr>
        <td style="min-width: 82px">Day <?= $key + 1 ?>, <?= date('l', strtotime($tour_date)) ?></td>
        <td style="min-width: 130px"><?= $tour_date ?></td>
        <td><?= $result['itineary'] ?></td>
    </tr>
<?php
    $tour_date = addOneDay($tour_date);
endforeach;

$db = getDbInstance();
$db->where('id', $_POST['package_id']);
$result = $db->getOne("packages", 'permit, guide');
?>
98230948klasd908809230894
<h2>Extra Services</h2>
<div class="fixed-service">
    <div class="colm">
        <label class="container"> Permit
            <input class="custom-check" checked type="checkbox" onClick="return calculateTotal();" data-permit="<?= $result['permit'] ?>" name="permit" id="permit">
            <span class="checkmark"></span>
        </label>
    </div>
    <div class="colm">
        <label class="container"> Guide
            <input class="custom-check" checked type="checkbox" onClick="return calculateTotal();" data-guide="<?= $result['guide'] ?>" name="guide" id="guide">
            <span class="checkmark"></span>
        </label>
    </div>
</div>