<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$property_id = decryptId($_GET['crm']);
$db = getDbInstance();

// STEP 1: Fetch bookings for the property
$bookings = $db->where('property_id', $property_id)->get('property_booking');

// STEP 2: Prepare day-wise summary
$calendarSummary = [];
foreach ($bookings as $booking) {
    $start = new DateTime($booking['check_in_date']);
    $end = (new DateTime($booking['check_out_date']))->modify('+1 day');
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end);

    foreach ($period as $date) {

        $d = $date->format('Y-m-d');
        if (!isset($calendarSummary[$d])) {
            $calendarSummary[$d] = ['sales' => 0, 'statuses' => []];
        }
        $calendarSummary[$d]['sales'] += $booking['total_amount'];
        $calendarSummary[$d]['statuses'][] = $booking['status'];
    }
}

// STEP 3: Create event entries for each day
$calendarEvents = [];
$startDate = new DateTime('first day of this month');
$endDate = (new DateTime('last day of next month'))->modify('+1 day');
$interval = new DateInterval('P1D');
$days = new DatePeriod($startDate, $interval, $endDate);

foreach ($days as $day) {
    $date = $day->format('Y-m-d');

    $status = 'No Booking';
    $color = '#fd7e14'; // Orange

    if (isset($calendarSummary[$date])) {
        $statuses = array_unique($calendarSummary[$date]['statuses']);
        $sales = $calendarSummary[$date]['sales'];

        if (in_array('Confirmed', $statuses) && count($statuses) === 1) {
            $status = 'Fully Booked';
            $color = '#F44336'; // red
        } elseif (in_array('Confirmed', $statuses) || in_array('Hold', $statuses)) {
            $status = 'Some Available';
            $color = '#28a745'; // green
        } else {
            $status = 'No Booking';
            $color = '#F44336'; // orange
        }

        $calendarEvents[] = [
            'title' => "₹{$sales} - {$status}",
            'start' => $date,
            'display' => 'background',
            'backgroundColor' => $color,
            'extendedProps' => [
                'date' => $date,
                'sales' => $sales,
                'status' => $status
            ]
        ];
    } else {
        // No bookings for this date
        $calendarEvents[] = [
            'title' => '',
            'start' => $date,
            'display' => 'background',
            'backgroundColor' => '#fd7e14', // Orange
            'extendedProps' => [
                'date' => $date,
                'sales' => 0,
                'status' => 'No Booking'
            ]
        ];
    }
}

include BASE_PATH . '/includes/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
<style>
    #calendar td {
        height: 120px !important;
        vertical-align: top;
        padding: 8px;
        font-size: 1rem;
    }

    .fc-daygrid-day-number {
        font-weight: bold;
        font-size: 1.3rem;
    }

    .legend-box span {
        margin: 10px;
        padding: 6px 12px;
        border-radius: 4px;
        color: #fff;
        font-size: 0.9rem;
        display: inline-block;
    }

    .confirmed {
        background-color: #dc3545;
    }

    .hold {
        background-color: #28a745;
    }

    .enquiry {
        background-color: #fd7e14;
    }
</style>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="mb-4 text-center">Booking Availability Calendar</h4>
            <div id="calendar"></div>

            <div class="legend-box text-center mt-4">
                <span class="confirmed">🔴 Fully Booked</span>
                <span class="hold">🟢 Some Rooms Available</span>
                <span class="enquiry">🟠 No Booking</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bookingModalLabel">Day Summary</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="bookingModalBody">
                <!-- Booking data will be injected here -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            events: <?= json_encode($calendarEvents) ?>,
            eventContent: function(arg) {
                // Customize how events are rendered
                return {
                    html: `<div class="fc-event-title" style="color: black; font-weight: bold; padding: 2px;">${arg.event.title}</div>`
                };
            },
            dateClick: function(info) {
                const clickedDate = info.dateStr;
                const matched = calendar.getEvents().find(ev => ev.startStr === clickedDate);

                if (!matched) return;

                const e = matched.extendedProps;
                document.getElementById('bookingModalBody').innerHTML = `
                <p><strong>Date:</strong> ${e.date}</p>
                <p><strong>Status:</strong> ${e.status}</p>
                <p><strong>Total Sales:</strong> ₹${e.sales}</p>
            `;
                new bootstrap.Modal(document.getElementById('bookingModal')).show();
            }
        });

        calendar.render();
    });
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>