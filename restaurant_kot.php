<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Restaurant Invoice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            padding: 40px;
            color: #000;
        }

        .invoice-box {
            width: 100%;
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            background: #fff;
        }

        .logo {
            max-height: 60px;
        }

        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .restaurant-details,
        .invoice-details {
            line-height: 1.5;
        }

        h2 {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }

        .totals table {
            border: none;
        }

        .totals td {
            border: none;
            padding: 6px 0;
        }

        .footer {
            clear: both;
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }

        .buttons {
            margin-top: 20px;
            text-align: center;
        }

        .btn {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            margin: 0 10px;
            cursor: pointer;
        }

        .guest-info {
            display: flex;
            gap: 20px;
            margin: 15px 0;
        }

        .guest-info div {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            flex: 1;
        }

        @media print {
            .buttons {
                display: none;
            }

            body {
                padding: 0;
            }

            .invoice-box {
                border: none;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="top-section">
            <div class="restaurant-details">
                <h2><?= htmlspecialchars($restaurant['name']) ?></h2>
                <div>Your Restaurant Slogan</div>
                <div>Street Address</div>
                <div>City, ST ZIP Code</div>
                <div>Phone: 503-555-0190</div>
            </div>
            <div class="invoice-details">
                <img src="/path/to/logo.png" class="logo" alt="Logo">
                <div><strong>Date:</strong> <?= date('M j, Y') ?></div>
                <div><strong>Invoice #</strong> <?= $order['order_code'] ?></div>
                <div><strong>Time:</strong> <?= date('g:i A') ?></div>
                <div><strong>Persons:</strong> <?= $order['no_of_persons'] ?? 1 ?></div>
            </div>
        </div>

        <div class="guest-info">
            <div>
                <strong>Bill To:</strong> <?= htmlspecialchars($order['guest_name']) ?>
            </div>
            <div>
                <strong>Table No:</strong> <?= !empty($order['table_no']) ? htmlspecialchars($order['table_no']) : 'N/A' ?>
            </div>
            <div>
                <strong>Room No:</strong> <?= !empty($order['room_no']) ? htmlspecialchars($order['room_no']) : 'N/A' ?>
            </div>
        </div>

        <div><strong>Server:</strong> <?= $_SESSION['user_name'] ?? 'Staff' ?></div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name']) ?></td>
                        <td><?= htmlspecialchars($item['type']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td>$<?= number_format($item['quantity'] * $item['price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td>$<?= number_format($subtotal, 2) ?></td>
                </tr>
                <tr>
                    <td><strong>Tax (<?= $taxRate ?>%):</strong></td>
                    <td>$<?= number_format($taxAmount, 2) ?></td>
                </tr>
                <tr>
                    <td><strong>Other:</strong></td>
                    <td>$<?= number_format($otherCharges, 2) ?></td>
                </tr>
                <tr>
                    <td><strong>Total:</strong></td>
                    <td><strong>$<?= number_format($total, 2) ?></strong></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Make all checks payable to <?= htmlspecialchars($restaurant['name']) ?> <br>
            Total due in 15 days. Overdue accounts incur 1% monthly service charge.
        </div>

        <div class="buttons">
            <button onclick="window.print()" class="btn">Print Invoice</button>
            <a href="order_list.php" class="btn">Back to Orders</a>
        </div>
    </div>
</body>

</html>