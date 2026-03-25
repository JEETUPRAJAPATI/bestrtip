<?php
session_start();
require_once '../config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

function getHotelDetails($data, $hotel_name,$key) {
    // Check if the hotel exists in the array
    
   // $hotel_index = array_search($hotel_name, $data["'name'"]); 
    if ($data["'name'"][$key]==$hotel_name) {
        $hotel_index = $key;
        // Hotel found, return their details
        return [
            'night' => $data["'night'"][$hotel_index],
            'amount' => $data["'amount'"][$hotel_index],
            'name' => $data["'name'"][$hotel_index],
            'check_in' => $data["'check_in'"][$hotel_index],
            'check_out' => $data["'check_out'"][$hotel_index],
            'mobile' => $data["'mobile'"][$hotel_index],
            'status' => 1
        ];
    } else {
        // Hotel not found
        return [
            'name' => $hotel_name,
            'status' => 0
        ];
    }
}

$db = getDbInstance();
$db->where('package_id', $_POST['package_id']);
$db->where('itineary', ['TWIN Fixed', 'CWB Fixed', 'CNB Fixed', 'TRIPLE Fixed', 'SINGLE Fixed', 'QUAD SHARING Fixed'], "NOT IN");
$results = $db->get("package_details");
$tour_date =  date('d-m-Y', strtotime($_POST['tour_date']));
$location = "";
$checkIn = $tour_date;
$day =0;

$db = getDbInstance();
$db->where('id', $_POST['agent_query_id']);
$result = $db->getOne("agent_queries");
$ho_details = json_decode($result['hotel_details'], true);
 
foreach ($results as $key => $result) :
    $night = 0;

    switch (strtolower($_POST['category'])) {
        case 'budget':
            $amount = $result['budget'];
            break;
        case 'standard':
            $amount = $result['standard'];
            break;
        case 'deluxe':
            $amount = $result['deluxe'];
            break;
        case 'super deluxe':
            $amount = $result['super_deluxe'];
            break;
        case 'premium':
            $amount = $result['premium'];
            break;
        case 'premium plus':
            $amount = $result['premium_plus'];
            break;
        case 'luxury':
            $amount = $result['luxury'];
            break;
        case 'luxury plus':
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
       
        $istrue=false;
        $db = getDbInstance();
        $db->where('location', $result['location']);
        $db->where('category', $_POST['category']);
        $hotels = $db->get("hotels");
        if ($hotels) :
?>

            <tr>
                <td><?=$day = $day+$night?> 
               
            </td>
                <td> 
                <input type="hidden" name="hotel['night'][]" value="<?=$night?>" /> 
                <input type="hidden" name="hotel['amount'][]" value="<?=$amount?>" />
                <input type="hidden" name="hotel['location'][]" value="<?= $hotels[0]['location'] ?>" >
                <input type="hidden" name="hotel['check_in'][]" value="<?= $checkIn ?>" >
                <input type="hidden" name="hotel['check_out'][]" value="<?= $checkOut ?>" > 
                    <select  onchange="updateManagerContact(this)"  name="hotel['name'][]" class="form-select transportation-select" > 
                        <?php                       
                        foreach ($hotels as $hotel) :
                            if(!$istrue){
                            $ho_res =  getHotelDetails($ho_details,  $hotel['hotel_name'],$key);
                            if($ho_res['status']==1){
                                $istrue = true;
                            }
                        }
                           ?>
                            <option <?=(isset($ho_res['name']) && $ho_res['name']==$hotel['hotel_name'])?'selected':''?> data-manager-contact="<?=$hotel['mobile']?>" value="<?php echo $hotel['hotel_name']; ?>"><?php echo  $hotel['hotel_name']; ?></option>
                        <?php endforeach; ?>
                    </select></td>
                <td>
                    <?= $checkIn ?>
                    
            </td>
                <td><?= $checkOut ?></td>
                <td><?= $night?></td>
                <td><?= $hotels[0]['location'] ?></td>
                <td>
                <input type="hidden" name="hotel['mobile'][]" value="<?= (isset($ho_res['mobile']) && $ho_res['mobile'] !== null) ? $ho_res['mobile'] : $hotels[0]['mobile']  ?>" />
                 
                <span><?= (isset($ho_res['mobile']) && $ho_res['mobile'] !== null) ? $ho_res['mobile'] : $hotels[0]['mobile'] ?></span>
            </td>
            </tr>
<?php
        endif;
        $checkIn = $checkOut;
    } 
endforeach; ?>