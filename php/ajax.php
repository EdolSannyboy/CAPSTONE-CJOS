<?php

require_once 'init.php';

// if (!isset($_SESSION['user_ID'])) {
//     echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
//     exit;
// }
require_once 'db_config.php';
require_once 'classes.php';

$db = new db_class();
$conn = $db->getConnection();

$action = $_POST['action'] ?? '';

if($action === 'getOrderDetails') {
    $order_id = intval($_POST['order_id']);
    $stmt = $conn->prepare("
        SELECT 
            p.product_name, 
            p.image AS product_image, 
            oi.quantity, 
            oi.price_each, 
            (oi.quantity * oi.price_each) AS total_price
        FROM order_items oi
        INNER JOIN products p ON oi.product_ID = p.product_Id
        WHERE oi.order_ID = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $res = $stmt->get_result();

    $items = [];
    while ($row = $res->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt->close();

    echo json_encode(['success' => true, 'items' => $items]);
    exit;
}

if ($action === 'getOrders') {
    $result = $db->getOrders();
    echo json_encode($result);
    exit;
}
