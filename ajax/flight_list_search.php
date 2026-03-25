<?php
session_start();
require_once '../config/config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = getDbInstance();

// Get form values
$from = isset($_POST['from']) ? trim($_POST['from']) : '';
$to = isset($_POST['to']) ? trim($_POST['to']) : '';
$depart = isset($_POST['depart']) ? trim($_POST['depart']) : '';
$arrival = isset($_POST['arrival']) ? trim($_POST['arrival']) : '';

/**
 * Get flights based on user selection
 */
function getFlightsForPackage($fromId, $destinationId, $departureDate, $arrivalDate)
{
    $db = getDbInstance();
    $db->where('f.status', 'Active');
    $db->where("f.from", $fromId);
    $db->where("f.destination", $destinationId);
    $db->where("DATE(fd.departure_datetime)", $departureDate);
    $db->where("DATE(fd.arrival_datetime)", $arrivalDate);

    $db->join("flight_details fd", "fd.flight_id = f.id", "LEFT");
    $db->join("flight_destination i", "i.id = f.from", "LEFT");
    $db->join("flight_destination d", "d.id = f.destination", "LEFT");

    $flights = $db->get("flight_lists f", null, "f.*, fd.*, d.name AS destination_name, i.name AS from_name");

    return $flights;
}

$flights = getFlightsForPackage($from, $to, $depart, $arrival);

if (empty($flights)) {
    echo ""; // Return empty response if no flights found
    exit;
}

?>
<?php foreach ($flights as $flight) : ?>
    <?php
    $currentDate = date('Y-m-d');
    $flightDate = date('Y-m-d', strtotime($flight['departure_datetime']));
    if ($flightDate !== $currentDate) continue; // Skip if not today

    // Calculate dynamic flight duration
    $departure_time = new DateTime($flight['departure_datetime']);
    $arrival_time = new DateTime($flight['arrival_datetime']);
    $duration = $departure_time->diff($arrival_time);
    $formatted_duration = $duration->h . " hr : " . $duration->i . " mins";
    ?>

    <div class="flight-card">
        <div class="flight-info d-flex justify-content-between align-items-center">
            <!-- Flight Logo -->
            <div class="flight-logo">
                <img src="<?= htmlspecialchars($flight["flight_logo"]) ?>" alt="Flight Logo">
            </div>

            <!-- Departure Info -->
            <div class="text-center">
                <div class="time"><?= date("H:i", strtotime($flight['departure_datetime'])) ?></div>
                <div class="text-muted"><?= date("d-M-Y", strtotime($flight['departure_datetime'])) ?></div>
                <div class="airport-code"><?= htmlspecialchars($flight["from_name"]) ?></div>
            </div>

            <!-- Duration -->
            <div class="dotted-line"></div>
            <div class="duration"><?= $formatted_duration ?></div>
            <div class="dotted-line"></div>

            <!-- Arrival Info -->
            <div class="text-center">
                <div class="time"><?= date("H:i", strtotime($flight['arrival_datetime'])) ?></div>
                <div class="text-muted"><?= date("d-M-Y", strtotime($flight['arrival_datetime'])) ?></div>
                <div class="airport-code"><?= htmlspecialchars($flight["destination_name"]) ?></div>
            </div>

            <div class="vertical-line"></div>
            <!-- Fare & Booking -->
            <div class="text-end">
                <div class="fare">₹ <?= htmlspecialchars($flight['price']) ?></div>
                <button class="btn btn-primary">Book Now</button>
            </div>
        </div>

        <!-- Baggage Info -->
        <div class="baggage-info">
            <!-- Baggage Icon -->
            <i class="fas fa-suitcase text-secondary"></i>
            <?= htmlspecialchars($flight["cabin_baggage"]) ?>KG |

            <!-- Refund Status Icon -->
            <i class="fas fa-times-circle text-danger"></i> Refund: Not Available |

            <!-- Flight Status -->
            <i class="fas fa-user-check text-success"></i> Available Seat: <?= htmlspecialchars($flight["person"]) ?>
        </div>
    </div>
<?php endforeach; ?>