<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';

$pid = decryptId($_GET['pid'] ?? '');
$rid = $_GET['rid'] ?? '';
$db = getDbInstance();

// Hotel info
$db->where('id', $pid);
$hotel = $db->getOne('properties');

// Get restaurants for this property
$db->where('property_id', $pid);
$restaurants = $db->get('restaurants');

// Get orders
$orders = [];
if (!empty($restaurants)) {
    $restIds = array_column($restaurants, 'id');

    if ($rid) {
        $db->where('restaurant_id', $rid);
    } else {
        $db->where('restaurant_id', $restIds, 'IN');
    }

    $db->orderBy('created_at', 'DESC');
    $orders = $db->get('restaurant_orders');
}

include BASE_PATH . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 0.375rem;
            right: 0;
        }

        .dropdown-menu a {
            color: #4b5563;
            padding: 0.5rem 1rem;
            text-decoration: none;
            display: block;
            font-size: 0.875rem;
        }

        .dropdown-menu a:hover {
            background-color: #f3f4f6;
        }

        .dropdown-menu.show {
            display: block;
        }

        .action-dropdown {
            position: relative;
            display: inline-block;
        }

        .action-btn {
            @apply inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200;
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-100">


    <div class="layout-page">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="false">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-black" href="javascript:;">Pages</a></li>
                        <li class="breadcrumb-item text-sm text-black active" aria-current="page">Restaurant Orders</li>
                    </ol>
                    <h6 class="font-weight-bolder text-black mb-0">Restaurant Orders</h6>
                </nav>
                <?php include BASE_PATH . '/includes/navbar.php'; ?>
            </div>
        </nav>
        <!-- End Navbar -->

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6>All Orders</h6>
                                <a href="add_order.php?pid=<?= encryptId($pid) ?><?= $rid ? '&rid=' . $rid : '' ?>"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-1"></i> Add New Order
                                </a>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="restaurantFilter" class="form-control-label">Filter by Restaurant</label>
                                        <select id="restaurantFilter" class="form-control">
                                            <option value="">All Restaurants</option>
                                            <?php foreach ($restaurants as $restaurant): ?>
                                                <option value="<?= $restaurant['id'] ?>" <?= $rid == $restaurant['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($restaurant['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Order #</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Order Code</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Guest</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Table</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($orders)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center p-6">
                                                    <p class="text-sm text-secondary mb-0">No orders found</p>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($orders as $order): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex px-2 py-1">
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <h6 class="mb-0 text-sm">#<?= $order['id'] ?></h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0"><?= $order['order_code'] ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0"><?= htmlspecialchars($order['guest_name']) ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0"><?= $order['table_no'] ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">₹<?= number_format($order['total_amount'], 2) ?></p>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusClass = [
                                                            'Pending' => 'bg-warning',
                                                            'Served' => 'bg-success',
                                                            'Cancelled' => 'bg-danger'
                                                        ][$order['status']] ?? 'bg-secondary';
                                                        ?>
                                                        <span class="badge badge-sm <?= $statusClass ?>">
                                                            <?= $order['status'] ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">
                                                            <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?>
                                                        </p>
                                                    </td>
                                                    <td class="align-middle">

                                                        <a class="dropdown-item" href="edit_order.php?crm=<?= encryptId($order['id']) ?>">
                                                            <i class="fas fa-edit me-1"></i> Edit
                                                        </a>
                                                        <a class="dropdown-item" href="restaurant_kot.php?oid=<?= $order['id'] ?>">
                                                            <i class="fas fa-print me-1"></i> Print KOT
                                                        </a>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="confirmDelete(<?= $order['id'] ?>)">
                                                            <i class="fas fa-trash me-1"></i> Delete
                                                        </a>

                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Order Details Modal -->
        <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Order Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="orderDetails">
                        Loading order details...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <?php include BASE_PATH . '/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize dropdown toggles
            $('.dropdown-toggle').click(function(e) {
                e.stopPropagation();
                $(this).next('.dropdown-menu').toggleClass('show');
            });

            // Close dropdowns when clicking outside
            $(document).click(function() {
                $('.dropdown-menu').removeClass('show');
            });

            // Filter by restaurant
            $('#restaurantFilter').change(function() {
                const restaurantId = $(this).val();
                const url = new URL(window.location.href);

                if (restaurantId) {
                    url.searchParams.set('rid', restaurantId);
                } else {
                    url.searchParams.delete('rid');
                }

                window.location.href = url.toString();
            });

            // Order view modal
            $('.view-order').click(function(e) {
                e.preventDefault();
                const orderId = $(this).data('id');
                $('#orderModal').modal('show');

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
                        $('#orderDetails').html('<div class="alert alert-danger">Failed to load order details.</div>');
                    }
                });
            });

            function confirmDelete(orderId) {
                if (confirm('Are you sure you want to delete this order?')) {
                    window.location.href = 'delete_order.php?id=' + orderId;
                }
            }
        });
    </script>
</body>

</html>