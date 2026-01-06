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
$page_title = "User Activity Logs";

// Get filter parameters
$filter_user = $_GET['filter_user'] ?? '';
$filter_table = $_GET['filter_table'] ?? '';
$filter_date_from = $_GET['filter_date_from'] ?? '';
$filter_date_to = $_GET['filter_date_to'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Build WHERE conditions
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

// Get total count
$count_sql = "
    SELECT COUNT(*) as total
    FROM activity_logs al
    LEFT JOIN tbluser u ON al.changed_by = u.user_id
    {$where_clause}
";
$count_stmt = $db->getConnection()->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_result = $count_stmt->get_result();
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $per_page);

// Get logs with pagination
$sql = "
    SELECT 
        al.audit_ID,
        al.batch_ID,
        al.table_name,
        al.record_ID,
        al.column_name,
        CASE 
            WHEN al.table_name = 'order_status' AND al.column_name = 'status_id' THEN 
                (SELECT s.status_name FROM tblstatus s WHERE s.status_id = al.new_value)
            WHEN al.table_name = 'order_status' AND al.column_name = 'status_id' AND al.old_value IS NOT NULL THEN 
                (SELECT s.status_name FROM tblstatus s WHERE s.status_id = al.old_value)
            ELSE al.old_value
        END as old_value_display,
        CASE 
            WHEN al.table_name = 'order_status' AND al.column_name = 'status_id' THEN 
                (SELECT s.status_name FROM tblstatus s WHERE s.status_id = al.new_value)
            ELSE al.new_value
        END as new_value_display,
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
    LIMIT ? OFFSET ?
";
$params[] = $per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = $db->getConnection()->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$logs = $stmt->get_result();

// Get distinct tables for filter dropdown
$tables_stmt = $db->getConnection()->prepare("SELECT DISTINCT table_name FROM activity_logs ORDER BY table_name");
$tables_stmt->execute();
$tables = $tables_stmt->get_result();
?>

<?php require_once 'sidebar.php'; ?>
<title><?= $system_info['system_name'] ?> | User Activity Logs</title>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>User Activity Logs</h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="x_panel">
      <div class="x_title">
        <h2>Activity Logs</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">

                
                <!-- Logs Display -->
        <div class="logs-container">
          <?php if ($logs->num_rows > 0): ?>
            <?php 
            $current_batch = null;
            while ($log = $logs->fetch_assoc()): 
              $entry_class = '';
              if (strpos($log['action_description'], 'Created') !== false) {
                $entry_class = 'created';
              } elseif (strpos($log['action_description'], 'Updated') !== false) {
                $entry_class = 'updated';
              } elseif (strpos($log['action_description'], 'Deleted') !== false) {
                $entry_class = 'deleted';
              }
              
              // Show batch header if this is a new batch
              if ($current_batch !== $log['batch_ID']) {
                if ($current_batch !== null) {
                  echo '</div>'; // Close previous batch
                }
                $current_batch = $log['batch_ID'];
                echo '<div class="card mb-3 log-entry-' . $entry_class . '">';
              }
            ?>
              <div class="card-body">
                <div class="row">
                  <div class="col-12">
                    <h6 class="card-title mb-2">
                      <strong><?php echo htmlspecialchars($log['action_description']); ?></strong>
                    </h6>
                    <div class="text-muted small">
                      <div class="row">
                        <div class="col-lg-6 col-md-12">
                          <i class="fas fa-user"></i> <strong>By:</strong> <?php echo htmlspecialchars($log['operator_name'] ?? 'Unknown'); ?> 
                          <span class="badge bg-info ms-1"><?php echo htmlspecialchars($log['user_type']); ?></span>
                        </div>
                        <div class="col-lg-6 col-md-12">
                          <i class="fas fa-table"></i> <strong>Table:</strong> <?php echo htmlspecialchars($log['table_name']); ?>
                        </div>
                        <div class="col-lg-6 col-md-12">
                          <i class="fas fa-hashtag"></i> <strong>ID:</strong> <?php echo htmlspecialchars($log['record_ID']); ?>
                        </div>
                        <div class="col-lg-6 col-md-12">
                          <i class="fas fa-clock"></i> <?php echo date('M j, Y g:i A', strtotime($log['created_at'])); ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <?php if (!empty($log['column_name']) && $log['column_name'] !== 'action_description'): ?>
                  <div class="mt-3">
                    <div class="change-detail p-2 bg-light rounded">
                      <strong><?php echo htmlspecialchars($log['column_name']); ?>:</strong>
                      <?php if ($log['old_value'] !== '' && $log['old_value'] !== null): ?>
                        <span class="old-value text-danger text-decoration-line-through"><?php echo htmlspecialchars($log['old_value_display']); ?></span>
                      <?php endif; ?>
                      <i class="fas fa-arrow-right mx-1"></i>
                      <span class="new-value text-success fw-bold"><?php echo htmlspecialchars($log['new_value_display']); ?></span>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
            <?php endwhile; ?>
            <?php if ($current_batch !== null): ?>
              </div> <!-- Close last batch -->
            <?php endif; ?>
          <?php else: ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i> No activity logs found matching your criteria.
            </div>
          <?php endif; ?>
        </div>

                <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
          <nav aria-label="Activity logs pagination">
            <ul class="pagination justify-content-center">
              <?php if ($page > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                    <i class="fas fa-chevron-left"></i> Previous
                  </a>
                </li>
              <?php endif; ?>
              
              <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                  <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                </li>
              <?php endfor; ?>
              
              <?php if ($page < $total_pages): ?>
                <li class="page-item">
                  <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                    Next <i class="fas fa-chevron-right"></i>
                  </a>
                </li>
              <?php endif; ?>
            </ul>
          </nav>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>

<style>
.log-entry-created {
  border-left: 4px solid #28a745 !important;
}
.log-entry-updated {
  border-left: 4px solid #ffc107 !important;
}
.log-entry-deleted {
  border-left: 4px solid #dc3545 !important;
}
.change-detail {
  font-family: 'Courier New', monospace;
  font-size: 0.9em;
}
@media (max-width: 768px) {
  .card-title {
    font-size: 0.9rem !important;
  }
  .text-muted.small {
    font-size: 0.75rem !important;
  }
  .btn-group .btn {
    font-size: 0.8rem;
  }
}
</style>

<script src="../assets/js/jquery/jquery.min.js"></script>
<script>
  // Auto-refresh every 5 minutes
  setInterval(() => {
    window.location.reload();
  }, 300000);
</script>
