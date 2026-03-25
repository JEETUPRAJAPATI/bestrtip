<?php
session_start();
require_once '../config/config.php';
// require_once BASE_PATH . '/includes/auth_validate.php';

$db = getDbInstance();
$db->where('package_id', $_POST['package_id']);
$db->where('itineary',['TWIN Fixed','CWB Fixed','CNB Fixed','TRIPLE Fixed','SINGLE Fixed','QUAD SHARING Fixed'], "NOT IN");
$results = $db->get("package_details");
$tour_date =  date('d-m-Y',strtotime($_POST['tour_date']));
foreach($results as $key=>$result):
   
?>
<!-- Itinerary Section 1 - Improved with consistent spacing -->
        <div class="d-flex flex-column pt-5 px-3 pb-4 bg-white border-bottom">
            <div class="row">
                <div class="col-md-4">
                    <div class="d-flex flex-column text-xl font-weight-bold text-dark">
                        <div>Day <?=$key+1?>, <?=date('l',strtotime($tour_date))?></div>
                        <div class="mt-2"><?=$tour_date?></div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="text-xl font-weight-bold text-gray-800 mb-3 text-theme">
                        Good tour, really well organised
                    </div>
                    <p class="text-base font-weight-semibold text-muted">
                        <?=$result['itineary']?>
                    </p>
                </div>
            </div>
        </div>

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
            <input class="custom-check" checked type="checkbox" onClick="return calculateTotal();" data-permit="<?=$result['permit']?>" name="permit" id="permit">
            <span class="checkmark"></span>
        </label>
    </div>
    <div class="colm">
        <label class="container"> Guide
        <input class="custom-check" checked type="checkbox" onClick="return calculateTotal();" data-guide="<?=$result['guide']?>" name="guide" id="guide">
        <span class="checkmark"></span>
        </label>
    </div>
</div>