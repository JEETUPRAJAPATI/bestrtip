<?php
session_start();
require_once 'config/config.php';
require_once BASE_PATH . '/includes/auth_validate.php';
require 'vendor/autoload.php'; // Make sure PhpSpreadsheet is installed via Composer
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the TCPDF library

require_once BASE_PATH . '/vendor/tecnickcom/tcpdf/tcpdf.php';
$db = getDbInstance();
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if (!$order_id) {
    $_SESSION['failure'] = 'No order specified';
    header('Location: purchase_list.php');
    exit();
}

// Get order data
$db->where('id', $order_id);
$order = $db->getOne('purchase_orders');

if (!$order) {
    $_SESSION['failure'] = 'Order not found';
    header('Location: purchase_list.php');
    exit();
}

// Get order items
$db->where('order_id', $order_id);
$items = $db->get('purchase_order_items');

// Get vendor data if exists
$vendor = null;
if ($order['vendor_id']) {
    $db->where('id', $order['vendor_id']);
    $vendor = $db->getOne('vendor');
}

// Create new PDF document
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Company');
$pdf->SetTitle('Purchase Order #' . $order['order_number']);
$pdf->SetSubject('Purchase Order');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins
$pdf->SetMargins(15, 15, 15);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', 'B', 16);

// Title
$pdf->Cell(0, 10, 'Purchasing order By Manager kitchen/ team', 0, 1, 'C');

// Order header information
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Date: ' . date('d/m/Y', strtotime($order['order_date'])), 0, 1);
$pdf->Cell(0, 10, 'Order By Name: ' . $order['order_by_name'], 0, 1);
$pdf->Cell(0, 10, 'Order No: ' . $order['order_number'], 0, 1);
$pdf->Cell(0, 10, 'Signature: _________________________', 0, 1);

// Add space
$pdf->Ln(5);

// Create the items table
$pdf->SetFont('helvetica', 'B', 12);
$header = array('Srl', 'Item', 'Qty', '/kg/Ltr', 'Date', 'Status');
$w = array(10, 80, 15, 15, 30, 30);

// Header
for ($i = 0; $i < count($header); $i++) {
    $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
}
$pdf->Ln();

// Items
$pdf->SetFont('helvetica', '', 12);
foreach ($items as $index => $item) {
    $pdf->Cell($w[0], 6, $index + 1, 'LR', 0, 'C');
    $pdf->Cell($w[1], 6, $item['item_name'], 'LR');
    $pdf->Cell($w[2], 6, $item['quantity'], 'LR', 0, 'C');
    $pdf->Cell($w[3], 6, $item['unit'], 'LR', 0, 'C');
    $pdf->Cell($w[4], 6, $item['date_needed'] ? date('d/m/Y', strtotime($item['date_needed'])) : '', 'LR', 0, 'C');
    $pdf->Cell($w[5], 6, ucfirst($item['status']), 'LR', 0, 'C');
    $pdf->Ln();
}

// Close the table
$pdf->Cell(array_sum($w), 0, '', 'T');
$pdf->Ln(10);

// Delivery information
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Ordered Hanover By', 0, 1);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Date: ' . ($order['delivery_date'] ? date('d/m/Y', strtotime($order['delivery_date'])) : '_________'), 0, 1);
$pdf->Cell(0, 10, 'Delivered By Name: ' . ($order['delivered_by_name'] ?: '_________'), 0, 1);
$pdf->Cell(0, 10, 'Signature: _________________________', 0, 1);
$pdf->Cell(0, 10, 'Bill attached Must: ' . ($order['bill_attached'] ? 'Yes' : 'No'), 0, 1);

// Vendor information if exists
if ($vendor) {
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Vendor Information:', 0, 1);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Name: ' . $vendor['name'], 0, 1);
    $pdf->Cell(0, 10, 'Contact: ' . ($order['vendor_contact'] ?: $vendor['mobile']), 0, 1);
}

// Output the PDF
$pdf->Output('purchase_order_' . $order['order_number'] . '.pdf', 'D');
