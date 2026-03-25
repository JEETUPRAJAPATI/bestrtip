<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Initialize Dompdf with options
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// Load HTML content
$html = '<!DOCTYPE html>
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
        <div class="g-detail">
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
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 14%;">12/02/2024</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 14%;">12/03/2024</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 14%;">30</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 14%;">Half Board</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 14%;">Not included</td>
                    <td style="width: 14%; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">CNF</td>
                </tr>
            </table>
        </div>
        <div class="itineary-details">
            <h3 style="padding: 10px; background: #dfdfd5;">Itineary Details</h3>
            <table cellpadding="0" cellspacing="0" style="width: 100%; border-top: solid 1px #333;">
                <tr style="border-bottom: solid 1px #333;">
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%; font-weight: bold;">Day</td>
                    <td style="border-right: solid 1px #333; width: 13%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">Date</td>
                    <td style="border-right: solid 1px #333; width: 13%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">Day</td>
                    <td style="width: 61%; font-weight: bold; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333;">Short Itineary</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">Day 1</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">30-08-2024</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">Friday</td>
                    <td style="width: 61%; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">Arrive In Srinagar</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">Day 2</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">31-08-2024</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">Saturday</td>
                    <td style="width: 61%; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">You might have a brilliant tour plan in mind, but is it realistic? When creating a tour from scratch it’s wise to understand your competition and whether your ideas are realistic. So while the possibilities are endless, building sustainable tour business needs to look at the competition in your area.</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">Day 3</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">01-09-2024</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">Sunday</td>
                    <td style="width: 61%; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">You might have a brilliant tour plan in mind, but is it realistic? When creating a tour from scratch it’s wise to understand your competition and whether your ideas are realistic. So while the possibilities are endless, building sustainable tour business needs to look at the competition in your area.</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">Day 4</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">02-09-2024</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">Monday</td>
                    <td style="width: 61%; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">You might have a brilliant tour plan in mind, but is it realistic? When creating a tour from scratch it’s wise to understand your competition and whether your ideas are realistic. So while the possibilities are endless, building sustainable tour business needs to look at the competition in your area.</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">Day 5</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">03-09-2024</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 13%;">Tuesday</td>
                    <td style="width: 61%; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">You might have a brilliant tour plan in mind, but is it realistic? When creating a tour from scratch it’s wise to understand your competition and whether your ideas are realistic. So while the possibilities are endless, building sustainable tour business needs to look at the competition in your area.</td>
                </tr>
            </table>
        </div>
        <div class="inclusion-exclusion-details">
            <h3 style="padding: 10px; background: #dfdfd5;">Inclusions & Exclusions</h3>
            <table cellpadding="0" cellspacing="0" style="width: 100%; border-top: solid 1px #333;">
                <tr style="border-bottom: solid 1px #333;">
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 50%; font-weight: bold;">Inclusions</td>
                    <td style="width: 50%; font-weight: bold; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333;">Exclusions</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 50%;">Permit</td>
                    <td style="width: 50%; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">Bike</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 50%;">Guide</td>
                    <td style="width: 50%; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">Lunch</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 50%;">Bonfire</td>
                    <td style="width: 50%; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">Water Bottles</td>
                </tr>
            </table>
        </div>
        <div class="itineary-details">
            <h3 style="padding: 10px; background: #dfdfd5;">Transport Details</h3>
            <table cellpadding="0" cellspacing="0" style="width: 100%; border-top: solid 1px #333;">
                <tr style="border-bottom: solid 1px #333;">
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 20%; font-weight: bold;">Category</td>
                    <td style="border-right: solid 1px #333; width: 20%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">No. of Vehicle</td>
                    <td style="border-right: solid 1px #333; width: 20%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">Driver Name</td>
                    <td style="border-right: solid 1px #333; width: 20%; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">Mobile No.</td>
                    <td style="width: 20%; font-weight: bold; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333;">Region</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 20%;">Innova</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 20%;">2</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 20%;">Kumer</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 20%;">9999999999</td>
                    <td style="width: 20%; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">Leh</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 20%;">Innova</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 20%;">2</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 20%;">Kumer</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; width: 20%;">9999999999</td>
                    <td style="width: 20%; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">Leh</td>
                </tr>
            </table>
        </div>
        <div class="itineary-details">
            <h3 style="padding: 10px; background: #dfdfd5;">Hotel Details</h3>
            <table cellpadding="0" cellspacing="0" style="width: 100%; border-top: solid 1px #333;">
                <tr style="border-bottom: solid 1px #333;">
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px; font-weight: bold;">Day</td>
                    <td style="border-right: solid 1px #333; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">Hotel Name</td>
                    <td style="border-right: solid 1px #333; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold; width: 65px;">Check In</td>
                    <td style="border-right: solid 1px #333; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold; width: 65px;">Check Out</td>
                    <td style="border-right: solid 1px #333; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">Night</td>
                    <td style="border-right: solid 1px #333; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold; width: 60px;">Meal Plan</td>
                    <td style="border-right: solid 1px #333; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333; font-weight: bold;">Location</td>
                    <td style="font-weight: bold; padding: 5px; padding-left: 10px; border-bottom: solid 1px #333;">Contact</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">1</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">Hotel Radison Large hotel name</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">04-05-2024</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">06-05-2024</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">2</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">MAP</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">Leh</td>
                    <td style="padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">9999999999</td>
                </tr>
                <tr>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">2</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">Hotel Radison</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">04-05-2024</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">06-05-2024</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">2</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">MAP</td>
                    <td style="border-right: solid 1px #333; padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">Leh</td>
                    <td style="padding: 5px; border-bottom: solid 1px #333; padding-left: 10px;">9999999999</td>
                </tr>
               
            </table>
        </div>
    </div>
</body>
</html>';

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
