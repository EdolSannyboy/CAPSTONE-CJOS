<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET' && basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    http_response_code(403);
    include 'access_guard.php';
    exit;
}
require_once 'init.php';
require_once 'db_config.php';
require_once 'classes.php';

header('Content-Type: application/json');

$db = new db_class();

$order_date_from = $_GET['order_date_from'] ?? null;
$order_date_to = $_GET['order_date_to'] ?? null;
$status = $_GET['status'] ?? null;

try {
    $orders = $db->getFilteredOrderRecords($order_date_from, $order_date_to, $status);
    
    $response = [
        'success' => true,
        'data' => $orders,
        'count' => count($orders)
    ];
    
    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching filtered orders: ' . $e->getMessage()
    ]);
}
?>
