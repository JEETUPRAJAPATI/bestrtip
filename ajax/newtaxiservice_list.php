<?php
session_start();
require_once '../config/config.php';
// require_once BASE_PATH . '/includes/auth_validate.php';
//echo $_POST['package_id'];
//die;
$db = getDbInstance();
//$db->where('type', 'Cumulative');
$db->where('taxi_id', $_POST['package_id']);

$cumulative_service = $db->get("services");

$db = getDbInstance();
$db->where('type', 'Per Person');
$db->where('taxi_id', $_POST['package_id']);

$per_person_service = $db->get("services");

$db = getDbInstance();
$db->where('type', 'Per Service');
$db->where('taxi_id', $_POST['package_id']);

$per_services = $db->get("services");

$tour_date = $_POST['tour_date'];
$days = $_POST['days'];
$date_data = [];
for ($i = 0; $i < $days; $i++) {
    $date_data[] = date('d-m-Y', strtotime($tour_date));
    $tour_date = addOneDay($tour_date);
}

?>


<table class="table">
    <thead class="add-bg">
        <tr>
            <th class="text-white">Service</th>
            <?php foreach ($date_data as $d) { ?>
                <th class="text-white"><?= $d ?></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody class="table-border-bottom-0">
        <?php foreach ($cumulative_service as $cumulative) : ?>
           
             <div class="card-container col-12 col-md-4 col-sm-6 mb-0 mb-sm-0 mt-sm-0 mt-2">
            <div class="card-body addon-card">
                <div class="flex">
                     <!--Image section -->
                    <div class="mr-4">
                        <img src="https://plus.unsplash.com/premium_photo-1676999306178-60a9d07079bf?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8Ymlla3xlbnwwfHwwfHx8MA%3D%3D"
                            alt="Bike Riding" class="card-image">
                    </div>

                     <!--Content section -->
                    <div class="flex flex-col justify-between">
                        <div>
                            <h3 class="title"><?= $cumulative['name'] ?></h3>
                            <p class="subtitle">Enjoy seaside cruising<br>while on trip</p>
                        </div>

                        <div>
                            <div class="flex items-baseline">
                                <span class="price"><?= $cumulative['amount'] ?></span>
                                <span class="per-day">/<?= $cumulative['type'] ?></span>
                            </div>

                            <div class="quantity-control">
                                <button class="quantity-btn">−</button>
                                <span class="quantity">2</span>
                                <button class="quantity-btn">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

             <!--Date selector -->
            <div class="date-selector px-4 pb-3">
                <div class="date-btn">21</div>
                <div class="date-btn">22</div>
                <div class="date-btn">23</div>
                <div class="date-btn">24</div>
                <div class="date-btn">25</div>
                <div class="date-btn">26</div>
                <div class="date-btn">27</div>
            </div>
        </div>
        <?php endforeach ?>

  

    </tbody>
</table>
98230948klasd908809230894
<h2>Per Service</h2>
<div class="per-service-content">
    <?php foreach ($per_services as $per_service) : ?>
        <div class="single">
            <div class="per-service-label">
                <label for="<?= $per_service['name'] ?>"><?= $per_service['name'] ?> </label>
            </div>
            <div class="per-service-input">
                <input onChange="return calculateTotal();" placeholder="10" type="number" min="0" name="per_service[<?= $per_service['id'] ?>][]" amount-per-service="<?= $per_service['amount'] ?>" id="per_service<?= $per_service['id'] ?>">
                <span class="hint">Per Day</span>
            </div>
        </div>
    <?php endforeach ?>
</div>