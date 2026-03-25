<?php
session_start();
require_once '../config/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = getDbInstance();

// Fetch active destinations
$destinations = $db->where('status', 'Active')->get("destination");

// Fetch active packages
$db->join("destination as dest", "dest.id = packages.destination", "LEFT");
$db->join("traveling_from as source", "source.id = packages.traveling_from", "LEFT");
$db->where("packages.status", "Active");
$db->where("packages.id", $_POST['package_id']);
$package = $db->getOne("packages", "packages.*, dest.name as destination_name, source.name as traveling_from_name");


$db = getDbInstance();
$db->orderBy("name", "asc");
$destinations = $db->get("flight_destination", null, 'id, name');
// Function to get flights for a specific package
function getFlightsForPackage($fromName, $destinationName)
{
    global $db;

    $fromName = $fromName ?? ''; // Ensure it's not null
    $destinationName = $destinationName ?? ''; // Ensure it's not null

    $db->where('f.status', 'Active');
    $db->where("LOWER(i.name)", strtolower((string) $fromName)); // Case-insensitive match
    $db->where("LOWER(d.name)", strtolower((string) $destinationName)); // Case-insensitive match
    $db->join(
        "flight_destination i",
        "i.id = f.from",
        "LEFT"
    );
    $db->join("flight_destination d", "d.id = f.destination", "LEFT");

    return $db->get("flight_lists f", null, "f.*, d.name AS destination_name, i.name AS from_name");
}
$flights = getFlightsForPackage($package['traveling_from_name'], $package['destination_name']);
// echo "<pre>";
// print_r($flights);
// die();
?>
<h2 class="">Airline Booking Form</h2>
<div class="fixed-service">
    <div class="colm">
        <label class="container"> Hide
            <input class="custom-check" type="checkbox" onClick="return hideFlightSection();" name="permit" id="permit">
            <span class="checkmark"></span>
        </label>
    </div>

</div>
<!-- Search Box -->
<div class="search-box">
    <div class="form-group">
        <label><i class="fas fa-plane-departure icon"></i> From</label>
        <select class="form-select" id="flight_from">
            <option selected>Select Departure</option>
            <?php foreach ($destinations as $location): ?>
                <option value="<?= $location['id']; ?>" <?= ($_POST['traveling_from'] == $location['id']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($location['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="swap-icon">
        <i class="fas fa-exchange-alt"></i>
    </div>

    <div class="form-group">
        <label><i class="fas fa-plane-arrival icon"></i> To</label>
        <select class="form-select" id="flight_destination" required>
            <option disabled selected>Select Destination</option>
            <?php foreach ($destinations as $destination): ?>
                <option value="<?= $destination['id']; ?>" <?= ($_POST['destination'] == $destination['id']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($destination['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label><i class="fas fa-calendar-alt icon"></i> Depart</label>
        <input type="date" class="form-control" id="departure_date" value="<?= $currentDate ?>">
    </div>

    <div class="form-group">
        <label><i class="fas fa-calendar-alt icon"></i> Return</label>
        <input type="date" class="form-control" id="return_date">
    </div>

    <div class="form-group">
        <label><i class="fas fa-user icon"></i> Travellers & Cabin</label>
        <select class="form-select">
            <option selected>1 Adult, Economy</option>
            <option>2 Adults, Economy</option>
            <option>1 Adult, Business</option>
            <option>2 Adults, Business</option>
        </select>
    </div>

    <button class="search-btn"><i class="fas fa-search"></i> Search</button>
</div>