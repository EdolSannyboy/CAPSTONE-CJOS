<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    http_response_code(403);
    include 'access_guard.php';
    exit;
}
require_once 'init.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
require_once '../dompdf/autoload.inc.php';

require_once 'db_config.php';
require_once 'classes.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$db = new db_class();

$system_settings = $db->getActiveSystemSettings();
$system_info = null;

if ($system_settings && $system_settings->num_rows === 0) {
    $system_info = [
        'system_name' => 'Default System Name',
        'address' => 'Default Address',
        'contact' => '09123456789',
        'email' => 'mail@gmail.com',
        'about_us' => 'Sample description',
        'logo' => 'avatar.png'
    ];
} elseif ($system_settings) {
    $system_info = $system_settings->fetch_assoc();
} else {
    // Handle the case where $system_settings is null or false
}

$logoPath = __DIR__ . '/../assets/img/logo/' . $system_info['logo'];

if (!file_exists($logoPath)) {
    $logoPath = __DIR__ . '/../assets/img/logo/default_logo.png';
}

$operator_ID = $_SESSION['user_ID'] ?? null;
$loggedInUserType = $_SESSION['user_type'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['export_orders_record_pdf'])) {
        $order_date_from = $_POST['order_date_from'] ?? null;
        $order_date_to = $_POST['order_date_to'] ?? null;
        $status = $_POST['status'] ?? null;

        $records = $db->getOrderRecordsForReports($order_date_from, $order_date_to, $status);

        if ($records === false || count($records) === 0) {
            if ($loggedInUserType === 'Canteen Staff') {
                header('Location: ../Canteen Staff/norecordfound.php');
            } else {
                header('Location: ../Admin/norecordfound.php');
            }
            exit;
        }

        // Prepare filters
        $filterParts = [];
        if ($order_date_from && $order_date_to) {
            $filterParts[] = "Date from " . date('F d, Y', strtotime($order_date_from)) . " to " . date('F d, Y', strtotime($order_date_to));
        } elseif ($order_date_from) {
            $filterParts[] = "Date from " . date('F d, Y', strtotime($order_date_from));
        } elseif ($order_date_to) {
            $filterParts[] = "Date up to " . date('F d, Y', strtotime($order_date_to));
        }

        if (!empty($status)) {
            $filterParts[] = "Status: " . htmlspecialchars($status);
        }

        $filterLine = !empty($filterParts) ? "Filters: " . implode(" | ", $filterParts) : "";

        $grandTotal = 0;

        ob_start();
        ?>
        <html>

        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                }

                .header {
                    text-align: center;
                    margin-bottom: 10px;
                }

                .title {
                    font-size: 18px;
                    font-weight: bold;
                }

                .filter {
                    font-style: italic;
                    font-size: 12px;
                    margin-top: 5px;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                }

                th,
                td {
                    border: 1px solid #000;
                    padding: 5px;
                    text-align: center;
                }

                th {
                    background-color: #f0f0f0;
                }

                .bold {
                    font-weight: bold;
                }

                .right {
                    text-align: right;
                }

                .order-header {
                    background-color: #d9edf7;
                    font-weight: bold;
                }
            </style>
        </head>

        <body>
            <div class="header">
                <!-- <div style="font-size:16px; font-weight:bold;">University of Science and Technology of Southern Philippines</div>
                <div style="font-size:12px; margin-bottom:5px;">
                    Lapasan, Cagayan de Oro City, 9000, Philippines
                </div> -->
                <div class="title">Order Records Report</div>
                <?php if ($filterLine): ?>
                    <div class="filter"><?= $filterLine; ?></div>
                <?php endif; ?>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Job Order No</th>
                        <th>Office Name</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($records as $index => $record): 
                        $officeName = !empty($record['office_name'])
                            ? ucwords($record['office_name'])
                            : 'N/A';
                    ?>
                        <tr class="order-header">
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($record['job_order_no']); ?></td>
                            <td><?= htmlspecialchars($officeName, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($record['status']); ?></td>
                            <td><?= date('F d, Y H:i', strtotime($record['order_date'])); ?></td>
                            <td><?= number_format($record['total_amount'], 2); ?></td>

                        </tr>
                        <tr>
                            <td colspan="6">

                                <table width="100%" style="margin-top:5px;">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($record['items'])):
                                            foreach ($record['items'] as $item): ?>

                                                <tr>
                                                    <td><?= htmlspecialchars($item['description']); ?></td>
                                                    <td><?= number_format($item['price'], 2); ?></td>
                                                    <td><?= $item['quantity']; ?></td>
                                                    <td><?= number_format($item['subtotal'], 2); ?></td>
                                                    <td><?= htmlspecialchars($item['remarks']); ?></td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="5">No items found for this order.</td>
                                            </tr>

                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <?php $grandTotal += $record['total_amount']; ?>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="5" class="bold right">Grand Total</td>
                        <td class="bold"><?= number_format($grandTotal, 2); ?></td>
                    </tr>

                </tbody>
            </table>
        </body>

        </html>
        <?php

        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("Order_Records_Report_" . date("Ymd_His") . ".pdf", ["Attachment" => true]);
        exit;
    }

}

header('Content-Type: application/json');
echo json_encode(['success' => false]);
exit;
?>