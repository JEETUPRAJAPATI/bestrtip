<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$id = isset($_GET['crm']) ? decryptId($_GET['crm']) : '';
if (!$id) exit('Invalid property ID.');

$db = getDbInstance();
$prop = $db->where('id', $id)->getOne('properties', ['hotel_name', 'no_of_rooms'])
    or exit('Property not found.');

$days = [];
for ($i = 0; $i < 7; $i++) $days[] = date('Y-m-d', strtotime("+$i days"));

$summary = [];
foreach ($days as $d) {
    $total = (int)$prop['no_of_rooms'];
    $db->where('property_id', $id)
        ->where('check_in_date', $d, '<=')
        ->where('check_out_date', $d, '>')
        ->where('status', 'Confirmed');
    $bks = $db->get('property_booking', null, ['double_room_count', 'extra_bed_count', 'child_no_bed_count', 'single_room_count', 'total_amount']);
    $sold = array_sum(array_map(fn($b) => $b['double_room_count'] + $b['extra_bed_count'] + $b['child_no_bed_count'] + $b['single_room_count'], $bks));
    $rev  = array_sum(array_column($bks, 'total_amount'));
    $summary[] = compact('d', 'total', 'sold', 'rev');
}
include BASE_PATH . '/includes/header.php';
?>
<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <h2 class="fw-bold mb-3"><?= htmlspecialchars($prop['hotel_name']) ?> – 7‑Day Summary</h2>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <canvas id="chartSummary" height="300"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Daily Data</h5>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($summary as $row): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold"><?= date('D, d M', strtotime($row['d'])) ?></div>
                                        <small class="text-muted">Sold: <?= $row['sold'] ?> / <?= $row['total'] ?> | Avail: <?= $row['total'] - $row['sold'] ?></small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">₹<?= number_format($row['rev'], 0) ?></div>
                                        <small class="text-muted">Revenue</small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include BASE_PATH . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const data = <?= json_encode(array_map(fn($r) => [
                        'label' => date('D', strtotime($r['d'])),
                        'sold' => $r['sold'],
                        'avail' => $r['total'] - $r['sold'],
                        'rev' => $r['rev']
                    ], $summary)) ?>;
    const ctx = document.getElementById('chartSummary').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.label),
            datasets: [{
                    label: 'Sold',
                    data: data.map(d => d.sold),
                    backgroundColor: '#4e73df'
                },
                {
                    label: 'Available',
                    data: data.map(d => d.avail),
                    backgroundColor: '#1cc88a'
                },
                {
                    label: 'Revenue',
                    data: data.map(d => d.rev),
                    type: 'line',
                    yAxisID: 'revAxis',
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231,74,59,0.1)',
                    tension: 0.3
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Rooms'
                    }
                },
                revAxis: {
                    beginAtZero: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Revenue (₹)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>