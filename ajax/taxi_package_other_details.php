<?php
session_start();
require_once '../config/config.php';
// require_once BASE_PATH . '/includes/auth_validate.php';

$db = getDbInstance();
$db->where('taxi_id', $_POST['package_id']);
$db->where('itineary', ['TWIN Fixed', 'CWB Fixed', 'CNB Fixed', 'TRIPLE Fixed', 'SINGLE Fixed', 'QUAD SHARING Fixed'], "IN");
$results = $db->get("taxi_details");
foreach ($results as $key => $result) :
?>

    <div class="col-md add-room-toggle-btn">
        <label class="form-label"><?= str_replace("Fixed", "", $result['itineary']) ?></label>
        <!-- <small class="text-muted float-end set-padding-top">2 Pax</small>-->
        <div class="input-group">
            <button class="btn border-lighter add-custom-padding decrement" type="button">-</button>
            <input

                name="person[<?= str_replace("Fixed", "", $result['itineary']) ?>]"
                type="text" class="form-control text-center quantity" value="0">
            <button class="btn border-lighter add-custom-padding increment" type="button">+</button>
        </div>
    </div>
<?php
endforeach;

?>