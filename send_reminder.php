<?php
require_once 'php/db_config.php';
require_once 'php/classes.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)$_POST['order_id'];
    $jobOrderNo = trim($_POST['job_order_no']);
    $officeEmail = trim($_POST['office_email']);
    
    $db = new db_class();
    $conn = $db->getConnection();
    
    try {
        // Check if reminder already sent for this order
        $checkSql = "SELECT reminder_id FROM order_reminders 
                    WHERE order_id = ? LIMIT 1";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param('i', $orderId);
        $checkStmt->execute();
        $existingResult = $checkStmt->get_result();
        
        if ($existingResult->num_rows > 0) {
            echo json_encode([
                'success' => true,
                'alreadySent' => true,
                'message' => 'Reminder already sent for this order.'
            ]);
            exit;
        }
        
        // Insert reminder
        $sql = "INSERT INTO order_reminders (order_id, job_order_no, office_email, message) 
                VALUES (?, ?, ?, 'Please follow up on this order - Customer reminder sent via tracking page')";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iss', $orderId, $jobOrderNo, $officeEmail);
        
        if ($stmt->execute()) {
            // Optional: Send email notification to canteen staff
            $reminderId = $conn->insert_id;
            sendEmailNotification($jobOrderNo, $officeEmail, $reminderId);
            
            echo json_encode(['success' => true, 'message' => 'Reminder sent successfully']);
        } else {
            throw new Exception("Failed to save reminder");
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

function sendEmailNotification($jobOrderNo, $officeEmail, $reminderId) {
    // Optional: Send email to canteen staff
    $to = "canteen@yourcompany.com"; // Replace with actual canteen email
    $subject = "Order Reminder: " . $jobOrderNo;
    $message = "A customer has sent a reminder for order: " . $jobOrderNo . "\n";
    $message .= "Customer Email: " . $officeEmail . "\n";
    $message .= "Reminder ID: " . $reminderId . "\n";
    $message .= "Please check your admin dashboard for details.";
    
    $headers = "From: noreply@yourcompany.com\r\n";
    $headers .= "Reply-To: " . $officeEmail . "\r\n";
    
    // Uncomment to actually send email
    // mail($to, $subject, $message, $headers);
}
?>
