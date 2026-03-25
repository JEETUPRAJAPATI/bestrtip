<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Initialize Dompdf with options
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// Define HTML content directly
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Voucher</title>
    <style type="text/css">
        html, body{
            margin: 0;
            padding: 0;
        }
        .wrapper{
            width: 620px;
            height: 100%;
            border: solid 1px #333;
            margin: 0 auto;
            margin-top: 10px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }
        .v-head{
            border-bottom: solid 1px #333;
        }
        h1, h2, h3, h4, h5, h6, p{
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="v-head">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                <tr>
                    <td style="border-right: solid 1px #333; padding: 10px; width: 70%;">
                        <h3 style="margin-bottom: 8px;">Hotel Voucher</h3>
                        <p style="margin-bottom: 3px;">Go2ladakh Booking ID: <strong>GLKH209301</strong></p>
                        <p style="margin-bottom: 3px;">Hotel ID or Name: <strong>Taj Hotel</strong></p>
                        <p style="margin-bottom: 3px;">Booking Date: <strong>20 June 2024</strong></p>
                    </td>
                    <td style="text-align: right; width: 30%; padding: 10px;">
                        <img src="black-logo.png" alt="go2ladakh-logo" width="150px" />
                    </td>
                </tr>
            </table>
        </div>
        <div class="guest-detail">
            <h3 style="padding: 10px; background: #dfdfd5;">Group Details</h3>
            <table cellpadding="0" cellspacing="0" style="width: 100%; border-top: solid 1px #333;">
                <tr style="border-bottom: solid 1px #333;">
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 33%; font-weight: bold;">Guest/Group Name</td>
                    <td style="border-right: solid 1px #333; width: 33%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">No. of Guest</td>
                    <td style="width: 33%; font-weight: bold; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333;">Meal Request</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; border-bottom: solid 1px #333; padding: 5px; padding-left: 10px; width: 33%;">Arvind Family</td>
                    <td style="border-right: solid 1px #333; border-bottom: solid 1px #333; padding: 5px; padding-left: 10px; width: 33%;">10</td>
                    <td style="width: 33%; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">No</td>
                </tr>
            </table>
        </div>
        <div class="r-details">
            <h3 style="padding: 10px; background: #dfdfd5;">Rooming Details</h3>
            <table cellpadding="0" cellspacing="0" style="width: 100%; border-top: solid 1px #333;">
                <tr style="border-bottom: solid 1px #333;">
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 14%; font-weight: bold;">Twin</td>
                    <td style="border-right: solid 1px #333; width: 14%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">Extra bed</td>
                    <td style="border-right: solid 1px #333; width: 14%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">CWB</td>
                    <td style="border-right: solid 1px #333; width: 14%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">CNB</td>
                    <td style="border-right: solid 1px #333; width: 14%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">Infant</td>
                    <td style="border-right: solid 1px #333; width: 14%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">Single</td>
                    <td style="width: 14%; font-weight: bold; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333;">Quad</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 14%;">2</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 14%;">2</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 14%;">2</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 14%;">2</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 14%;">2</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 14%;">2</td>
                    <td style="width: 14%; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">2</td>
                </tr>
            </table>
        </div>
        <div class="r-details">
            <h3 style="padding: 10px; background: #dfdfd5;">Check In/Check Out Details</h3>
            <table cellpadding="0" cellspacing="0" style="width: 100%; border-top: solid 1px #333;">
                <tr style="border-bottom: solid 1px #333;">
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 14%; font-weight: bold;">Check In</td>
                    <td style="border-right: solid 1px #333; width: 14%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">Check Out</td>
                    <td style="border-right: solid 1px #333; width: 14%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">No. of Night</td>
                    <td style="border-right: solid 1px #333; width: 14%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">Meal Plan</td>
                    <td style="border-right: solid 1px #333; width: 14%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">Lunch</td>
                    <td style="width: 14%; font-weight: bold; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333;">Status</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; padding: 5px; padding-left: 10px; width: 14%;">12/02/2024</td>
                    <td style="border-right: solid 1px #333; padding: 5px; padding-left: 10px; width: 14%;">12/03/2024</td>
                    <td style="border-right: solid 1px #333; padding: 5px; padding-left: 10px; width: 14%;">30</td>
                    <td style="border-right: solid 1px #333; padding: 5px; padding-left: 10px; width: 14%;">Half Board</td>
                    <td style="border-right: solid 1px #333; padding: 5px; padding-left: 10px; width: 14%;">Not included</td>
                    <td style="width: 14%; padding: 5px; padding-left: 10px;">CNF</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
';

// Load the HTML into Dompdf
$dompdf->loadHtml($html);

// (Optional) Set up the paper size and orientation
$dompdf->setPaper('A4', 'portrait'); // 'portrait' or 'landscape'

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('document.pdf', [
    'Attachment' => false // Set to true to download the PDF file, false to display in the browser
]);