
<?php
require_once '../php/db_config.php';
require_once '../php/classes.php';

$db = new db_class();

$action = $_REQUEST['action'] ?? '';


switch ($action) {
    case 'get_orders':
        getOrders();
        break;
    default:
        echo json_encode(["error" => "Invalid action."]);
        break;
}

function getOrders() {
    $db = new db_class();

    $query = "
        SELECT 
            o.order_ID, o.tracking_number, o.user_ID, o.order_status, 
            o.total_amount, o.schedule_date, o.created_at, o.updated_at
        FROM orders o
        ORDER BY o.created_at ASC
    ";

    $result = $db->getConnection()->query($query);

    if (!$result) {
        http_response_code(500);
        echo json_encode(['error' => 'Database query failed: ' . $db->getConnection()->error]);
        return;
    }

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = [
            'order_ID'          => $row['order_ID'],
            'tracking_number'   => $row['tracking_number'],
            'user_ID'           => $row['user_ID'],
            'order_status'      => $row['order_status'],
            'total_amount'      => $row['total_amount'],
            'schedule_date'     => $row['schedule_date'],
            'created_at'        => $row['created_at'],
            'updated_at'        => $row['updated_at']
        ];
    }
    echo json_encode($orders);
}