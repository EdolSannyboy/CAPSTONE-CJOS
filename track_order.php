<?php
require_once 'php/db_config.php';
require_once 'php/classes.php';

$db = new db_class();
$conn = $db->getConnection();

$orderDetails = null;
$orderItems = [];
$statusHistory = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobOrderNo = trim($_POST['job_order_no'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($jobOrderNo === '' || $email === '') {
        $error = 'Please enter both Job Order No. and Office Email.';
    } else {
        try {
            $sql = "
                SELECT o.*, 
                       COALESCE(o.office_name, ofc.office_name) AS office_name_display,
                       COALESCE(o.email, ofc.office_email) AS office_email_display
                FROM orders o
                LEFT JOIN tbloffice ofc ON o.office_id = ofc.office_id
                WHERE o.job_order_no = ?
                  AND (
                        (o.email IS NOT NULL AND o.email = ?)
                     OR (o.email IS NULL AND ofc.office_email = ?)
                  )
                LIMIT 1
            ";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('sss', $jobOrderNo, $email, $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result && $row = $result->fetch_assoc()) {
                    $orderDetails = $row;
                    $orderId = (int)$row['order_id'];

                    // Use existing helper methods for items and status history
                    $orderItems = $db->getOrderItems($orderId) ?: [];
                    $statusHistory = $db->getOrderStatusHistory($orderId) ?: [];
                } else {
                    $error = 'No order found for the provided Job Order No. and Email.';
                }
                $stmt->close();
            } else {
                $error = 'Unable to process your request at the moment.';
            }
        } catch (Exception $e) {
            $error = 'An unexpected error occurred while retrieving the order.';
        }
    }
}

function formatStatusName($statusId) {
    switch ((int)$statusId) {
        case 1: return 'On-going';
        case 2: return 'Cancelled';
        case 3: return 'Completed';
        default: return 'Unknown';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Job Order Status</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Track Job Order Status</h5>
                </div>
                <div class="card-body">
                    <form method="post" autocomplete="off">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="job_order_no">Job Order No.</label>
                                <input type="text" class="form-control" id="job_order_no" name="job_order_no" value="<?= htmlspecialchars($_POST['job_order_no'] ?? '') ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email">Office Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                        </div>
                        <?php if ($error): ?>
                            <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                </div>
            </div>

            <?php if ($orderDetails): ?>
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $datetime_string = $orderDetails['needed_datetime'] ?? '';
                        $slots = $datetime_string !== '' ? explode(' | ', $datetime_string) : [];
                        $formattedSlots = [];
                        foreach ($slots as $slot) {
                            $ts = strtotime($slot);
                            if ($ts !== false) {
                                $formattedSlots[] = date('F d, Y h:i A', $ts);
                            }
                        }

                        // Derive current status from status history if available
                        $currentStatusName = 'On-going';
                        if (!empty($statusHistory)) {
                            $last = end($statusHistory);
                            $currentStatusName = $last['status_name'] ?? formatStatusName($last['status_id'] ?? 0);
                        }
                        ?>
                        <p><strong>Job Order No:</strong> <?= htmlspecialchars($orderDetails['job_order_no']) ?></p>
                        <p><strong>Office:</strong> <?= htmlspecialchars($orderDetails['office_name_display'] ?? 'N/A') ?></p>
                        <p><strong>Office Email:</strong> <?= htmlspecialchars($orderDetails['office_email_display'] ?? 'N/A') ?></p>
                        <p><strong>Needed Date/Time:</strong><br>
                            <?php if ($formattedSlots): ?>
                                <?php foreach ($formattedSlots as $fs): ?>
                                    <?= htmlspecialchars($fs) ?><br>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </p>
                        <p><strong>Total Amount:</strong>
                            <?php if (!empty($orderDetails['total_amount']) && $orderDetails['total_amount'] > 0): ?>
                                ₱<?= number_format($orderDetails['total_amount'], 2) ?>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </p>
                        <p><strong>Current Status:</strong>
                            <?php
                            $badgeClass = 'badge-secondary';
                            if ($currentStatusName === 'On-going') $badgeClass = 'badge-warning';
                            elseif ($currentStatusName === 'Completed') $badgeClass = 'badge-success';
                            elseif ($currentStatusName === 'Cancelled') $badgeClass = 'badge-danger';
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($currentStatusName) ?></span>
                        </p>
                        
                        <!-- Simple Reminder Button -->
                        <div class="mt-3">
                            <button type="button" class="btn btn-danger" onclick="sendReminder(<?= $orderDetails['order_id'] ?>, '<?= htmlspecialchars($orderDetails['job_order_no']) ?>', '<?= htmlspecialchars($orderDetails['office_email_display']) ?>')">
                                <i class="fas fa-bell"></i> Send Reminder to Canteen
                            </button>
                            <small class="text-muted ml-2">Click to alert canteen staff about your order</small>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Order Items</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-right">Unit Price</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($orderItems)): ?>
                                    <?php foreach ($orderItems as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['description'] ?? '') ?></td>
                                            <td class="text-center"><?= htmlspecialchars($item['quantity'] ?? '') ?></td>
                                            <td class="text-right">
                                                <?php if (!empty($item['price']) && $item['price'] > 0): ?>
                                                    ₱<?= number_format($item['price'], 2) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-right">
                                                <?php if (!empty($item['total']) && $item['total'] > 0): ?>
                                                    ₱<?= number_format($item['total'], 2) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No items found for this order.</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0">Status History</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Status</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($statusHistory)): ?>
                                    <?php foreach ($statusHistory as $entry): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($entry['status_name'] ?? formatStatusName($entry['status_id'] ?? 0)) ?></td>
                                            <td><?= htmlspecialchars($entry['updated_at'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No status history available.</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script>
function sendReminder(orderId, jobOrderNo, officeEmail) {
    if (confirm('Send a reminder to canteen staff about your order ' + jobOrderNo + '?')) {
        // Show loading state
        event.target.disabled = true;
        event.target.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        
        // Send reminder via AJAX
        fetch('send_reminder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'order_id=' + orderId + '&job_order_no=' + encodeURIComponent(jobOrderNo) + '&office_email=' + encodeURIComponent(officeEmail)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reminder sent successfully! Canteen staff has been notified.');
                // Update button state
                event.target.innerHTML = '<i class="fas fa-check"></i> Reminder Sent';
                event.target.classList.remove('btn-danger');
                event.target.classList.add('btn-success');
            } else {
                alert('Error: ' + data.message);
                // Reset button
                event.target.disabled = false;
                event.target.innerHTML = '<i class="fas fa-bell"></i> Send Reminder to Canteen';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send reminder. Please try again.');
            // Reset button
            event.target.disabled = false;
            event.target.innerHTML = '<i class="fas fa-bell"></i> Send Reminder to Canteen';
        });
    }
}
</script>
</body>
</html>
