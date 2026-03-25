<?php
session_start();
require_once '../config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

function getDriverDetails($data, $driver_name)
{ 
    $driver_index = array_search($driver_name, $data["'driver'"]??[]);

    if ($driver_index !== false) { 
        return [
            'name' => $data["'driver'"][$driver_index],
            'mobile' => $data["'mobile'"][$driver_index],
            'status' => 1
        ];
    } else { 
        return [
            'name' => null,
            'mobile' => null,
            'status' => 0
        ];
    }
}


$db = getDbInstance();
$db->where('id', $_POST['agent_query_id']);
$result = $db->getOne("agent_queries");
$dr_details = json_decode($result['driver_details'], true);
// Decode the transport field
$trans = json_decode($result['transport'] ?? "{}", true);
$transName = $trans["'name'"] ?? [];
foreach ($transName as $name) :
    $db = getDbInstance();
    $db->where('vehicle_type', $name);
    $results = $db->get("vehicles");

?>

    <tr>
        <input type="hidden" name="transport['type'][]" value="<?= $name ?>" />
        <td><?= $name ?></td>
        <td>1</td>
        <td>
            <select onchange="updateDriverContact(this)" name="transport['driver'][]" class="form-select driver-select">
                <?php
                $dr_detail = ['status' => 0];
                foreach ($results as $res) :
                    if ($dr_detail['status'] == 0) {
                        $dr_detail =  getDriverDetails($dr_details, $res['driver_name']);
                    }
                ?>
                    <option <?= $dr_detail['name'] == $res['driver_name'] ? 'selected' : '' ?> data-driver-phone="<?= $res['mobile']; ?>" value="<?= $res['driver_name']; ?>"><?= $res['driver_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="hidden" name="transport['mobile'][]" value="<?= $dr_detail['mobile'] !== null ? $dr_detail['mobile'] : $results[0]['mobile']  ?>" />
            <span><?= $dr_detail['mobile'] !== null ? $dr_detail['mobile'] : $results[0]['mobile'] ?></span>
        </td>
    </tr>
<?php
    $dr_detail = ['status' => 0];
endforeach;
?>