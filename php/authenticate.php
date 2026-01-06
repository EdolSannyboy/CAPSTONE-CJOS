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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $db = new db_class();
    $response = ['success' => false];

    if (!isset($_POST['action']) || $_POST['action'] !== 'login') {
        echo json_encode(['error' => 'Invalid action']);
        exit;
    }

    $user_type = $_POST['user_type'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }

    try {
        // Check user in tbluser table and auto-detect user level
        $query = "SELECT u.*, ul.userlevel_name 
                 FROM tbluser u 
                 JOIN tbluserlevel ul ON u.userlevel_id = ul.userlevel_id 
                 WHERE u.email = ? AND u.is_verified = 1";
        
        $conn = $db->getConnection();
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Determine user type and redirect based on userlevel_name
                $userLevelName = $user['userlevel_name'];
                $redirectUrl = '';
                $sessionUserType = '';
                
                if ($userLevelName === 'Admin') {
                    $redirectUrl = 'Admin/index.php';
                    $sessionUserType = 'Administrator';
                } elseif ($userLevelName === 'Canteen Staff') {
                    $redirectUrl = 'Canteen Staff/index.php';
                    $sessionUserType = 'Canteen Staff';
                } elseif ($userLevelName === 'Canteen Manager') {
                    $redirectUrl = 'Canteen Manager/index.php';
                    $sessionUserType = 'Canteen Manager';
                } else {
                    $response['message'] = 'Unauthorized user level';
                    echo json_encode($response);
                    exit;
                }
                
                // Set session variables
                $_SESSION['user_ID'] = $user['user_id'];
                $_SESSION['user_type'] = $sessionUserType;
                $_SESSION['user_level'] = $user['userlevel_id'];
                $_SESSION['user_level_name'] = $user['userlevel_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['fullname'] = $user['firstname'] . ' ' . $user['lastname'];
                $_SESSION['login_time'] = date('Y-m-d H:i:s');
                
                // Generate and store UIN for this session
                $_SESSION['uin'] = date('YmdHis') . substr(md5(uniqid(mt_rand(), true)), 0, 8);
                
                // Insert login record into log_history
                try {
                    $insertLog = $conn->prepare("INSERT INTO log_history (user_ID, uin, usertype, login_datetime) VALUES (?, ?, NULL, NOW())");
                    $insertLog->bind_param("is", $user['user_id'], $_SESSION['uin']);
                    if (!$insertLog->execute()) {
                        error_log("Failed to insert login record: " . $insertLog->error);
                        // Continue with login even if log insertion fails
                    }
                    $insertLog->close();
                } catch (Exception $e) {
                    error_log("Exception in login record insertion: " . $e->getMessage());
                    // Continue with login even if log insertion fails
                }
                
                // Log login attempt - commented out as method doesn't exist
                // $db->logLoginAttempt($user['user_id'], $sessionUserType);
                
                $response['success'] = true;
                $response['message'] = "Login successful! Redirecting to {$userLevelName} dashboard...";
                $response['redirect_url'] = $redirectUrl;
            } else {
                $response['message'] = 'Invalid email or password';
            }
        } else {
            // Check if user exists but not verified
            $checkQuery = "SELECT user_id FROM tbluser WHERE email = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("s", $email);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows === 1) {
                $response['message'] = 'Account not verified. Please check your email for verification.';
            } else {
                $response['message'] = 'Invalid email or password';
            }
        }

    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $response['message'] = 'An error occurred during login. Please try again.';
    }

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
