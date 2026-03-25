<?php
session_start();
require_once './config/config.php';
if ($_SESSION['admin_type'] == 'Admin') {
    require_once 'includes/header.php';
} else {
    require_once 'includes/agent_header.php';
}
require_once 'PDFGenerate.php';
$edit = false;
$id = isset($_GET['ID']) && !empty($_GET['ID']) ? decryptId($_GET['ID']) : "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['form_submit_type'] == 'Accept Booking') {

    $data_to_store = array_filter($_POST);
    $save_data = [];
    $save_data["is_accept"] = "Yes";
    $save_data["updated_by"] = $_SESSION['user_id'];
    $db = getDbInstance();
    $db->where('id', $id);
    $last_id = $db->update('agent_queries', $save_data);
    $_SESSION['success'] = "The booking has been accepted successfully.";

    //PDF send to Mail
    $file_name = "transport_booking_{$id}_" . uniqid();
    $db = getDbInstance();
    $db->where('id', $id);
    $queries = $db->getOne("agent_queries");
    $hotel_details = !empty($queries['hotel_details']) ? json_decode($queries['hotel_details'], true) : [];

    // get agent email
    $db = getDbInstance();
    $db->where('id', $queries['created_by']);
    $agent_detail = $db->getOne("agents", ['email_id']);
    $agent_email = $agent_detail['email_id'];
    // get Logged in admin email
    $db = getDbInstance();
    $db->where('id', $_SESSION['user_id']);
    $admin_detail = $db->getOne("agents", ['email_id']);
    $admin_email = $admin_detail['email_id'];

    $pdfObj1 = new PDFGenerate;
    $pdfObj1->transport_booking($queries);
    $filePath = $pdfObj1->generatePDF($file_name);
    $pdfObj1->sendMailToClient($agent_email, ['type' => 'transport'],  $filePath);
    $pdfObj1->sendMailToClient($admin_email, ['type' => 'transport'],  $filePath);
    // send mail here for first PDF


    foreach ($hotel_details["'name'"] as $hkey => $hname) {
        $pdfObj2 = new PDFGenerate;
        $file_name = "hotel_voucher_{$hname}_{$id}_" . uniqid();
        $pdfObj2->hotel_voucher(['query_id' => $id, 'index' => $hkey]);
        $filePath =  $pdfObj2->generatePDF($file_name);
        $pdfObj2->sendMailToClient($agent_email, ['type' => 'voucher'],  $filePath);
        $pdfObj2->sendMailToClient($admin_email, ['type' => 'voucher'],  $filePath);

        $db = getDbInstance();
        $db->where('hotel_name', $hname);
        $hotel_detail = $db->getOne("hotels", ['email_id']);
        $hotel_email = $hotel_detail['email_id'];

        $pdfObj2->sendMailToClient($hotel_email, ['type' => 'voucher'],  $filePath);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['form_submit_type'] == 'Confirm Booking') {
    $data_to_store = array_filter($_POST);
    $save_data = [];
    $save_data["type"] = "Booking";
    $save_data["updated_by"] = $_SESSION['user_id'];
    $db = getDbInstance();
    $db->where('id', $id);
    $last_id = $db->update('agent_queries', $save_data);
    $_SESSION['success'] = "The booking has been generated successfully.";
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['form_submit_type'] == 'Edit Quote') {
    $data_to_store = array_filter($_POST);
    $save_data = [];
    $save_data["name"] = $data_to_store['name'];
    $save_data["duration"] = $data_to_store['duration'];
    $save_data["tour_start_date"] = $data_to_store['tour_start_date'];
    $save_data["package_id"] = $data_to_store['package_id'];
    $save_data["category"] = $data_to_store['category'];
    $save_data["your_budget"] = $data_to_store['your_budget'] ?? 0;
    $save_data["total_amount"] = $data_to_store['total_amount'];
    $save_data["without_gst"] = $data_to_store['without_gst'];
    $save_data["total_pax"] = $data_to_store['total_pax'];
    $save_data["tour_end_date"] = $data_to_store['tour_end_date'];
    $save_data["cumulative"] = json_encode($data_to_store['cumulative'] ?? []);
    $save_data["per_person"] = json_encode($data_to_store['per_person'] ?? []);
    $save_data["per_service"] = json_encode($data_to_store['per_service'] ?? []);
    $save_data["inclusive"] = !empty($data_to_store['inclusive']) ? $data_to_store['inclusive'] : json_encode([]);
    $save_data["exclusive"] = !empty($data_to_store['exclusive']) ? $data_to_store['exclusive'] : json_encode([]);
    $save_data["person"] = json_encode($data_to_store['person'] ?? []);
    $save_data["transport"] = json_encode($data_to_store['transport'] ?? []);
    $save_data["updated_by"] = $_SESSION['user_id'];
    $db = getDbInstance();
    $db->where('id', $id);
    $last_id = $db->update('agent_queries', $save_data);
    $_SESSION['success'] = "The query has been updated successfully.";
}
if (!empty($id)) {
    $db = getDbInstance();
    $db->where('id', $id);
    $queries = $db->getOne("agent_queries");

    $db = getDbInstance();
    $db->where('id',  $queries['package_id']);
    $package = $db->getOne("packages");
}
$save_transport = json_decode($queries['transport'], true);
$hotel_details = !empty($queries['hotel_details']) ? json_decode($queries['hotel_details'], true) : [];
$driver_details = !empty($queries['driver_details']) ? json_decode($queries['driver_details'], true) : [];
$db = getDbInstance();
$vehicles = $db->get("vehicles", null, 'driver_name, vehicle_number, mobile, vehicle_type');
$vehicleData = [];
foreach ($vehicles as $vehicle) {
    $vehicleData[$vehicle['vehicle_type']] = $vehicle;
}
$json_vehicle = json_encode($vehicleData);

//$disabled = $queries['type']=='Booking'?'disabled':'';
$disabled = '';
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<style>
    .btn-loading {
        position: relative;
    }

    .btn-loading .spinner-border {
        display: none;
        width: 1rem;
        height: 1rem;
        position: absolute;
        right: 0.5em;
        top: 50%;
        transform: translateY(-50%);
    }

    .btn-loading.loading .spinner-border {
        display: inline-block;
    }
</style>
<div class="layout-page">
    <div class="content-wrapper">

        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="front-body-content">
                <form method="post" id="edit-query" data-disable-inputs="true">
                    <div class="block">
                        <div class="left-part">

                            <div class="card">
                                <?php include BASE_PATH . '/includes/flash_messages.php'; ?>
                                <h1>Quick Booking</h1>

                                <div class="row mb-3">
                                    <div class="col-md">
                                        <label class="form-label">Guest Name</label>
                                        <input type="text" <?= $disabled ?> name="name" class="form-control" placeholder="" value="<?php echo $queries['name'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md">
                                        <label class="form-label">Select Duration</label>
                                        <div class="input-group">
                                            <label class="input-group-text">Options</label>
                                            <select class="form-select" <?= $disabled ?> name="duration" id="duration">
                                                <option value="">Choose...</option>
                                                <option value="1 Nights 2 Days" <?php echo ($queries['duration'] == "1 Nights 2 Days") ? 'selected' : '' ?>>1 Nights 2 Days</option>
                                                <option value="2 Nights 3 Days" <?php echo ($queries['duration'] == "2 Nights 3 Days") ? 'selected' : '' ?>>2 Nights 3 Days</option>
                                                <option value="3 Nights 4 Days" <?php echo ($queries['duration'] == "3 Nights 4 Days") ? 'selected' : '' ?>>3 Nights 4 Days</option>
                                                <option value="4 Nights 5 Days" <?php echo ($queries['duration'] == "4 Nights 5 Days") ? 'selected' : '' ?>>4 Nights 5 Days</option>
                                                <option value="5 Nights 6 Days" <?php echo ($queries['duration'] == "5 Nights 6 Days") ? 'selected' : '' ?>>5 Nights 6 Days</option>
                                                <option value="6 Nights 7 Days" <?php echo ($queries['duration'] == "6 Nights 7 Days") ? 'selected' : '' ?>>6 Nights 7 Days</option>
                                                <option value="7 Nights 8 Days" <?php echo ($queries['duration'] == "7 Nights 8 Days") ? 'selected' : '' ?>>7 Nights 8 Days</option>
                                                <option value="8 Nights 9 Days" <?php echo ($queries['duration'] == "8 Nights 9 Days") ? 'selected' : '' ?>>8 Nights 9 Days</option>
                                                <option value="9 Nights 10 Days" <?php echo ($queries['duration'] == "9 Nights 10 Days") ? 'selected' : '' ?>>9 Nights 10 Days</option>
                                                <option value="10 Nights 11 Days" <?php echo ($queries['duration'] == "10 Nights 11 Days") ? 'selected' : '' ?>>10 Nights 11 Days</option>
                                                <option value="11 Nights 12 Days" <?php echo ($queries['duration'] == "11 Nights 12 Days") ? 'selected' : '' ?>>11 Nights 12 Days</option>
                                                <option value="12 Nights 13 Days" <?php echo ($queries['duration'] == "12 Nights 13 Days") ? 'selected' : '' ?>>12 Nights 13 Days</option>
                                                <option value="13 Nights 14 Days" <?php echo ($queries['duration'] == "13 Nights 14 Days") ? 'selected' : '' ?>>13 Nights 14 Days</option>
                                                <option value="14 Nights 15 Days" <?php echo ($queries['duration'] == "14 Nights 15 Days") ? 'selected' : '' ?>>14 Nights 15 Days</option>
                                                <option value="15 Nights 16 Days" <?php echo ($queries['duration'] == "15 Nights 16 Days") ? 'selected' : '' ?>>15 Nights 16 Days</option>
                                                <option value="16 Nights 17 Days" <?php echo ($queries['duration'] == "16 Nights 17 Days") ? 'selected' : '' ?>>16 Nights 17 Days</option>
                                                <option value="17 Nights 18 Days" <?php echo ($queries['duration'] == "17 Nights 18 Days") ? 'selected' : '' ?>>17 Nights 18 Days</option>
                                                <option value=">18 Nights 19 Days" <?php echo ($queries['duration'] == ">18 Nights 19 Days") ? 'selected' : '' ?>>18 Nights 19 Days</option>
                                                <option value="19 Nights 20 Days" <?php echo ($queries['duration'] == "19 Nights 20 Days") ? 'selected' : '' ?>>19 Nights 20 Days</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <label class="form-label">Select Date</label>
                                        <input class="form-control" <?= $disabled ?> type="date" name="tour_start_date" onChange="return itinerary_list()" value="<?= $queries['tour_start_date'] ?>">
                                    </div>
                                </div>


                                <h3 class="mt-3 mb-3">Select Package</h3>
                                <div class="row mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="text-white px-2" style="max-width: 84px">Select Package</th>
                                                    <th class="text-white px-2" style="max-width: 84px">Package Code</th>
                                                    <th class="text-white px-2">Package Name</th>
                                                    <th class="text-white px-2">Duration</th>
                                                    <th class="text-white px-2">Hotel Category</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0" id="package_list">
                                                <?php
                                                $default_categories = getCategories();
                                                $db = getDbInstance();
                                                $db->where('status', 'Active');
                                                $db->where('duration', $queries['duration']);
                                                $results = $db->get("packages");
                                                foreach ($results as $result) :
                                                    $selected =  ($result['id'] == $queries['package_id']) ? 'checked' : "";

                                                ?>

                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input name="package_name" <?= $disabled ?> onClick="return setPackageId(<?= $result['id'] ?>)" class="form-check-input" type="radio" <?= $selected ?> value="<?= $result['id'] ?>" id="defaultRadio1">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="fw-medium">#00<?= $result['id'] ?></span>
                                                        </td>
                                                        <td><?= $result['package_name'] ?></td>
                                                        <td><?= $result['duration'] ?></td>
                                                        <td>
                                                            <select <?= $disabled ?> class="form-select" onchange="return setCategory(this.value, <?= $result['id'] ?>);">
                                                                <option>Choose Hotel Category</option>
                                                                <?php
                                                                foreach ($default_categories as $category) {
                                                                    $cat_selected = "";
                                                                    if (!empty($selected)) {
                                                                        $cat_selected = ($category == $queries['category']) ? "selected" : "";
                                                                    }

                                                                    echo " <option value=\"$category\" $cat_selected>$category</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <input type="hidden" name="package_id" value="<?php echo $queries['package_id'] ?? ''; ?>">
                                <input type="hidden" name="category" value="<?php echo $queries['category'] ?? ''; ?>">
                                <input type="hidden" name="total_amount" value="<?php echo $queries['total_amount'] ?? ''; ?>">
                                <input type="hidden" name="without_gst" value="<?php echo $queries['without_gst'] ?? ''; ?>">

                                <input type="hidden" name="total_pax" value="<?php echo $queries['total_pax'] ?? ''; ?>">
                                <input type="hidden" name="tour_end_date" value="<?php echo $queries['tour_end_date'] ?? ''; ?>">

                                <input type="hidden" name="exclusive" value="<?php echo $queries['exclusive'] ?? ''; ?>">
                                <input type="hidden" name="inclusive" value="<?php echo $queries['inclusive'] ?? ''; ?>">
                                <div class="row mb-3" id="package-other-details">
                                    <?php include("./ajax/package_other_details_edit.php") ?>
                                </div>

                                <?php $transportations = setTransportation(); ?>
                                <h3 class="mt-3 mb-3">Select Transportation</h3>
                                <div class="row mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="text-white">Select Transport</th>

                                                    <th class="text-white">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                <?php if ($save_transport["'name'"] < 1) { ?>
                                                    <tr class="transport-row">
                                                        <td>
                                                            <select name="transport['name'][]" <?= $disabled ?> class="form-select transportation-select" onChange="return calculateTotal();">
                                                                <option>Select Transport</option>
                                                                <?php foreach ($transportations as $name => $val) : ?>
                                                                    <option value="<?php echo $name; ?>" data-trans="<?php echo $val; ?>"><?php echo $name; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>
                                                        <td>Maximum <span class="max-persons"></span> Persons</td>
                                                    </tr>

                                                <?php
                                                }
                                                foreach ($save_transport["'name'"] as $key => $sname) :
                                                    $per_person = '';
                                                ?>
                                                    <tr class="transport-row">
                                                        <td>
                                                            <select select name="transport['name'][]" <?= $disabled ?> class="form-select transportation-select" onChange="return calculateTotal();">
                                                                <option>Select Transport</option>
                                                                <?php foreach ($transportations as $name => $val) :
                                                                    $selected = $name == $sname ? "selected" : "";

                                                                    $per_person = !empty($selected) ? $val : $per_person;
                                                                ?>
                                                                    <option <?= $selected ?> value="<?php echo $name; ?>" data-trans="<?php echo $val; ?>"><?php echo $name; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>

                                                        <td>Maximum <span class="max-persons"><?= $per_person ?></span> Persons</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <?php if (empty($disabled)) { ?>
                                                    <tr>
                                                        <td colspan="3" style="text-align:right;"><a href="#" id="addMoreTransport">Add More</a></td>
                                                    </tr>
                                                <?php } ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row mb-3" id="fixed-service">
                                    <h3 class="mt-3 mb-3">Extra Services</h3>
                                    <div class="col-md-3">
                                        <div class="form-check mt-b">
                                            <input <?= $disabled ?> class="form-check-input" <?php if ($queries['permit'] == 'on') {
                                                                                                    echo "checked";
                                                                                                } ?> type="checkbox" onClick="return calculateTotal();" data-permit="<?= $package['permit'] ?>" name="permit" id="permit">
                                            <label class="form-check-label" for="permit">Permit </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check mt-b">
                                            <input <?= $disabled ?> class="form-check-input" <?php if ($queries['guide'] == 'on') {
                                                                                                    echo "checked";
                                                                                                } ?> type="checkbox" onClick="return calculateTotal();" data-guide="<?= $package['guide'] ?>" name="guide" id="guide">
                                            <label <?= $disabled ?> class="form-check-label" for="guide">Guide </label>
                                        </div>
                                    </div>
                                </div>


                                <div class="row mb-3">
                                    <div class="table-responsive" id="service-list">

                                        <?php
                                        $tour_date = $queries['tour_start_date'];
                                        preg_match('/\d+/', $queries['duration'], $matches);
                                        $days = $matches[0];
                                        $date_data = [];
                                        for ($i = 0; $i < $days; $i++) {
                                            $date_data[] = date('d-m-Y', strtotime($tour_date));
                                            $tour_date = addOneDay($tour_date);
                                        }
                                        $save_cumulative  =    json_decode($queries['cumulative'], true);
                                        $save_per_person  =    json_decode($queries['per_person'], true);
                                        $save_per_service  =    json_decode($queries['per_service'], true);

                                        $db = getDbInstance();
                                        $db->where('type', 'Cumulative');
                                        $cumulative_service = $db->get("services");

                                        $db = getDbInstance();
                                        $db->where('type', 'Per Person');
                                        $per_person_service = $db->get("services");

                                        $db = getDbInstance();
                                        $db->where('type', 'Per Service');
                                        $per_services = $db->get("services");
                                        ?>
                                        <table class="table table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="text-white">Service</th>
                                                    <?php foreach ($date_data as $d) { ?>
                                                        <th class="text-white"><?= $d ?></th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                <?php foreach ($cumulative_service as $cumulative) : ?>
                                                    <tr>
                                                        <td>
                                                            <label class="form-check-label" for="><?= $cumulative['name'] ?>"><?= $cumulative['name'] ?> </label>
                                                        </td>
                                                        <?php foreach ($date_data as $d) {
                                                            $checked = "";

                                                            if (isset($save_cumulative[$cumulative['id']]['dates']) && in_array($d, $save_cumulative[$cumulative['id']]['dates'])) {
                                                                $checked = "checked";
                                                            }


                                                        ?>
                                                            <td>
                                                                <input <?= $disabled ?> <?= $checked ?> onClick="return calculateTotal();" class="form-check-input" type="checkbox" name="cumulative[<?= $cumulative['id'] ?>][dates][]" amount-cumulative="<?= $cumulative['amount'] ?>" value="<?= $d ?>" id="cumulative<?= $cumulative['id'] ?>_<?= $d ?>">
                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php endforeach ?>

                                                <?php foreach ($per_person_service as $per_person) : ?>
                                                    <tr>
                                                        <td>
                                                            <label class="form-check-label" for="<?= $per_person['name'] ?>"><?= $per_person['name'] ?> </label>
                                                        </td>
                                                        <?php foreach ($date_data as $d) {
                                                            $checked = "";

                                                            if (isset($save_per_person[$per_person['id']]['dates']) && in_array($d, $save_per_person[$per_person['id']]['dates'])) {
                                                                $checked = "checked";
                                                            }
                                                        ?>
                                                            <td>
                                                                <input <?= $disabled ?> <?= $checked ?> onClick="return calculateTotal();" class="form-check-input" type="checkbox" name="per_person[<?= $per_person['id'] ?>][dates][]" amount-per-person="<?= $per_person['amount'] ?>" value="<?= $d ?>" id="per_person<?= $per_person['id'] ?>_<?= $d ?>">
                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php endforeach ?>
                                        </table>
                                    </div>
                                </div>

                                <h3 class="mt-3 mb-3">Per Service</h3>
                                <div class="table-responsive" id="service-per-service">
                                    <div class="row mb-3 align-items-top">
                                        <?php foreach ($per_services as $per_service) : ?>
                                            <div class="col-md-4">
                                                <div class="row">
                                                    <div class="col-md-7">
                                                        <div class="form-check mt-b">
                                                            <label class="form-check-label" for="<?= $per_service['name'] ?>"><?= $per_service['name'] ?> </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="row">
                                                            <div class="col-md">
                                                                <input <?= $disabled ?> value="<?= $save_per_service[$per_service['id']][0] ?>" onChange="return calculateTotal();" placeholder="Enter no. of quantity" type="number" min="0" class="form-control phone-mask" name="per_service[<?= $per_service['id'] ?>][]" amount-per-service="<?= $per_service['amount'] ?>" id="per_service<?= $per_service['id'] ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php endforeach ?>
                                    </div>
                                </div>




                                <div class="row mb-3 align-items-top d-none">
                                    <div class="col-md-3">
                                        <div class="form-check mt-b">
                                            <input class="form-check-input" type="checkbox" value="" id="bike">
                                            <label class="form-check-label" for="bike">Bike </label>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="row">

                                            <div class="col-md">
                                                <input <?= $disabled ?> type="text" class="form-control phone-mask" placeholder="No. of Day">
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <h3 class="mt-3 mb-3 d-none">Enter Bike Details</h3>
                                <div class="row mb-3 d-none">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody class="table-border-bottom-0">
                                                <tr>
                                                    <td>Twin Bike</td>
                                                    <td><input type="text" class="form-control phone-mask" placeholder="No. of Single rider bike"></td>
                                                    <td><input type="text" class="form-control phone-mask" placeholder="No. of Double rider bike"></td>

                                                </tr>
                                                <tr>
                                                    <td>Twin Bike</td>
                                                    <td><input type="text" class="form-control phone-mask" placeholder="No. of Single rider bike"></td>
                                                    <td><input type="text" class="form-control phone-mask" placeholder="No. of Double rider bike"></td>

                                                </tr>
                                                <tr>
                                                    <td>Twin Bike</td>
                                                    <td><input type="text" class="form-control phone-mask" placeholder="No. of Single rider bike"></td>
                                                    <td><input type="text" class="form-control phone-mask" placeholder="No. of Double rider bike"></td>

                                                </tr>
                                                <tr>
                                                    <td>Mechanic</td>
                                                    <td colspan="2">
                                                        <select class="form-select">
                                                            <option>No</option>
                                                            <option>Yes</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Marshal with Bike</td>
                                                    <td colspan="2">
                                                        <select class="form-select">
                                                            <option>No</option>
                                                            <option>Yes</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Fuel</td>
                                                    <td colspan="2">
                                                        <select class="form-select">
                                                            <option>No</option>
                                                            <option>Yes</option>
                                                        </select>
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td>Backup</td>
                                                    <td colspan="2">
                                                        <select class="form-select">
                                                            <option>No</option>
                                                            <option>Yes</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <h3 class="mt-3 mb-3" id="enter-bike-details">Enter Bike Details</h3>
                                <div class="row mb-3" id="enter-bike-details-section">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody class="table-border-bottom-0">
                                                <tr>
                                                    <td>No. of Bike</td>
                                                    <td colspan="2">
                                                        <input type="number" name="number_of_bike" onChange="return calculateTotal();" class="form-control phone-mask">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Mechanic</td>
                                                    <td colspan="2">
                                                        <select class="form-select" name="mechanic" onChange="return calculateTotal();">
                                                            <option>No</option>
                                                            <option>Yes</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Marshal with Bike</td>
                                                    <td colspan="2">
                                                        <select class="form-select" name="marshal" onChange="return calculateTotal();">
                                                            <option>No</option>
                                                            <option>Yes</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Fuel</td>
                                                    <td colspan="2">
                                                        <select class="form-select" name="fuel" onChange="return calculateTotal();">
                                                            <option>No</option>
                                                            <option>Yes</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Backup</td>
                                                    <td colspan="2">
                                                        <select class="form-select" name="backup" onChange="return calculateTotal();">
                                                            <option>No</option>
                                                            <option>Yes</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <h3 class="mt-3 mb-3">Itinerary</h3>
                                <div class="row mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="text-white">Day</th>
                                                    <th class="text-white">Date</th>
                                                    <th class="text-white">Day</th>
                                                    <th class="text-white">Plan #001</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0" id="itinerary-list">
                                                <?php

                                                $db = getDbInstance();
                                                $db->where('package_id', $queries['package_id']);
                                                $db->where('itineary', ['TWIN Fixed', 'CWB Fixed', 'CNB Fixed', 'TRIPLE Fixed', 'SINGLE Fixed', 'QUAD SHARING Fixed'], "NOT IN");
                                                $results = $db->get("package_details");
                                                $tour_date =  date('d-m-Y', strtotime($queries['tour_start_date']));

                                                foreach ($results as $key => $result) :

                                                ?>

                                                    <tr>
                                                        <td style="min-width: 82px">Day <?= $key + 1 ?></td>
                                                        <td style="min-width: 130px"><?= $tour_date ?></td>
                                                        <td><?= date('l', strtotime($tour_date)) ?></td>
                                                        <td><?= $result['itineary'] ?></td>
                                                    </tr>
                                                <?php
                                                    $tour_date = addOneDay($tour_date);
                                                endforeach;
                                                /*
                                                $db = getDbInstance();
                                                $db->where('id', $queries['package_id']);
                                                $result = $db->getOne("packages", 'permit, guide');
                                                */
                                                ?>
                                                <!-- break
                                                <h3 class="mt-3 mb-3">Extra Services</h3>
                                                <div class="col-md-3">
                                                    <div class="form-check mt-b">
                                                     <input class="form-check-input" checked type="checkbox" onClick="return calculateTotal(<?= $result['id'] ?>);" data-permit="<?= $result['permit'] ?>" id="permit">
                                                    <label class="form-check-label" for="permit">Permit </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-check mt-b">
                                                    <input class="form-check-input" checked type="checkbox" onClick="return calculateTotal(<?= $result['id'] ?>);" `data-guide="<?= $result['guide'] ?>" id="guide"`>
                                                    <label class="form-check-label" for="guide">Guide </label>
                                                    </div>
                                                </div> -->

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                                    <h3 class="mt-3 mb-3">Hotel Details</h3>
                                    <?php if ($_SESSION['admin_type'] == 'Admin' && $queries['is_accept'] == 'No') { ?>
                                        <button type="button" id="change-hotel" class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-xl">Choose Hotel</button>
                                    <?php } ?>
                                </div>
                                <div class="row mb-3">
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="text-white px-2" style="max-width: 84px">Day</th>
                                                    <th class="text-white px-2" style="max-width: 84px">Hotel Name</th>
                                                    <th class="text-white px-2">Check in Date</th>
                                                    <th class="text-white px-2">Check out Date</th>
                                                    <th class="text-white px-2">Night</th>
                                                    <th class="text-white px-2">Location</th>
                                                    <th class="text-white px-2">Manager Cont.</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0" id="hotel-list">
                                                <?php
                                                if (count($hotel_details) > 0) {
                                                    foreach ($hotel_details["'name'"] as $hkey => $hname) :
                                                ?>
                                                        <tr>
                                                            <td><?= ($hkey + 1) ?>
                                                                <input type="hidden" name="hotel_night[]" value="<?= 1 ?>" />
                                                                <input type="hidden" name="hotel_amount[]" value="<?= $hotel_details["'amount'"][$hkey] ?>" />
                                                                <input type="hidden" name="hotel_name[]" value="<?= $hname ?>" />
                                                            </td>
                                                            <td><?= $hname ?></td>
                                                            <td><?= $hotel_details["'check_in'"][$hkey] ?></td>
                                                            <td><?= $hotel_details["'check_out'"][$hkey] ?></td>
                                                            <td><?= 1 ?></td>
                                                            <td><?= $hotel_details["'location'"][$hkey] ?></td>
                                                            <td><?= $hotel_details["'mobile'"][$hkey] ?></td>
                                                        </tr>

                                                        <?php
                                                    endforeach;
                                                } else {
                                                    $db = getDbInstance();
                                                    $db->where('package_id', $queries['package_id']);
                                                    $db->where('itineary', ['TWIN Fixed', 'CWB Fixed', 'CNB Fixed', 'TRIPLE Fixed', 'SINGLE Fixed', 'QUAD SHARING Fixed'], "NOT IN");
                                                    $results = $db->get("package_details");
                                                    $tour_date = date('d-m-Y', strtotime($queries['tour_start_date']));
                                                    $location = "";
                                                    $checkIn = $tour_date;
                                                    $day = 0;

                                                    foreach ($results as $key => $result) :
                                                        $night = 0;

                                                        switch (strtolower($queries['category'])) {
                                                            case 'budget':
                                                                $amount = $result['budget'];
                                                                break;
                                                            case 'standard':
                                                                $amount = $result['standard'];
                                                                break;
                                                            case 'deluxe':
                                                                $amount = $result['deluxe'];
                                                                break;
                                                            case 'super_deluxe':
                                                                $amount = $result['super_deluxe'];
                                                                break;
                                                            case 'premium':
                                                                $amount = $result['premium'];
                                                                break;
                                                            case 'premium_plus':
                                                                $amount = $result['premium_plus'];
                                                                break;
                                                            case 'luxury':
                                                                $amount = $result['luxury'];
                                                                break;
                                                            case 'luxury_plus':
                                                                $amount = $result['luxury_plus'];
                                                                break;
                                                            default:
                                                                $amount = 0;
                                                                break;
                                                        }

                                                        if ($result['location'] != $location) {

                                                            $location = $result['location'];
                                                            $checkOut = addOneDay($checkIn);
                                                            $i = $key + 1;
                                                            $night++;
                                                            while (isset($results[$i]['location']) && $result['location'] == $results[$i]['location']) {
                                                                $checkOut =  addOneDay($checkOut);
                                                                $i++;
                                                                $night++;
                                                            }


                                                            $db = getDbInstance();
                                                            $db->where('location', $result['location']);
                                                            $db->where('category', $queries['category']);
                                                            $hotel = $db->getOne("hotels");
                                                            if ($hotel) :
                                                        ?>

                                                                <tr>
                                                                    <td><?= $day = $day + $night ?>
                                                                        <input type="hidden" name="hotel_night[]" value="<?= $night ?>" />
                                                                        <input type="hidden" name="hotel_amount[]" value="<?= $amount ?>" />
                                                                        <input type="hidden" name="hotel_name[]" value="<?= $hotel['hotel_name'] ?>" />
                                                                    </td>
                                                                    <td><?= $hotel['hotel_name'] ?></td>
                                                                    <td><?= $checkIn ?></td>
                                                                    <td><?= $checkOut ?></td>
                                                                    <td><?= $night ?></td>
                                                                    <td><?= $hotel['location'] ?></td>
                                                                    <td><?= $hotel['mobile'] ?></td>
                                                                </tr>
                                                <?php
                                                            endif;
                                                            $checkIn = $checkOut;
                                                        }
                                                    endforeach;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3 mb-3">
                                    <h3 class="mt-3 mb-3">Transport</h3>
                                    <?php if ($_SESSION['admin_type'] == 'Admin' && $queries['is_accept'] == 'No') { ?>
                                        <button type="button" id="driver-change" class="btn btn-primary" data-toggle="modal" data-target=".bd-transport-modal-xl">Choose Driver</button>
                                    <?php } ?>
                                </div>

                                <div class="row mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="driver_list_name">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="text-white">Vehicle Type</th>
                                                    <th class="text-white">No. of Vehicle</th>
                                                    <th class="text-white">Driver Name</th>
                                                    <th class="text-white">Mobile No.</th>
                                                </tr>
                                            </thead>
                                            <tbody class=" table-border-bottom-0">
                                                <?php
                                                if (count($driver_details) > 0) {
                                                    foreach ($driver_details["'driver'"] as $dkey => $dname) :
                                                ?>

                                                        <tr>
                                                            <td><?= $driver_details["'type'"][$dkey] ?></td>
                                                            <td>1</td>
                                                            <td><?= $dname ?></td>
                                                            <td><?= $driver_details["'mobile'"][$dkey] ?></td>
                                                        </tr>

                                                    <?php
                                                    endforeach;
                                                } else {

                                                    foreach ($save_transport["'name'"] as $trans) :

                                                    ?>
                                                        <tr>
                                                            <td><?= $trans ?></td>
                                                            <td>1</td>
                                                            <td><?= $vehicleData["$trans"]['driver_name'] ?></td>
                                                            <td><?= $vehicleData["$trans"]['mobile'] ?></td>
                                                        </tr>
                                                <?php endforeach;
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <h3 class="mt-3 mb-3">Exclusive / Inclusive</h3>

                                <div class="row mb-3">
                                    <div class="col">
                                        <h3 class="mt-3 mb-3 text-center" style="background-color: #233446; padding: 10px;   color: white;">Exclusive</h3>
                                        <ul class="list-group" id="exclusive">
                                        </ul>
                                    </div>
                                    <div class="col">
                                        <h3 class="mt-3 mb-3 text-center" style="background-color: #233446; padding: 10px;  color: white;">Inclusive</h3>
                                        <ul class="list-group" id="inclusive">
                                        </ul>
                                    </div>
                                </div>

                                <h3 class="mt-3 mb-3">Final Quotation</h3>
                                <div class="row mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="final_quotation">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th colspan="4" class="text-white">Your query 01133 Details Quotation in INR</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-border-bottom-0">
                                                <tr>
                                                    <td class="dark-col"><strong>Plan</strong></td>
                                                    <td class="dark-col"><strong>Amount</strong></td>
                                                    <td class="dark-col"><strong>Pax</strong></td>
                                                    <td class="dark-col"><strong>Total amount</strong></td>
                                                </tr>


                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="right-part" style="position:fixed;right:0px">
                            <div class="booking-summary">
                                <div class="card">
                                    <div class="head">
                                        <h3>Booking Summary</h3>
                                    </div>
                                    <div class="summary-detail">
                                        <div class="row mb-3">
                                            <div class="col-md text-bold"><strong>Duration:</strong></div>
                                            <div class="col-md" id="summary-duration">-</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md"><strong>Travel Date:</strong></div>
                                            <div class="col-md" id="summary-travel-date">-</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md"><strong>Total No. of Pax:</strong></div>
                                            <div class="col-md" id="summary-no-of-pax">-</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md"><strong>Calculated Price:</strong></div>
                                            <div class="col-md"><strong id="summary-calculated-price"></strong></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md"><strong>Per Person Price:</strong></div>
                                            <div class="col-md"><strong id="per-person-calculated-price"></strong></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card" style="margin-top: 10px;height: 50px ! IMPORTANT;">

                                    <div class="summary-detail">
                                        <div class="row mb-3">
                                            <div class="col-md text-bold"><label class="form-label"><strong>Your Budget</strong></label></div>
                                            <div class="col-md" id="summary-duration"><input type="number" <?= $disabled ?> name="your_budget" value="<?= $queries['your_budget'] ?>" class="form-control"></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php if ($_SESSION['admin_type'] == 'Agent' && $queries['type'] == 'Query') { ?>
                                <div class="row get-quote-btn" style="margin-top: 10px;">
                                    <div class="d-flex justify-content-between">
                                        <button type="submit" name="form_submit_type" value="Edit Quote" class="btn btn-primary">Edit Quote</button>
                                        <button type="submit" class="btn btn-primary" name="form_submit_type" value="Confirm Booking">Confirm Booking</button>

                                    </div>
                                </div>
                            <?php } ?>

                            <?php if ($_SESSION['admin_type'] == 'Admin') { ?>
                                <div class="row get-quote-btn" style="margin-top: 10px;">
                                    <div class="d-flex justify-content-between">
                                        <?php if (empty($disabled)) { ?>
                                            <button type="submit" name="form_submit_type" value="Edit Quote" class="btn btn-primary">Edit Quote</button>
                                            <?php }
                                        if ($queries['is_accept'] == 'No') {
                                            if ($queries['hotel_details'] != '' && $queries['driver_details'] != '') {
                                            ?>
                                                <button type="submit" class="btn btn-primary" name="form_submit_type" value="Accept Booking">Accept Booking</button>
                                        <?php }
                                        } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<!--
    this is for hotel modal
-->
<div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Choose Hotel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="save-hotel-details">
                <div class="modal-body">
                    <div class="row mb-3">

                        <div class="table-responsive">
                            <table class="table table-bordered" id="driver_list">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-white">DAY</th>
                                        <th class="text-white">HOTEL NAME</th>
                                        <th class="text-white">CHECK IN DATE</th>
                                        <th class="text-white">CHECK OUT DATE</th>
                                        <th class="text-white">NIGHT</th>
                                        <th class="text-white">LOCATION</th>
                                        <th class="text-white">MANAGER CONT.</th>
                                    </tr>
                                </thead>
                                <tbody class=" table-border-bottom-0" id="change-hotel-list">
                                    <tr>
                                        <td colspan="7">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <input type="hidden" value="<?= $id ?>" name="agent_query_id">
                <input type="hidden" value="hotel" name="data_save_form">
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--
    this is for transport modal
-->
<div class="modal fade bd-transport-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Choose Driver</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="save-driver-details">
                <div class="modal-body">
                    <div class="row mb-3">

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-white">VEHICLE TYPE</th>
                                        <th class="text-white">NO. OF VEHICLE</th>
                                        <th class="text-white">DRIVER NAME</th>
                                        <th class="text-white">MOBILE NO.</th>
                                    </tr>
                                </thead>
                                <tbody class=" table-border-bottom-0" id="change-driver-list">
                                    <tr>
                                        <td colspan="4">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <input type="hidden" value="<?= $id ?>" name="agent_query_driver_id">
                <input type="hidden" value="driver" name="data_save_form">
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var inclusive = [];
    var exclusive = [];
    var isEdit = false;
    $(document).ready(function() {
        $('#duration').change(function() {
            var duration = $('#duration').val();
            service_list();
            $.ajax({
                url: 'ajax/package_list.php',
                type: 'POST',
                data: {
                    duration: duration
                },
                success: function(data) {
                    $('#package_list').html(data);

                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });

        $('#change-hotel').click(function() {
            let tour_date = $('input[name="tour_start_date"]').val()
            let package_id = $('input[name="package_id"]').val()
            let category = $('input[name="category"]').val()
            let agent_query_id = $('input[name="agent_query_driver_id"]').val()
            $('#package_list').html('<tr>  <td colspan="7">Loading...</td>  </tr>');
            $.ajax({
                url: 'ajax/change_hotel_list.php',
                type: 'POST',
                data: {
                    package_id: package_id,
                    tour_date: tour_date,
                    category: category,
                    agent_query_id: agent_query_id
                },
                success: function(data) {
                    $('#change-hotel-list').html(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });

        $('#save-hotel-details').on('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            // Serialize form data
            var formData = $(this).serialize();

            // AJAX request
            $.ajax({
                url: 'ajax/save_data.php', // Replace with your server endpoint
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Handle success response
                    $('#response').html('<div class="alert alert-success">' + response.message + '</div>');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    $('#response').html('<div class="alert alert-danger">An error occurred: ' + error + '</div>');
                }
            });
        });
        /// driver data manupulation
        $('#driver-change').click(function() {
            let agent_query_id = $('input[name="agent_query_driver_id"]').val()
            $('#change-driver-list').html('<tr>  <td colspan="7">Loading...</td>  </tr>');
            $.ajax({
                url: 'ajax/change_driver_list.php',
                type: 'POST',
                data: {
                    agent_query_id: agent_query_id
                },
                success: function(data) {
                    $('#change-driver-list').html(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });

        $('#save-driver-details').on('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            // Serialize form data
            var formData = $(this).serialize();

            // AJAX request
            $.ajax({
                url: 'ajax/save_data.php', // Replace with your server endpoint
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Handle success response
                    $('#response').html('<div class="alert alert-success">' + response.message + '</div>');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    $('#response').html('<div class="alert alert-danger">An error occurred: ' + error + '</div>');
                }
            });
        });


    });


    function setPackageId(package_id) {
        $('input[name="package_id"]').val(package_id)
        setTimeout(function() {
            itinerary_list();
        }, 10);

    }

    function setCategory(category, package_id) {
        if ($('input[name="package_id"]').val() == package_id) {
            $('input[name="category"]').val(category)
            setTimeout(function() {
                hotel_list();
            }, 10);
        } else {
            alert("Please select package name")
        }
    }

    function calculateTourEndDate() {
        const startDate = $('input[name="tour_start_date"]').val();
        const string = $("#duration").val();
        const pattern = /(\d+)\s*Nights?\s*(\d+)\s*Days?/i;
        const matches = string.match(pattern);
        console.log(matches);
        nights = parseInt(matches[1], 10);

        const startDateTime = new Date(startDate);

        startDateTime.setDate(startDateTime.getDate() + nights);

        const endDate = startDateTime.toISOString().split('T')[0];
        $('input[name="tour_end_date"]').val(endDate);
    }

    function itinerary_list() {
        let tour_date = $('input[name="tour_start_date"]').val()
        let package_id = $('input[name="package_id"]').val()

        service_list();
        $.ajax({
            url: 'ajax/itinerary_list.php',
            type: 'POST',
            data: {
                package_id: package_id,
                tour_date: tour_date
            },
            success: function(data) {
                let dataArr = data.split("break")
                $('#itinerary-list').html(dataArr[0]);
                $('#fixed-service').html(dataArr[1]);

                setTimeout(function() {
                    calculateTotal();
                }, 2)
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function service_list() {
        let tour_date = $('input[name="tour_start_date"]').val()
        let days = parseInt($("#duration").val().match(/\d+/)[0]);

        if ($('input[name="tour_start_date"]').val() != '' &&
            $("#duration").val().match(/\d+/)[0] != "") {
            $.ajax({
                url: 'ajax/service_list.php',
                type: 'POST',
                data: {
                    days: days,
                    tour_date: tour_date
                },
                success: function(data) {
                    let dataArr = data.split("break")
                    $('#service-list').html(dataArr[0]);
                    $('#service-per-service').html(dataArr[1]);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    }

    function package_other_details(package_id, category) {
        $.ajax({
            url: 'ajax/package_other_details.php',
            type: 'POST',
            data: {
                package_id: package_id,
                category: category
            },
            success: function(data) {
                $('#package-other-details').html(data);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('#package-other-details').html(error);
            }
        });
    }

    function hotel_list() {
        let tour_date = $('input[name="tour_start_date"]').val()
        let package_id = $('input[name="package_id"]').val()
        let category = $('input[name="category"]').val()

        package_other_details(package_id, category)
        $.ajax({
            url: 'ajax/hotel_list.php',
            type: 'POST',
            data: {
                package_id: package_id,
                tour_date: tour_date,
                category: category
            },
            success: function(data) {
                $('#hotel-list').html(data);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }


    function handleIncrement() {
        const input = this.parentElement.querySelector('.quantity');
        input.value = parseInt(input.value) + 1;

        calculateTotal();
    }

    function handleDecrement() {
        const input = this.parentElement.querySelector('.quantity');
        if (parseInt(input.value) > 0) {
            input.value = parseInt(input.value) - 1;

            calculateTotal();
        }
    }

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('increment')) {
            handleIncrement.call(event.target);
        }
    });

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('decrement')) {
            handleDecrement.call(event.target);

        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Function to update maximum number of persons
        function updateMaxPersons() {
            // console.log( $(this))
            const selectedTransport = $(this).find('option:selected').data('trans');
            $(this).closest('.transport-row').find('.max-persons').text(selectedTransport);
            $(this).closest('.transport-row').find('.num-persons-select').empty();
            for (let i = 0; i <= 5; i++) {
                $(this).closest('.transport-row').find('.num-persons-select').append(`<option value="${i}">${i}</option>`);
            }
            isEdit = true;
        }

        function removeAllBelowElement() {
            $('.transport-row:not(:first)').remove();
        }

        // Initial setup for first row
        // $('.transportation-select').each(updateMaxPersons);

        // Add more transportation option
        $('#addMoreTransport').click(function(e) {
            e.preventDefault();
            const newRow = $('.transport-row:first').clone();
            newRow.find('.transportation-select').val('Select Transport');
            newRow.find('.num-persons-select').val('Select Person');
            newRow.find('.max-persons').empty();
            newRow.insertAfter('.transport-row:last');
            newRow.find('.transportation-select').each(updateMaxPersons);
        });

        // Event delegation for dynamically added elements
        $('.table').on('change', '.transportation-select', updateMaxPersons);
        $('.table').on('change', '.transportation-select:first', removeAllBelowElement);

        //bike section hide and show
        const bikeCheckbox = document.getElementById('bike');
        const bikeDetailsSection = document.getElementById('enter-bike-details-section');
        const bikeDetails = document.getElementById('enter-bike-details');
        bikeDetailsSection.style.display = 'none';
        bikeDetails.style.display = 'none';
        bikeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                bikeDetailsSection.style.display = 'block';
                bikeDetails.style.display = 'block';
            } else {
                bikeDetailsSection.style.display = 'none';
                bikeDetails.style.display = 'none';
            }
        });
        calculateTotal();
    });

    function calculateTotal() {
        inclusive = [];
        exclusive = [];
        const packageDetails = document.querySelectorAll('#package-other-details .col-md');
        const targetTableBody = document.querySelector('#final_quotation tbody');

        let totalAmount = 0;
        let total_per_person = 0;
        const existingRows = targetTableBody.querySelectorAll('tr:not(:first-child)');
        existingRows.forEach(row => row.remove());
        const rowData = {
            items: [],
            totalMember: 0
        };

        packageDetails.forEach(detail => {
            const label = detail.querySelector('.form-label').textContent;
            const price = parseFloat(detail.querySelector('input').dataset.amount);
            const quantity = parseInt(detail.querySelector('input').value);
            const total = price * quantity;
            //totalPax = totalPax + quantity;
            let h_pax = quantity;

            switch (label.trim()) {

                case 'TWIN':
                    h_pax = (quantity * 2);
                    break;
                case 'TRIPLE':
                    h_pax = (quantity * 3);
                    break;
                case 'QUAD SHARING':
                    h_pax = (quantity * 4);
                    break;
                default:
                    h_pax = quantity;
                    break;

            }
            if (quantity > 0) {
                const newRowData = {
                    label: label,
                    price: price,
                    h_pax: h_pax,
                    total: total,
                    quantity: quantity
                };
                rowData.items.push(newRowData);
                rowData.totalMember += h_pax;

            }
        });
        //Extra Services
        //permit
        console.log(rowData)
        let totalPax = rowData.totalMember;
        let permitElement = document.getElementById("permit");
        if (permitElement) {
            if (permitElement.checked) {
                permit_amount = permitElement.getAttribute("data-permit");
                permit_amount = parseInt(permit_amount * totalPax);
                total_per_person = total_per_person + parseInt((permit_amount / totalPax));
            }
        }
        //guide
        let guideElement = document.getElementById("guide");
        if (guideElement) {
            if (guideElement.checked) {
                guide_amount = guideElement.getAttribute("data-guide");
                total_per_person = total_per_person + parseInt((guide_amount / totalPax));
            }
        }

        //services
        const serviceDetails = {};
        document.querySelectorAll('#service-list input[type="checkbox"]').forEach(function(checkbox) {
            if (checkbox.checked) {
                const serviceName = checkbox.closest('tr').querySelector('label').textContent.trim();
                const amount = parseFloat(checkbox.getAttribute('amount-cumulative') || checkbox.getAttribute('amount-per-person'));
                const date = checkbox.value;
                let service_type = ""
                if (checkbox.getAttribute('amount-cumulative')) {
                    service_type = "Cumulative"
                } else if (checkbox.getAttribute('amount-per-person')) {
                    service_type = "Per Person"
                } else if (checkbox.getAttribute('amount-per-service')) {
                    service_type = "Per Service"
                }

                if (serviceDetails.hasOwnProperty(serviceName)) {
                    serviceDetails[serviceName].quantity += 1;
                } else {
                    serviceDetails[serviceName] = {
                        amount: amount,
                        quantity: 1,
                        total: amount,
                        type: service_type
                    };
                }
            } else {
                const serviceName = checkbox.closest('tr').querySelector('label').textContent.trim();
                addExclusive(serviceName);
            }
        });

        for (const serviceName in serviceDetails) {
            if (serviceDetails.hasOwnProperty(serviceName)) {
                const {
                    amount,
                    quantity,
                    type
                } = serviceDetails[serviceName];

                if (type == "Cumulative") {
                    total_per_person = total_per_person + ((amount * quantity) / totalPax);
                } else if (type == "Per Person") {
                    total_per_person = total_per_person + (amount * quantity);
                }
                addInclusive(serviceName);
            }
        }


        // Service Per Service

        const perService = document.getElementById('service-per-service');
        console.log(perService)
        perService.querySelectorAll('.col-md-4').forEach(row => {
            const input = row.querySelector('input[type="number"]');
            const label = row.querySelector('.form-check-label').textContent.trim();

            console.log("herer");
            const amount = input.getAttribute('amount-per-service')
            const quantity = parseInt(input.value) || 0;
            if (quantity > 0) {
                addInclusive(label);
                total_per_person = total_per_person + ((amount * quantity) / totalPax);
            } else {
                addExclusive(label);
            }
        });


        //Transportation

        const driverTableBody = document.querySelector('#driver_list_name tbody');
        const driverDetails = <?= $json_vehicle ?>;
        const existingDriver = driverTableBody.querySelectorAll('tr');
        if (isEdit) {
            existingDriver.forEach(row => row.remove());
        }
        const transportationSelects = document.querySelectorAll('.transportation-select');
        var trans_inclusive = '';
        transportationSelects.forEach(select => {
            const detailId = 'detail_' + select.value.replace(' / ', '_');
            if (document.getElementById(detailId)) {
                const label = select.value;
                selectedOption = select.options[select.selectedIndex];
                const trans = selectedOption.getAttribute('data-trans');

                const amount = parseFloat(document.getElementById(detailId).value);
                const quantity = 1; // Quantity is always 1
                const total = amount * quantity;
                // Append to the table
                total_per_person = total_per_person + ((amount * quantity) / totalPax)
                trans_inclusive = trans_inclusive + label + "-" + trans + ", ";
                if (isEdit) {

                    let driver_name = driverDetails[label].driver_name;
                    let mobile = driverDetails[label].mobile;
                    const driverRow = document.createElement('tr');
                    <?php if ($_SESSION['admin_type'] == 'Admin') { ?>
                        driverRow.innerHTML = `
                    <td>${label}</td>
                    <td>${quantity}</td>
                    <td>${driver_name}</td>
                    <td>${mobile}</td>
                `;
                    <?php } else { ?>
                        driverRow.innerHTML = `
                <td>${label}</td>
                <td>${quantity}</td>
                <td>Pending</td>
                <td>Pending</td>
            `;
                    <?php  } ?>
                    driverTableBody.appendChild(driverRow);
                }

            }
        });

        if (trans_inclusive != '') {
            addInclusive("Transportation: " + trans_inclusive.replace(/, $/, ''));
        }
        if ($('input[name="category"]').val()) {
            addInclusive("Hotel: " + $('input[name="category"]').val());
        }

        // Get Bike Details
        const numberOfBike = document.querySelector('input[name="number_of_bike"]').value;
        const mechanic = document.querySelector('select[name="mechanic"]').value;
        const marshal = document.querySelector('select[name="marshal"]').value;
        const fuel = document.querySelector('select[name="fuel"]').value;
        const backup = document.querySelector('select[name="backup"]').value;

        // Retrieve the values of the inputs
        const numberOfBikePrice = parseInt(numberOfBike) * parseInt(<?php echo Bike ?>);
        const mechanicPrice = mechanic == 'Yes' ? parseInt(<?php echo Mechanic ?>) : 0;
        const marshalPrice = marshal == 'Yes' ? parseInt(<?php echo Marshal ?>) : 0;
        const fuelPrice = fuel == 'Yes' ? parseInt(<?php echo Fuel ?>) : 0;
        const backupPrice = backup == 'Yes' ? parseInt(<?php echo Backup ?>) : 0;
        //document.getElementById('bike').checked
        const total_bike_price = (numberOfBikePrice + mechanicPrice + marshalPrice + fuelPrice + backupPrice)

        rowData.items.forEach(function(item) {
            let price = item.price + total_per_person;
            let h_pax = item.quantity;
            let total = price * item.quantity;
            let per_person_pr = item.price;
            if ("TWIN" == item.label.trim()) {
                price = item.price + (total_per_person * 2)
                h_pax = item.quantity * 2;
                total = price * item.quantity;
                per_person_pr = (total / h_pax);
            } else if ("TRIPLE" == item.label.trim()) {
                price = item.price + (total_per_person * 3)
                h_pax = item.quantity * 3;
                total = price * item.quantity;
                per_person_pr = (total / h_pax);
            } else if ("QUAD SHARING" == item.label.trim()) {
                price = item.price + (total_per_person * 4)
                h_pax = item.quantity * 4;
                total = price * item.quantity;
                per_person_pr = (total / h_pax);
            }
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${item.label}</td>
                <td>${round(per_person_pr)}</td>
                <td>${h_pax}</td>
                <td>${round(total)}</td>
            `;
            targetTableBody.appendChild(newRow);
            totalAmount += total;
        });
        //totalMember
        if (document.getElementById('bike').checked) {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>Bike</td>
                <td>${(total_bike_price/ numberOfBike).toFixed(2)}</td>
                <td>${ numberOfBike}</td>
                <td>${total_bike_price.toFixed(2)}</td>
            `;
            targetTableBody.appendChild(newRow);
            totalAmount += total_bike_price;
        }

        // Hotel List
        /*
    const hotelList = document.getElementById('hotel-list');
    hotelList.querySelectorAll('tr').forEach(row => {

      const nightInput = row.querySelector('input[name="hotel_night[]"]');
      const amountInput = row.querySelector('input[name="hotel_amount[]"]');
      const nameInput = row.querySelector('input[name="hotel_name[]"]');

      const night = nightInput.value;
      const amount = amountInput.value;
      const name = nameInput.value;
      const total = amount * night;
      totalAmount = totalAmount + total;
      const newRow = document.createElement('tr');
      newRow.innerHTML = `
            <td>${name}</td>
            <td>${amount}</td>
            <td>${night}</td>
            <td>${total}</td>
        `;
      targetTableBody.appendChild(newRow);
    });
*/
        // Add total rows
        var finalPrice = totalAmount;
        var igstPrice = round((totalAmount * 0.025));
        var sgstPrice = round((totalAmount * 0.025));
        finalPrice = round((finalPrice + igstPrice + sgstPrice));
        const totalRows = `
        <tr>
            <td></td>
            <td colspan="2">Total Amount Excluding GST</td>
            <td>${totalAmount}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2">IGST 2.5%</td>
            <td>${igstPrice}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2">SGST 2.5%</td>
            <td>${sgstPrice}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2" class="dark-col"><strong>Total Amount Including GST</strong></td>
            <td>${finalPrice}</td>
        </tr>
    `;
        $('input[name="total_amount"]').val(finalPrice);
        $('input[name="without_gst"]').val(totalAmount);
        $('input[name="total_pax"]').val(totalPax);
        calculateTourEndDate();

        targetTableBody.insertAdjacentHTML('beforeend', totalRows);


        var exclusiveList = document.getElementById('exclusive');
        exclusiveList.innerHTML = '';
        exclusive.forEach(function(value) {
            var li = document.createElement('li');
            li.className = 'add-items';
            li.textContent = value;
            exclusiveList.appendChild(li);
        });


        var inclusiveList = document.getElementById('inclusive');
        inclusiveList.innerHTML = '';
        inclusive.forEach(function(value) {
            var li = document.createElement('li');
            li.className = 'add-items';
            li.textContent = value;
            inclusiveList.appendChild(li);
        });

        $('input[name="exclusive"]').val(JSON.stringify(exclusive));
        $('input[name="inclusive"]').val(JSON.stringify(inclusive));

        document.getElementById('summary-duration').innerHTML = document.getElementById('duration').value;
        document.getElementById('summary-travel-date').innerHTML = $('input[name="tour_start_date"]').val();
        document.getElementById('summary-no-of-pax').innerHTML = totalPax;
        document.getElementById('summary-calculated-price').innerHTML = '₹' + finalPrice;
        document.getElementById('per-person-calculated-price').innerHTML = '₹' + round(parseInt(finalPrice / totalPax));
    }

    function round(number) {
        return Math.round(number * 100) / 100;
    }

    // for edit
    function updateManagerContact(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const managerContact = selectedOption.getAttribute('data-manager-contact');
        const managerLocation = selectedOption.getAttribute('data-manager-location');

        // Find the manager contact cell in the same row
        const row = selectElement.closest('tr');
        const hiddenInput = row.querySelector('input[name="hotel[\'mobile\'][]"]');
        const phoneSpan = row.querySelector('span');

        hiddenInput.value = managerContact;
        phoneSpan.textContent = managerContact;

    }

    function updateDriverContact(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const driverPhone = selectedOption.getAttribute('data-driver-phone');

        // Find the row and update the hidden input and span for the driver phone
        const row = selectElement.closest('tr');
        const hiddenInput = row.querySelector('input[name="transport[\'mobile\'][]"]');
        const phoneSpan = row.querySelector('span');

        hiddenInput.value = driverPhone;
        phoneSpan.textContent = driverPhone;
    }


    function addExclusive(value) {
        if (!exclusive.includes(value)) {
            exclusive.push(value);
        }
        var index = inclusive.indexOf(value);
        if (index !== -1) {
            inclusive.splice(index, 1);
        }

    }

    function addInclusive(value) {
        if (!inclusive.includes(value)) {
            inclusive.push(value);
        }
        var index = exclusive.indexOf(value);
        if (index !== -1) {
            exclusive.splice(index, 1);
        }
    }

    document.getElementById('edit-query').addEventListener('submit', function(e) {
        var submitButton = document.getElementById('submitButton');
        submitButton.classList.add('loading');
        submitButton.disabled = true; // Optional: Disable the button to prevent multiple submissions
    });
    //calculateTotal();
    /*
document.addEventListener('input', function(event) {
    if (event.target.classList.contains('quantity')) {
        calculateTotal();
    }
});
*/
</script>
<?php
if ($_SESSION['admin_type'] == 'Admin') {
    include  'includes/footer.php';
} else {
    include  'includes/agent_footer.php';
}
?>