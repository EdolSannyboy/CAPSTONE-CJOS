<?php
session_start();
if (!isset($_SESSION['user_ID']) || $_SESSION['user_type'] !== 'Administrator') {
    header('Location: ../login-admin.php');
    exit();
}

require_once '../php/init.php';
require_once '../php/db_config.php';
require_once '../php/classes.php';

$db = new db_class();

// Get filter parameters (same as main page)
$filter_user = $_GET['filter_user'] ?? '';
$filter_table = $_GET['filter_table'] ?? '';
$filter_date_from = $_GET['filter_date_from'] ?? '';
$filter_date_to = $_GET['filter_date_to'] ?? '';

// Build WHERE conditions (same as main page)
$where_conditions = [];
$params = [];
$types = '';

if (!empty($filter_user)) {
    $where_conditions[] = "(al.changed_by LIKE ? OR CONCAT(u.firstname, ' ', u.lastname) LIKE ?)";
    $params[] = "%{$filter_user}%";
    $params[] = "%{$filter_user}%";
    $types .= 'ss';
}

if (!empty($filter_table)) {
    $where_conditions[] = "al.table_name = ?";
    $params[] = $filter_table;
    $types .= 's';
}

if (!empty($filter_date_from)) {
    $where_conditions[] = "al.created_at >= ?";
    $params[] = $filter_date_from . ' 00:00:00';
    $types .= 's';
}

if (!empty($filter_date_to)) {
    $where_conditions[] = "al.created_at <= ?";
    $params[] = $filter_date_to . ' 23:59:59';
    $types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get all logs for export
$sql = "
    SELECT 
        al.audit_ID,
        al.batch_ID,
        al.table_name,
        al.record_ID,
        al.column_name,
        al.old_value,
        al.new_value,
        al.changed_by,
        al.user_type,
        al.action_description,
        al.created_at,
        CONCAT(u.firstname, ' ', u.lastname) as operator_name
    FROM activity_logs al
    LEFT JOIN tbluser u ON al.changed_by = u.user_id
    {$where_clause}
    ORDER BY al.created_at DESC
";

$stmt = $db->conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$logs = $stmt->get_result();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="user_activity_logs_' . date('Y-m-d') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Create output stream
$output = fopen('php://output', 'w');

// CSV headers
$headers = [
    'Audit ID',
    'Batch ID',
    'Action Description',
    'Table Name',
    'Record ID',
    'Column Name',
    'Old Value',
    'New Value',
    'Operator Name',
    'User Type',
    'Date/Time'
];
fputcsv($output, $headers);

// CSV data
while ($log = $logs->fetch_assoc()) {
    $row = [
        $log['audit_ID'],
        $log['batch_ID'],
        $log['action_description'],
        $log['table_name'],
        $log['record_ID'],
        $log['column_name'],
        $log['old_value'],
        $log['new_value'],
        $log['operator_name'] ?? 'Unknown',
        $log['user_type'],
        $log['created_at']
    ];
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
