<?php
    require_once '../php/db_config.php';
    require_once '../php/classes.php';

    if (!isset($_SESSION['user_ID'])) {
        header("Location: index.php");
        exit();
    }

    $db = new db_class();
    $operator_ID = $_SESSION['user_ID'] ?? null;
    $loggedInUserType = $_SESSION['user_type'] ?? null;

    if (!$operator_ID) {
        header("Location: index.php");
        exit();
    }

    $users = $db->get_Administrator_By_ID($operator_ID);
    $row2 = null;
    if ($users && is_object($users) && method_exists($users, 'fetch_assoc')) {
        $row2 = $users->fetch_assoc();
    }

    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    switch ($action) {
        case 'get_all_users':
            getUserCounts();
            break;
        case 'get_order_counts':
            getOrderCounts();
            break;
        default:
            echo json_encode(["error" => "Invalid action."]);
            break;
    }

    function getUserCounts(){
        $db = new db_class();
        $conn = $db->getConnection();

        // Initialize counts
        $userGenderCounts = [
            'Administrators' => ['Male' => 0, 'Female' => 0],
            'Canteen Staff' => ['Male' => 0, 'Female' => 0],
            'Canteen Manager' => ['Male' => 0, 'Female' => 0]
        ];

        // Count users from tbluser table by userlevel_id
        $query = "SELECT userlevel_id, gender, COUNT(*) AS count FROM tbluser GROUP BY userlevel_id, gender";
        $result = $conn->query($query);
        
        while ($row = $result->fetch_assoc()) {
            $gender = $row['gender'];
            $userlevel_id = (int) $row['userlevel_id'];
            $count = (int) $row['count'];
            
            switch ($userlevel_id) {
                case 1: // Administrator
                    $userGenderCounts['Administrators'][$gender] = $count;
                    break;
                case 2: // Canteen Staff
                    $userGenderCounts['Canteen Staff'][$gender] = $count;
                    break;
                case 3: // Canteen Manager
                    $userGenderCounts['Canteen Manager'][$gender] = $count;
                    break;
            }
        }

        // Calculate totals
        $totalUsers = 0;
        $totalGenderCount = ['Male' => 0, 'Female' => 0];
        foreach ($userGenderCounts as $type => $genders) {
            $totalUsers += array_sum($genders);
            $totalGenderCount['Male'] += $genders['Male'];
            $totalGenderCount['Female'] += $genders['Female'];
        }

        $userCounts = [
            'Administrators' => $userGenderCounts['Administrators'],
            'CanteenStaff' => $userGenderCounts['Canteen Staff'],
            'CanteenManager' => $userGenderCounts['Canteen Manager'],
            'TotalUsers' => $totalUsers,
            'TotalGender' => $totalGenderCount
        ];

        header('Content-Type: application/json');
        echo json_encode($userCounts);
    }

    function getOrderCounts() {
        $db = new db_class();
        $conn = $db->getConnection();

        $orderCounts = [
            'ongoing_count' => 0,
            'completed_count' => 0,
            'cancelled_count' => 0,
            'ongoing_total' => 0,
            'completed_total' => 0,
            'cancelled_total' => 0,
            'low_stock_count' => 0,
            'out_of_stock_count' => 0
        ];

        $query = "
            SELECT 
                ts.status_name AS status,
                COUNT(DISTINCT o.order_id) AS total_count, 
                COALESCE(SUM(o.total_amount), 0) AS total_amount
            FROM orders o
            LEFT JOIN (
                SELECT os1.*
                FROM order_status os1
                INNER JOIN (
                    SELECT order_id, MAX(updated_at) AS max_updated
                    FROM order_status
                    GROUP BY order_id
                ) latest ON latest.order_id = os1.order_id AND latest.max_updated = os1.updated_at
            ) os ON os.order_id = o.order_id
            LEFT JOIN tblstatus ts ON ts.status_id = os.status_id
            WHERE ts.status_name IN ('On-going', 'Completed', 'Cancelled')
            GROUP BY ts.status_name
        ";

        $result = $conn->query($query);

        while ($row = $result->fetch_assoc()) {
            $status_prefix = strtolower($row['status']);

            if ($status_prefix === 'on-going') {
                $orderCounts['ongoing_count'] = (int) $row['total_count'];
                $orderCounts['ongoing_total'] = (float) $row['total_amount'];
            } elseif ($status_prefix === 'completed') {
                $orderCounts['completed_count'] = (int) $row['total_count'];
                $orderCounts['completed_total'] = (float) $row['total_amount'];
            } elseif ($status_prefix === 'cancelled') {
                $orderCounts['cancelled_count'] = (int) $row['total_count'];
                $orderCounts['cancelled_total'] = (float) $row['total_amount'];
            }
        }

        // Compute low stock and out of stock item counts for dashboards
        $itemQuery = "
            SELECT 
                SUM(CASE WHEN stock_qty > 0 AND stock_qty <= low_stock_threshold THEN 1 ELSE 0 END) AS low_stock_count,
                SUM(CASE WHEN stock_qty <= 0 THEN 1 ELSE 0 END) AS out_of_stock_count
            FROM tblitem
        ";

        if ($itemResult = $conn->query($itemQuery)) {
            if ($itemRow = $itemResult->fetch_assoc()) {
                $orderCounts['low_stock_count'] = (int) ($itemRow['low_stock_count'] ?? 0);
                $orderCounts['out_of_stock_count'] = (int) ($itemRow['out_of_stock_count'] ?? 0);
            }
        }

        header('Content-Type: application/json');
        echo json_encode($orderCounts);
    }
