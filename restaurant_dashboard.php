<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$pid = decryptId($_GET['pid'] ?? '');
$db = getDbInstance();

// Hotel info
$db->where('id', $pid);
$hotel = $db->getOne('properties');

// Restaurant info
$db->where('property_id', $pid);
$restaurants = $db->get('restaurants');

// Stats
$stats = [
    'totalRestaurants' => count($restaurants),
    'totalMenuItems' => 0,
    'totalOrders' => 0,
    'pendingOrders' => 0,
    'todayRevenue' => 0
];

foreach ($restaurants as $rest) {
    $rid = $rest['id'];
    $stats['totalMenuItems'] += (int)$db->where('restaurant_id', $rid)->getValue('restaurant_menu', 'COUNT(*)');
    $stats['totalOrders'] += (int)$db->where('restaurant_id', $rid)->getValue('restaurant_orders', 'COUNT(*)');
    $stats['pendingOrders'] += (int)$db->where('restaurant_id', $rid)->where('status', 'Pending')->getValue('restaurant_orders', 'COUNT(*)');

    $today = date('Y-m-d');
    $stats['todayRevenue'] += (float)$db->where('restaurant_id', $rid)
        ->where('created_at', "$today 00:00:00", '>=')->where('created_at', "$today 23:59:59", '<=')
        ->getValue('restaurant_orders', 'SUM(total_amount)') ?? 0;
}

// Recent orders
$recentOrders = [];

include BASE_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Main Content -->
            <div class="flex-1 overflow-auto">
                <header class="bg-white shadow-sm">
                    <div class="flex justify-between items-center p-4">
                        <h1 class="text-2xl font-bold text-gray-800">
                            <i class="fas fa-utensils text-indigo-600"></i>
                            <?= htmlspecialchars($hotel['hotel_name'] ?? 'Hotel') ?> Restaurant Dashboard
                        </h1>
                    </div>
                </header>

                <main class="p-6">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white rounded-xl shadow-sm p-6 card-hover transition-all">
                            <div class="flex justify-between">
                                <div>
                                    <p class="text-gray-500">Restaurants</p>
                                    <h3 class="text-3xl font-bold text-gray-800"><?= $stats['totalRestaurants'] ?></h3>
                                </div>
                                <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-store text-indigo-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm p-6 card-hover transition-all">
                            <div class="flex justify-between">
                                <div>
                                    <p class="text-gray-500">Menu Items</p>
                                    <h3 class="text-3xl font-bold text-gray-800"><?= $stats['totalMenuItems'] ?></h3>
                                </div>
                                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-utensils text-green-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm p-6 card-hover transition-all">
                            <div class="flex justify-between">
                                <div>
                                    <p class="text-gray-500">Today's Revenue</p>
                                    <h3 class="text-3xl font-bold text-gray-800">₹<?= number_format($stats['todayRevenue'], 2) ?></h3>
                                </div>
                                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-rupee-sign text-blue-600 text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm p-6 card-hover transition-all">
                            <div class="flex justify-between">
                                <div>
                                    <p class="text-gray-500">Pending Orders</p>
                                    <h3 class="text-3xl font-bold text-gray-800"><?= $stats['pendingOrders'] ?></h3>
                                </div>
                                <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center">
                                    <i class="fas fa-clipboard-list text-yellow-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <a href="list_restaurant.php?pid=<?= encryptId($pid) ?>" class="bg-white p-4 rounded-xl shadow-sm flex flex-col items-center justify-center text-center card-hover transition-all hover:bg-indigo-50">
                                <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center mb-3">
                                    <i class="fas fa-plus-circle text-indigo-600 text-xl"></i>
                                </div>
                                <span class="font-medium">Restaurant</span>
                            </a>
                            <a href="add_menu.php?pid=<?= encryptId($pid) ?>" class="bg-white p-4 rounded-xl shadow-sm flex flex-col items-center justify-center text-center card-hover transition-all hover:bg-green-50">
                                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center mb-3">
                                    <i class="fas fa-utensils text-green-600 text-xl"></i>
                                </div>
                                <span class="font-medium">Add Menu Item</span>
                            </a>

                            <a href="restaurant_orders.php?pid=<?= encryptId($pid) ?>" class="bg-white p-4 rounded-xl shadow-sm flex flex-col items-center justify-center text-center card-hover transition-all hover:bg-blue-50">
                                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                                    <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                                </div>
                                <span class="font-medium">View Orders</span>
                            </a>
                            <a href="add_order.php?pid=<?= encryptId($pid) ?>" class="bg-white p-4 rounded-xl shadow-sm flex flex-col items-center justify-center text-center card-hover transition-all hover:bg-purple-50">
                                <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center mb-3">
                                    <i class="fas fa-receipt text-purple-600 text-xl"></i>
                                </div>
                                <span class="font-medium">Add New Order</span>
                            </a>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b">
                            <h2 class="text-xl font-bold text-gray-800">Recent Orders</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($recentOrders)): ?>
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No recent orders found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentOrders as $order): ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?= $order['id'] ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($order['customer_name']) ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹<?= number_format($order['total_amount'], 2) ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <?php
                                                    $statusClass = [
                                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                                        'Completed' => 'bg-green-100 text-green-800',
                                                        'Cancelled' => 'bg-red-100 text-red-800'
                                                    ][$order['status']] ?? 'bg-gray-100 text-gray-800';
                                                    ?>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                                        <?= $order['status'] ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3 view-order" data-id="<?= $order['id'] ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="restaurant_kot.php?oid=<?= $order['id'] ?>" class="text-blue-600 hover:text-blue-900">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- Order Details Modal -->
        <div class="fixed inset-0 overflow-y-auto z-50 hidden" id="orderModal">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modalTitle">
                                    Order Details
                                </h3>
                                <div class="mt-2" id="orderDetails">
                                    Loading order details...
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm close-modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
        <script>
            $(document).ready(function() {
                // Revenue Chart
                const ctx = document.getElementById('revenueChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                            datasets: [{
                                label: 'Revenue',
                                data: [0, 10000, 5000, 15000, 10000, 20000, 15000, 25000, 20000, 30000, 25000, 40000],
                                backgroundColor: 'rgba(79, 70, 229, 0.05)',
                                borderColor: 'rgba(79, 70, 229, 1)',
                                borderWidth: 2,
                                tension: 0.1,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return '₹' + context.raw.toLocaleString();
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return '₹' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Order view modal
                $('.view-order').click(function(e) {
                    e.preventDefault();
                    const orderId = $(this).data('id');
                    $('#orderModal').removeClass('hidden');

                    $.ajax({
                        url: 'get_order_details.php',
                        method: 'GET',
                        data: {
                            order_id: orderId
                        },
                        success: function(response) {
                            $('#orderDetails').html(response);
                        },
                        error: function() {
                            $('#orderDetails').html('<div class="text-red-500">Failed to load order details.</div>');
                        }
                    });
                });

                $('.close-modal').click(function() {
                    $('#orderModal').addClass('hidden');
                });
            });
        </script>
    </div>
</div>

</html>