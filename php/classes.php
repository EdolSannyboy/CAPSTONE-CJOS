<?php
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
        http_response_code(403);
        include 'access_guard.php';
        exit;
    }

    require_once 'db_config.php'; 

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if (!class_exists('PHPMailer\PHPMailer\Exception')) { require __DIR__ . '/../PHPMailer/src/Exception.php'; }
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) { require __DIR__ . '/../PHPMailer/src/PHPMailer.php'; }
    if (!class_exists('PHPMailer\PHPMailer\SMTP')) { require __DIR__ . '/../PHPMailer/src/SMTP.php'; }

    class db_class extends db_connect {
        
        public function __construct() {
            $this->connect();
        }

        /**
         * Generate a sequential job order number (simple number format)
         * Requirement: numbers must start with 11xxx (e.g. 11001, 11002, ...).
         */
        private function generateJobOrderNumber() {
            // Get the current maximum numeric job order
            $seqStmt = $this->conn->prepare("
                SELECT MAX(CAST(job_order_no AS UNSIGNED)) AS max_seq
                FROM orders
                WHERE job_order_no REGEXP '^[0-9]+$'
            ");
            if (!$seqStmt) {
                throw new Exception('Failed to prepare sequence query: ' . $this->conn->error);
            }
            $seqStmt->execute();
            $seqResult = $seqStmt->get_result();
            $seqRow = $seqResult ? $seqResult->fetch_assoc() : null;
            $maxSeq = ($seqRow && $seqRow['max_seq'] !== null) ? (int)$seqRow['max_seq'] : 0;
            $seqStmt->close();

            // Ensure we start from at least 11000 so that the first issued number is 11001
            $base = 11000;
            if ($maxSeq < $base) {
                $maxSeq = $base;
            }

            return (string)($maxSeq + 1);
        }

        /**
         * Generate job reference number when order is completed
         * Format: "Admin JO#YYYY-MM-NNN" or "Canteen Staff JO#YYYY-MM-NNN" or "Canteen Manager JO#YYYY-MM-NNN"
         */
        private function generateJobReferenceNumber($userType) {
            $timezone = new DateTimeZone('Asia/Manila');
            $now = new DateTime('now', $timezone);
            $year = $now->format('Y');
            $month = $now->format('m');
            
            // Get the next sequence for this month and user type
            $seqStmt = $this->conn->prepare("
                SELECT MAX(CAST(SUBSTRING(job_reference_no, -3) AS UNSIGNED)) AS max_seq
                FROM orders
                WHERE job_reference_no REGEXP ?
            ");
            if (!$seqStmt) {
                throw new Exception('Failed to prepare job reference sequence query: ' . $this->conn->error);
            }
            
            $pattern = '^' . preg_quote($userType) . ' JO#' . $year . '-' . $month . '-[0-9]{3}$';
            $seqStmt->bind_param('s', $pattern);
            $seqStmt->execute();
            $seqResult = $seqStmt->get_result();
            $seqRow = $seqResult ? $seqResult->fetch_assoc() : null;
            $maxSeq = ($seqRow && $seqRow['max_seq'] !== null) ? (int)$seqRow['max_seq'] : 0;
            $seqStmt->close();

            $seq = $maxSeq + 1;
            $seqPadded = str_pad($seq, 3, '0', STR_PAD_LEFT);
            
            return sprintf('%s JO#%s-%s-%s', $userType, $year, $month, $seqPadded);
        }

        public function getConnection() {
            return $this->conn;
        }
        public function checkExistingContact($user_type, $contact, $user_ID = "") {
            $response = ['contactExists' => false];

            try {
                // Map user types to userlevel_id values
                $userLevels = [
                    "Administrator" => 1,
                    "Canteen Staff" => 2,
                    "Canteen Manager" => 3
                ];

                if (!isset($userLevels[$user_type])) {
                    return $response;
                }

                $userlevel_id = $userLevels[$user_type];

                if (!empty($user_ID)) {
                    // Exclude the current record
                    $query = $this->conn->prepare("SELECT 1 FROM tbluser WHERE contact = ? AND userlevel_id = ? AND user_id != ?");
                    $query->bind_param("sii", $contact, $userlevel_id, $user_ID);
                } else {
                    // Check for existing contact
                    $query = $this->conn->prepare("SELECT 1 FROM tbluser WHERE contact = ? AND userlevel_id = ?");
                    $query->bind_param("si", $contact, $userlevel_id);
                }

                if (!$query) throw new Exception("Query failed for tbluser");

                $query->execute();
                $result = $query->get_result();

                if ($result && $result->num_rows > 0) {
                    $response['contactExists'] = true;
                }
                $query->close();
            } catch (Exception $e) {
                error_log("Error in checkExistingContact: " . $e->getMessage(), 3, __DIR__ . '/logs/php_errors.log');
            }

            return $response;
        }
        public function checkExistingEmail($user_type, $email, $user_ID = "") {
            $response = ['emailExists' => false];

            try {
                // Map user types to userlevel_id values
                $userLevels = [
                    "Administrator" => 1,
                    "Canteen Staff" => 2,
                    "Canteen Manager" => 3
                ];

                if (!isset($userLevels[$user_type])) {
                    return $response;
                }

                $userlevel_id = $userLevels[$user_type];

                if (!empty($user_ID)) {
                    // Exclude the current record
                    $query = $this->conn->prepare("SELECT 1 FROM tbluser WHERE email = ? AND userlevel_id = ? AND user_id != ?");
                    $query->bind_param("sii", $email, $userlevel_id, $user_ID);
                } else {
                    // Check for existing email
                    $query = $this->conn->prepare("SELECT 1 FROM tbluser WHERE email = ? AND userlevel_id = ?");
                    $query->bind_param("si", $email, $userlevel_id);
                }

                if (!$query) throw new Exception("Query failed for tbluser");

                $query->execute();
                $result = $query->get_result();

                if ($result && $result->num_rows > 0) {
                    $response['emailExists'] = true;
                }
                $query->close();
            } catch (Exception $e) {
                error_log("Error in checkExistingEmail: " . $e->getMessage(), 3, __DIR__ . '/logs/php_errors.log');
            }

            return $response;
        }
        // HANDLE CHECK EXISTING EMAIL ACTION FOR FORGOT PASSWORD - FORGOTPASSWORD.PHP
        public function checkEmail($email, $type) {
            $response = ['exists' => false];

            // Map user types to userlevel_id values
            $userLevels = [
                "Administrator" => 1,
                "Canteen Staff" => 2,
                "Canteen Manager" => 3
            ];

            if (!isset($userLevels[$type])) {
                return $response;
            }

            $userlevel_id = $userLevels[$type];

            // Use tbluser table for all user types
            $query = $this->conn->prepare("SELECT user_id FROM tbluser WHERE email = ? AND userlevel_id = ?");
            $query->bind_param("si", $email, $userlevel_id);
            $query->execute();
            $result = $query->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $response['exists'] = true;
                $response['id'] = $row['user_id'];
                $response['user_type'] = $type;
            }

            $query->close();
            return $response;
        }

        // GET USER BY TYPE - SENDCODE.PHP
        public function getUserByType($user_ID, $type, $email) {
            // Use tbluser table for all user types
            $query = $this->conn->prepare("SELECT * FROM tbluser WHERE email = ? AND user_id = ?");
            $query->bind_param("si", $email, $user_ID);

            if ($query->execute()) {
                return $query->get_result();
            }
            return false;
        }
        public function sendCode($email, $id, $type) {

            $key = substr(number_format(time() * rand(), 0, '', ''), 0, 6); 
            $response = ['success' => false, 'message' => 'User not found'];

            // Use tbluser table for all user types
            $query = $this->conn->prepare("SELECT firstname, lastname FROM tbluser WHERE email = ? AND user_id = ?");
            $query->bind_param("si", $email, $id);
            $query->execute();
            $result = $query->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $firstname = $user['firstname'];
                $lastname = $user['lastname'];

                $updateQuery = $this->conn->prepare("UPDATE tbluser SET verification_code = ? WHERE email = ? AND user_id = ?");
                $updateQuery->bind_param("isi", $key, $email, $id);

                if ($updateQuery->execute()) {
                    $bodyContent = "
                        <p>Dear " . htmlspecialchars($firstname) . " " . htmlspecialchars($lastname) . ",</p>
                        <p>Your verification code is: <b>" . $key . "</b>. Please keep this code confidential and do not share it with others.</p>
                        <p>To change your password, click <a href='http://localhost/Canteen%20Job%20Order%20System/change-password.php?email=" . urlencode($email) . "&id=" . $id . "&type=" . urlencode($type) . "&action=ChangePass'>here</a>.</p>";

                    $this->sendEmail('Verification Code', $bodyContent, $email);

                    $updateQuery->close();
                    $query->close();
                    $response = ['success' => true, 'message' => 'Verification code sent successfully'];
                }
            }

            return $response;
        }
        // HANDLE VERIFICATION CODE - VERIFYCODE.PHP
        public function verifyCode($email, $id, $code, $type) {
            $fetch_user = $this->verifyEmail_by_code($email, $id, $code, $type);

            if ($fetch_user && $fetch_user instanceof mysqli_result && $fetch_user->num_rows > 0) {
                // Use tbluser table for all user types
                $query = $this->conn->prepare("UPDATE tbluser SET verification_code = NULL WHERE email = ? AND user_id = ?");
                $query->bind_param("si", $email, $id);

                if ($query->execute()) {
                    $query->close();
                    return true;
                }
            }
            return false;
        }

        // VERIFY EMAIL BY VERIFICATION CODE - VERIFYCODE.PHP
        public function verifyEmail_by_code($email, $id, $code, $type) {

            // Use tbluser table for all user types
            $query = $this->conn->prepare("SELECT * FROM tbluser WHERE email = ? AND user_id = ? AND verification_code = ? LIMIT 1");
            $query->bind_param("sii", $email, $id, $code);
            
            if ($query->execute()) {
                return $query->get_result();
            }
            return false;
        }
        // HANDLE VERIFICATION OF ACCOUNT ACTION FROM EMAIL - ACCOUNT-VERIFICATION.PHP
        public function verifyUserAccount($user_ID, $email, $type) {
            // Update tbluser table to set is_verified = 1
            $sql = "UPDATE tbluser SET is_verified = 1 WHERE user_id = ? AND email = ?";
            
            $query = $this->conn->prepare($sql);
            if (!$query) {
                return false;
            }
            
            $query->bind_param("is", $user_ID, $email);

            if ($query->execute()) {
                $query->close();
                return true;
            }
            $query->close();
            return false;
        }
        // HANDLE CHANGEPASSWORD ACTION - CHANGEPASSWORD.PHP
        public function changePassword($email, $id, $type, $action_type, $password, $cpassword) { 

            if ($password !== $cpassword) {
                return "Password does not match";
            }

            $hashedPassword = password_hash($cpassword, PASSWORD_DEFAULT);

            // Use tbluser table for all user types
            if ($action_type == "FirstChangePass") {
                $sql = "UPDATE tbluser SET password = ?, is_password_changed = 1 WHERE email = ? AND user_id = ?";
            } else {
                $sql = "UPDATE tbluser SET password = ? WHERE email = ? AND user_id = ?";
            }

            $query = $this->conn->prepare($sql);

            if (!$query) {
                return "Failed to prepare statement: " . $this->conn->error;
            }

            $query->bind_param("ssi", $hashedPassword, $email, $id);

            if ($query->execute()) {
                $query->close();
                return true;
            } else {
                return "Query execution failed: " . $query->error;
            }

        }
        // RECORD FAILED LOGIN ATTEMPTS
        private function record_failed_attempt($email) {
            $check_query = $this->conn->prepare("SELECT * FROM login_attempts WHERE email = ?");
            $check_query->bind_param("s", $email);
            $check_query->execute();
            $check_result = $check_query->get_result();

            if ($check_result->num_rows > 0) {
                $update_query = $this->conn->prepare("UPDATE login_attempts SET attempts = attempts + 1, last_attempt = NOW() WHERE email = ?");
                $update_query->bind_param("s", $email);
                $update_query->execute();
                $update_query->close();
            } else {
                $insert_query = $this->conn->prepare("INSERT INTO login_attempts (email, attempts, last_attempt) VALUES (?, 1, NOW())");
                $insert_query->bind_param("s", $email);
                $insert_query->execute();
                $insert_query->close();
            }
        }
        // HANDLE LOGOUT ACTION - INCLUDES/HEADER.PHP
        public function logout_user($user_ID, $login_time, $usertype, $uin = null) {
            if ($uin) {
                // Use UIN for precise logout tracking
                $update_query = $this->conn->prepare("UPDATE log_history SET logout_datetime = NOW(), usertype = NULL WHERE uin = ? AND user_ID = ?");
                $update_query->bind_param("si", $uin, $user_ID);
            } else {
                // Fallback to original method if UIN not provided
                $update_query = $this->conn->prepare("UPDATE log_history SET logout_datetime = NOW(), usertype = NULL WHERE user_ID = ? AND login_datetime = ?");
                $update_query->bind_param("is", $user_ID, $login_time);
            }

            if ($update_query->execute()) {
                $update_query->close();
                return true;
            } else {
                $error = $update_query->error;
                $update_query->close();
                return "Error updating logout: " . $error;
            }
        }
        
        public function login($usertype, $email, $password) {
            try {
                $checkAttempts = $this->conn->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE email = ?");
                $checkAttempts->bind_param("s", $email);
                $checkAttempts->execute();
                $attemptResult = $checkAttempts->get_result();
                $attemptData = $attemptResult->fetch_assoc();
                $checkAttempts->close();

                $currentTime = time();
                if ($attemptData) {
                    $attempts = (int)$attemptData['attempts'];
                    $lastAttempt = strtotime($attemptData['last_attempt']);
                    if ($attempts >= 3 && ($currentTime - $lastAttempt) < 120) {
                        return "Too many failed login attempts. Please try again after 2 minutes.";
                    }
                }

                // Use tbluser table for authentication
                $stmt = $this->conn->prepare("
                    SELECT u.*, ul.userlevel_name 
                    FROM tbluser u 
                    JOIN tbluserlevel ul ON u.userlevel_id = ul.userlevel_id 
                    WHERE u.email = ? AND u.is_verified = 1
                ");
                
                if (!$stmt) {
                    error_log("Prepare failed: " . $this->conn->error);
                    return null;
                }
                
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    $userTypeFound = $user['userlevel_name']; // Get user type from userlevel_name
                    $idField = 'user_id';
                    $stmt->close();
                } else {
                    $stmt->close();
                }

                if (!$user) {
                    $this->record_failed_attempt($email);
                    return null;
                }

                if (!password_verify($password, $user['password'])) {
                    $this->record_failed_attempt($email);
                    return null;
                }

                $reset = $this->conn->prepare("DELETE FROM login_attempts WHERE email = ?");
                $reset->bind_param("s", $email);
                $reset->execute();
                $reset->close();

                $userID = $user[$idField];
                $ongoing = $this->conn->prepare(
                    "SELECT log_Id FROM log_history 
                    WHERE user_ID = ? AND logout_datetime IS NULL AND usertype IS NULL
                    ORDER BY log_Id DESC LIMIT 1"
                );
                $ongoing->bind_param("i", $userID);
                $ongoing->execute();
                $ongoingResult = $ongoing->get_result();
                $session = $ongoingResult->fetch_assoc();
                $ongoing->close();

                if ($session) {
                    $updateLog = $this->conn->prepare("UPDATE log_history SET logout_remarks = 1 WHERE log_Id = ? AND usertype IS NULL");
                    $updateLog->bind_param("i", $session['log_Id']);
                    $updateLog->execute();
                    $updateLog->close();
                }

                $insertLog = $this->conn->prepare(
                    "INSERT INTO log_history (user_ID, uin, usertype, login_datetime) VALUES (?, ?, NULL, NOW())"
                );
                
                // Generate unique UIN: timestamp + random string (same as in authenticate.php)
                $uin = date('YmdHis') . substr(md5(uniqid(mt_rand(), true)), 0, 8);
                $insertLog->bind_param("is", $userID, $uin);
                $insertLog->execute();
                $insertLog->close();
                
                // Store UIN in session for logout tracking
                $_SESSION['uin'] = $uin;

                return [
                    'user_ID' => $userID,
                    'full_name' => trim($user['firstname'] . ' ' . $user['lastname']),
                    'email' => $user['email'],
                    'is_verified' => (int)$user['is_verified'],
                    'is_password_changed' => (int)$user['is_password_changed'],
                    'user_type' => $userTypeFound,
                    'uin' => $uin
                ];

            } catch (Exception $e) {
                error_log("login error: " . $e->getMessage());
                return null;
            }
        }
        // ADMINISTRATOR FUNCTION***********************************************************   
            public function getAllAdminRecords($admin_ID = null) {
                try {
                    // if ($admin_ID) {
                        $query = $this->conn->prepare("SELECT * FROM administrator WHERE admin_ID != ? ORDER BY created_at DESC");
                        if (!$query) {
                            throw new Exception("Prepare failed: " . $this->conn->error);
                        }
                        $query->bind_param("i", $admin_ID);
                    // } else {
                        // Fetch all admins
                    //     $query = $this->conn->prepare("SELECT * FROM administrator ORDER BY created_at DESC");
                    //     if (!$query) {
                    //         throw new Exception("Prepare failed: " . $this->conn->error);
                    //     }
                    // }

                    if ($query->execute()) {
                        return $query->get_result();
                    } else {
                        throw new Exception("Execute failed: " . $query->error);
                    }

                } catch (Exception $e) {
                    error_log("getAllAdminRecords error: " . $e->getMessage());
                    return false;
                }
            }
            public function getUsersByLevel($userlevel_id, $exclude_user_id = null) {
                try {
                    if ($exclude_user_id !== null) {
                        $sql = "SELECT u.*, l.userlevel_name 
                                FROM tbluser AS u
                                INNER JOIN tbluserlevel AS l ON u.userlevel_id = l.userlevel_id
                                WHERE u.userlevel_id = ? AND u.user_id != ?
                                ORDER BY u.created_at DESC";
                        $query = $this->conn->prepare($sql);
                        if (!$query) {
                            throw new Exception("Prepare failed: " . $this->conn->error);
                        }
                        $query->bind_param("ii", $userlevel_id, $exclude_user_id);
                    } else {
                        $sql = "SELECT u.*, l.userlevel_name 
                                FROM tbluser AS u
                                INNER JOIN tbluserlevel AS l ON u.userlevel_id = l.userlevel_id
                                WHERE u.userlevel_id = ?
                                ORDER BY u.created_at DESC";
                        $query = $this->conn->prepare($sql);
                        if (!$query) {
                            throw new Exception("Prepare failed: " . $this->conn->error);
                        }
                        $query->bind_param("i", $userlevel_id);
                    }

                    if ($query->execute()) {
                        return $query->get_result();
                    } else {
                        throw new Exception("Execute failed: " . $query->error);
                    }
                } catch (Exception $e) {
                    error_log("getUsersByLevel error: " . $e->getMessage());
                    return false;
                }
            }
            public function get_Administrator_By_ID($admin_ID) {
                $query = $this->conn->prepare(query: "SELECT * FROM administrator WHERE admin_ID = ?");
                if (!$query) {
                    return false;
                }

                $query->bind_param('i', $admin_ID);
                if ($query->execute()) {
                    $result = $query->get_result();
                    return $result->fetch_assoc(); 
                }

                return false;
            }
            public function getLoggedinAdminRecord($admin_ID) {
                try {
                    $admin_ID = intval($admin_ID);

                    $query = $this->conn->prepare("SELECT * FROM administrator WHERE admin_ID = ? ");

                    if (!$query) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }

                    $query->bind_param('i', $admin_ID);

                    if ($query->execute()) {
                        return $query->get_result();
                    } else {
                        throw new Exception("Execute failed: " . $query->error);
                    }
                } catch (Exception $e) {
                    error_log("getLoggedinCanteenStaffRecord error: " . $e->getMessage());
                    return false;
                }
            }
        // END ADMINISTRATOR FUNCTION**********************************************************

        // END ADMINISTRATOR / CANTEEN STAFF / OFFICE STAFF FUNCTION**********************************************************
            public function UpdateInformation($user_ID, $type, $firstname, $middlename, $lastname, $suffix, $gender, $birthdate, $nationality, $contact, $email, $operator_ID, $loggedInUserType) {
            // Get old values from tbluser
            $oldDataQuery = $this->conn->prepare("SELECT * FROM tbluser WHERE user_id = ?");
            $oldDataQuery->bind_param("i", $user_ID);
            $oldDataQuery->execute();
            $result = $oldDataQuery->get_result();
            $oldData = $result->fetch_assoc();
            $oldDataQuery->close();
            
            if (!$oldData) {
                return "User record not found";
            }

            // Check for duplicate contact
            $existingPhone = $this->checkExistingContact($type, $contact, $user_ID);
            if ($existingPhone['contactExists']) {
                return "Contact number already exists";
            }

            // Check for duplicate email
            $existingEmail = $this->checkExistingEmail($type, $email, $user_ID);
            if ($existingEmail['emailExists']) {
                return "Email address already exists";
            }

            // Prepare update query for tbluser
            $updateQuery = $this->conn->prepare("
                UPDATE tbluser 
                SET firstname=?, middlename=?, lastname=?, suffix=?, gender=?, birthdate=?, nationality=?, contact=?, email=? 
                WHERE user_id = ?
            ");

            if (!$updateQuery) {
                return "Failed to prepare statement: " . $this->conn->error;
            }

            $updateQuery->bind_param(
                "sssssssssi", 
                $firstname, $middlename, $lastname, $suffix, $gender, $birthdate, $nationality, $contact, $email, $user_ID
            );

            if ($updateQuery->execute()) {
                // Only log if there are actual changes
                $changes = [];
                $fields = [
                    'firstname'   => $firstname,
                    'middlename'  => $middlename,
                    'lastname'    => $lastname,
                    'suffix'      => $suffix,
                    'gender'      => $gender,
                    'birthdate'   => $birthdate,
                    'nationality' => $nationality,
                    'contact'     => $contact,
                    'email'       => $email
                ];

                foreach ($fields as $col => $newValue) {
                    if ($oldData[$col] != $newValue) {
                        $changes[] = [
                            'column_name' => $col,
                            'old_value'   => $oldData[$col],
                            'new_value'   => $newValue
                        ];
                    }
                }

                if (!empty($changes)) {
                    $action_batch_id = uniqid("batch_");
                    $this->record_activity_logs(
                        $action_batch_id, 
                        'tbluser', 
                        $user_ID, 
                        $changes, 
                        $operator_ID, 
                        $loggedInUserType, 
                        "Profile updated for $type ID $user_ID"
                    );
                }

                $updateQuery->close();
                return true;
            } else {
                $error = $updateQuery->error;
                $updateQuery->close();
                return "Failed to update record: " . $error;
            }
        }
            public function updateProfilePicture($user_ID, $type, $unique_filename, $operator_ID, $loggedInUserType) {
            // Use tbluser table for all user types
            $old_value = null;
            $select = $this->conn->prepare("SELECT image FROM tbluser WHERE user_id = ?");
            $select->bind_param("i", $user_ID);
            $select->execute();
            $select->bind_result($old_value);
            if (!$select->fetch()) return 'User not found';
            $select->close();

            $update = $this->conn->prepare("UPDATE tbluser SET image = ? WHERE user_id = ?");
            $update->bind_param("si", $unique_filename, $user_ID);
            if (!$update->execute()) return "Failed to update profile picture: " . $update->error;
            $update->close();

            $action_batch_id = uniqid("batch_");
            $changes = [
                [
                    'column_name' => 'image',
                    'old_value'   => $old_value,
                    'new_value'   => $unique_filename
                ]
            ];
            $this->record_activity_logs(
                $action_batch_id, 
                'tbluser', 
                $user_ID, 
                $changes, 
                $operator_ID, 
                $loggedInUserType, 
                "Updated profile picture for $type with ID: $user_ID"
            );

            return true;
        }
            public function updatePassword($user_ID, $type, $OldPassword, $password, $cpassword, $operator_ID, $loggedInUserType) {
            // Use tbluser table for all user types
            $stmt = $this->conn->prepare("SELECT password FROM tbluser WHERE user_id = ?");
            $stmt->bind_param("i", $user_ID);
            $stmt->execute();
            $stmt->bind_result($stored_password);
            if (!$stmt->fetch()) return 'User not found';
            $stmt->close();

            if (!password_verify($OldPassword, $stored_password)) return 'Old password is incorrect';

            if ($password !== $cpassword) return 'New password and Confirm password do not match';

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $update = $this->conn->prepare("UPDATE tbluser SET password = ? WHERE user_id = ?");
            $update->bind_param("si", $hashedPassword, $user_ID);
            if (!$update->execute()) return 'Failed to update password';
            $update->close();

            $action_batch_id = uniqid("batch_");
            $changes = [
                [
                    'column_name' => 'password',
                    'old_value'   => '[ENCRYPTED]',
                    'new_value'   => '[ENCRYPTED]'
                ]
            ];
            $this->record_activity_logs($action_batch_id, 'tbluser', $user_ID, $changes, $operator_ID, $loggedInUserType, "Password updated for $type ID $user_ID");

            return true;
            }
            public function CreateUser($firstname, $middlename, $lastname, $suffix, $gender, $birthdate, $nationality, $contact, $email, $user_type, $operator_ID = "", $loggedInUserType = "") {
                $profile_filename = "avatar.png";

                // Map user types to tbluser.userlevel_id
                $userLevels = [
                    'Administrator'   => 1,
                    'Canteen Staff'   => 2,
                    'Canteen Manager' => 3,
                ];

                $userlevel_id = $userLevels[$user_type] ?? 1; // Default to Administrator if not found

                // Generate secure password
                $uniqueCode = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
                $plainPassword = strtolower($lastname) . "_" . date("Y") . "_" . $uniqueCode;
                $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

                // Insert directly into tbluser only
                $insertTblUserSql = "INSERT INTO tbluser 
                    (firstname, middlename, lastname, suffix, gender, birthdate, nationality, contact, email, password, image, userlevel_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $insertTblUser = $this->conn->prepare($insertTblUserSql);
                if (!$insertTblUser) {
                    return "Failed to prepare user insert statement.";
                }

                $insertTblUser->bind_param(
                    "sssssssssssi",
                    $firstname,
                    $middlename,
                    $lastname,
                    $suffix,
                    $gender,
                    $birthdate,
                    $nationality,
                    $contact,
                    $email,
                    $hashedPassword,
                    $profile_filename,
                    $userlevel_id
                );

                if ($insertTblUser->execute()) {
                    $user_ID = $this->conn->insert_id;
                    $insertTblUser->close();

                    // Record audit (optional)
                    if (!empty($operator_ID)) {
                        $batch_id = uniqid("batch_");
                        $changes = [
                            [
                                'column_name' => 'user_type',
                                'old_value' => '',
                                'new_value' => $user_type
                            ],
                            [
                                'column_name' => 'full_name',
                                'old_value' => '',
                                'new_value' => trim("$firstname $middlename $lastname $suffix")
                            ],
                            [
                                'column_name' => 'email',
                                'old_value' => '',
                                'new_value' => $email
                            ]
                        ];
                        $this->record_activity_logs($batch_id, 'tbluser', $user_ID, $changes, $operator_ID, $loggedInUserType, "Created new $user_type: $firstname $lastname");
                    }

                    // Prepare and send email
                    $subject = "Your $user_type Account Has Been Created";
                    $verificationLink = "http://localhost/Canteen%20Job%20Order%20System/account-verification.php?email=" . urlencode($email) . "&id=" . urlencode($user_ID) . "&type=" . urlencode($user_type);

                    $bodyContent = "
                        <p>Dear " . htmlspecialchars(ucwords($firstname)) . ",</p>
                        <p>We are pleased to inform you that your <strong>$user_type</strong> account has been successfully created.</p>
                        <p><strong>Account Details:</strong></p>
                        <ul>
                            <li><strong>Full Name:</strong> " . htmlspecialchars(ucwords(trim("$firstname $middlename $lastname $suffix"))) . "</li>
                            <li><strong>Email:</strong> " . htmlspecialchars($email) . "</li>
                            <li><strong>Password:</strong> " . htmlspecialchars($plainPassword) . "</li>
                        </ul>
                        <p>Please keep your password safe. For your security, you may change it after logging in.</p>
                        <p><strong>To verify your account, please click the link below:</strong></p>
                        <p><a href='$verificationLink'>$verificationLink</a></p>
                        <p>Best regards,<br>Administrator</p>
                    ";
                    $this->sendEmail($subject, $bodyContent, $email);

                    return true;
                } else {
                    $error = $insertTblUser->error;
                    $insertTblUser->close();
                    return "Failed to insert user: " . $error;
                }
            }
            public function UpdateUser($user_ID, $firstname, $middlename, $lastname, $suffix, $gender, $birthdate, $nationality, $contact, $email, $user_type, $operator_ID = "", $loggedInUserType = "", $password = "", $cpassword = "") {

            // Check duplicates (exclude current user)
            if ($this->checkExistingContact($user_type, $contact, $user_ID)['contactExists']) return "Contact number already exists";
            if ($this->checkExistingEmail($user_type, $email, $user_ID)['emailExists']) return "Email address already exists";

            $updateQuery = $this->conn->prepare("UPDATE tbluser SET firstname = ?, middlename = ?, lastname = ?, suffix = ?, gender = ?, birthdate = ?, nationality = ?, contact = ?, email = ?  WHERE user_id = ? ");
            $updateQuery->bind_param("sssssssssi", $firstname, $middlename, $lastname, $suffix, $gender, $birthdate, $nationality, $contact, $email, $user_ID);

            // Handle password update if provided
            $passwordUpdated = false;
            if (!empty($password) && !empty($cpassword)) {
                if ($password !== $cpassword) {
                    return "Password and confirm password do not match.";
                }
                
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Update password in tbluser table
                $tbluserQuery = $this->conn->prepare("UPDATE tbluser SET password = ? WHERE user_id = ?");
                $tbluserQuery->bind_param("si", $hashedPassword, $user_ID);
                
                if (!$tbluserQuery->execute()) {
                    $tbluserQuery->close();
                    return "Failed to update password.";
                }
                $tbluserQuery->close();
                $passwordUpdated = true;
            }

            // Get old values for logging
            $oldDataQuery = $this->conn->prepare("SELECT * FROM tbluser WHERE user_id = ?");
            $oldDataQuery->bind_param("i", $user_ID);
            $oldDataQuery->execute();
            $result = $oldDataQuery->get_result();
            $oldData = $result->fetch_assoc();
            $oldDataQuery->close();
            
            if ($updateQuery->execute()) {
                $updateQuery->close();

                // Log user update activity
                if ($operator_ID && $loggedInUserType && $oldData) {
                    $changes = [];
                    $fields = [
                        'firstname' => $firstname,
                        'middlename' => $middlename,
                        'lastname' => $lastname,
                        'suffix' => $suffix,
                        'gender' => $gender,
                        'birthdate' => $birthdate,
                        'nationality' => $nationality,
                        'contact' => $contact,
                        'email' => $email
                    ];

                    foreach ($fields as $col => $newValue) {
                        if ($oldData[$col] != $newValue) {
                            $changes[] = [
                                'column_name' => $col,
                                'old_value' => $oldData[$col],
                                'new_value' => $newValue
                            ];
                        }
                    }

                    // Add password change to log if password was updated
                    if ($passwordUpdated) {
                        $changes[] = [
                            'column_name' => 'password',
                            'old_value' => '[ENCRYPTED]',
                            'new_value' => '[ENCRYPTED]'
                        ];
                    }

                    if (!empty($changes)) {
                        $batch_id = uniqid("batch_");
                        $this->record_activity_logs($batch_id, 'tbluser', $user_ID, $changes, $operator_ID, $loggedInUserType, "Updated $user_type profile" . ($passwordUpdated ? " and password" : ""));
                    }
                }

                return true;
            } else {
                return false;
            }
        }
        // END ADMINISTRATOR / CANTEEN STAFF / OFFICE STAFF FUNCTION**********************************************************
            
        // CANTEEN STAFF FUNCTION**********************************************************  
            public function getAllCanteenStaffRecords() {
                try {
                    $query = $this->conn->prepare("SELECT * FROM canteen_staff ORDER BY created_at DESC");
                    if (!$query) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }

                    if ($query->execute()) {
                        return $query->get_result();
                    } else {
                        throw new Exception("Execute failed: " . $query->error);
                    }

                } catch (Exception $e) {
                    error_log("getAllCanteenStaffRecords error: " . $e->getMessage());
                    return false;
                }
            }
            public function get_CanteenStaff_By_ID($canteen_staff_ID) {
                $query = $this->conn->prepare(query: "SELECT * FROM canteen_staff WHERE canteen_staff_ID = ?");
                if (!$query) {
                    return false;
                }

                $query->bind_param('i', $canteen_staff_ID);
                if ($query->execute()) {
                    $result = $query->get_result();
                    return $result->fetch_assoc(); 
                }

                return false;
            }
            public function getLoggedinCanteenStaffRecord($canteen_staff_ID) {
                try {
                    $canteen_staff_ID = intval($canteen_staff_ID);

                    $query = $this->conn->prepare("SELECT * FROM canteen_staff WHERE canteen_staff_ID = ? ");

                    if (!$query) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }

                    $query->bind_param('i', $canteen_staff_ID);

                    if ($query->execute()) {
                        return $query->get_result();
                    } else {
                        throw new Exception("Execute failed: " . $query->error);
                    }
                } catch (Exception $e) {
                    error_log("getLoggedinCanteenStaffRecord error: " . $e->getMessage());
                    return false;
                }
            }
        // END CANTEEN STAFF FUNCTION**********************************************************  
        

        // ORDER FUNCTION********************************************************** 
            public function getAllOrderRecords()
            {
                $query = "SELECT 
                    o.order_id, 
                    o.job_order_no, 
                    o.job_requester_name,
                    COALESCE(o.office_name, of.office_name) AS office_name,
                    o.office_id,
                    o.office_under_id,
                    ou.office_under_name,
                    o.email, 
                    o.needed_datetime, 
                    o.event, 
                    ts.status_name AS status,
                    o.total_amount, 
                    o.created_at, 
                    o.updated_at 
                FROM (
                    SELECT o1.*
                    FROM orders o1
                    INNER JOIN (
                        SELECT job_order_no, MAX(order_id) AS latest_order_id
                        FROM orders
                        GROUP BY job_order_no
                    ) lo ON lo.latest_order_id = o1.order_id
                ) o
                LEFT JOIN tbloffice of ON o.office_id = of.office_id
                LEFT JOIN tbl_office_under ou ON o.office_under_id = ou.office_under_id
                LEFT JOIN (
                    SELECT os1.*
                    FROM order_status os1
                    INNER JOIN (
                        SELECT order_id, MAX(order_status_id) AS latest_status_id
                        FROM order_status
                        GROUP BY order_id
                    ) latest ON latest.order_id = os1.order_id AND latest.latest_status_id = os1.order_status_id
                ) os ON os.order_id = o.order_id
                LEFT JOIN tblstatus ts ON ts.status_id = os.status_id
                ORDER BY o.order_id DESC";
                $result = $this->conn->query($query);
                return $result;
            }

            public function getFilteredOrderRecords($order_date_from = null, $order_date_to = null, $status = null)
            {
                $conditions = ["1=1"];
                $params = [];
                $types = "";

                if ($order_date_from && $order_date_to) {
                    $conditions[] = "DATE(o.created_at) BETWEEN ? AND ?";
                    $params[] = $order_date_from;
                    $params[] = $order_date_to;
                    $types .= "ss";
                } elseif ($order_date_from) {
                    $conditions[] = "DATE(o.created_at) >= ?";
                    $params[] = $order_date_from;
                    $types .= "s";
                } elseif ($order_date_to) {
                    $conditions[] = "DATE(o.created_at) <= ?";
                    $params[] = $order_date_to;
                    $types .= "s";
                }

                if (!empty($status)) {
                    $conditions[] = "ts.status_name = ?";
                    $params[] = $status;
                    $types .= "s";
                }

                $query = "SELECT 
                    o.order_id, 
                    o.job_order_no, 
                    o.job_requester_name,
                    COALESCE(o.office_name, of.office_name) as office_name,
                    o.office_id,
                    o.office_under_id,
                    ou.office_under_name,
                    o.email, 
                    o.needed_datetime, 
                    o.event, 
                    ts.status_name AS status, 
                    o.total_amount, 
                    o.created_at, 
                    o.updated_at 
                FROM (
                    SELECT o1.*
                    FROM orders o1
                    INNER JOIN (
                        SELECT job_order_no, MAX(order_id) AS latest_order_id
                        FROM orders
                        GROUP BY job_order_no
                    ) lo ON lo.latest_order_id = o1.order_id
                ) o
                LEFT JOIN tbloffice of ON o.office_id = of.office_id
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
                WHERE " . implode(" AND ", $conditions) . "
                ORDER BY o.created_at DESC";

                $stmt = $this->conn->prepare($query);
                if ($types) {
                    $stmt->bind_param($types, ...$params);
                }

                if ($stmt->execute()) {
                    return $stmt->get_result();
                } else {
                    $stmt->close();
                    return false;
                }
            }

            public function getOrderDetails($order_id)
            {
                $stmt = $this->conn->prepare("
                    SELECT 
                        o.order_id,
                        -- Full office label (main + sub-office when applicable)
                        CASE 
                            WHEN o.office_under_id IS NOT NULL AND ou.office_under_name IS NOT NULL
                                THEN CONCAT(COALESCE(o.office_name, of.office_name), ' - ', ou.office_under_name)
                            ELSE COALESCE(o.office_name, of.office_name)
                        END AS office_name,
                        -- Raw IDs and emails needed for status email routing
                        o.office_id,
                        o.office_under_id,
                        of.office_email,
                        ou.office_under_email,
                        o.email AS order_email,
                        -- For backward compatibility, keep a single email field as well
                        CASE 
                            WHEN o.office_under_id IS NOT NULL AND ou.office_under_email IS NOT NULL THEN ou.office_under_email
                            WHEN o.office_id IS NOT NULL AND of.office_email IS NOT NULL THEN of.office_email
                            ELSE o.email
                        END AS email,
                        o.needed_datetime, 
                        o.event, 
                        ts.status_name AS status,
                        o.created_at AS order_placed_at,
                        o.total_amount,
                        o.job_order_no,
                        o.office_id
                    FROM orders o
                    LEFT JOIN tbloffice of ON o.office_id = of.office_id
                    LEFT JOIN tbl_office_under ou ON o.office_under_id = ou.office_under_id
                    LEFT JOIN (
                        SELECT os1.*
                        FROM order_status os1
                        INNER JOIN (
                            SELECT order_id, MAX(order_status_id) AS latest_status_id
                            FROM order_status
                            GROUP BY order_id
                        ) latest ON latest.order_id = os1.order_id AND latest.latest_status_id = os1.order_status_id
                    ) os ON os.order_id = o.order_id
                    LEFT JOIN tblstatus ts ON ts.status_id = os.status_id
                    WHERE o.order_id = ?
                ");
                $stmt->bind_param("i", $order_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                return $result->fetch_assoc();
            }

            public function getOrderItems($order_id)
            {
                $stmt = $this->conn->prepare("
                    SELECT 
                        oi.item_id, 
                        oi.items_id,
                        i.item_name AS description,
                        i.item_unit_price AS price,
                        oi.quantity, 
                        oi.total, 
                        oi.remarks, 
                        oi.created_at AS item_created_at, 
                        o.needed_datetime
                    FROM order_items oi
                    LEFT JOIN tblitem i ON oi.items_id = i.item_id
                    INNER JOIN orders o ON oi.order_id = o.order_id
                    WHERE oi.order_id = ?
                    ORDER BY oi.created_at ASC
                ");
                $stmt->bind_param("i", $order_id);
                $stmt->execute();
                $result = $stmt->get_result();

                $items = [];
                while ($row = $result->fetch_assoc()) {
                    $items[] = $row;
                }

                return $items;
            }

            public function getOrderStatusHistory($order_id)
            {
                $stmt = $this->conn->prepare("
                    SELECT 
                        os.order_status_id,
                        os.order_id,
                        os.status_id,
                        ts.status_name,
                        os.updated_at
                    FROM order_status os
                    LEFT JOIN tblstatus ts ON ts.status_id = os.status_id
                    WHERE os.order_id = ?
                    ORDER BY os.updated_at ASC
                ");
                $stmt->bind_param("i", $order_id);
                $stmt->execute();
                $result = $stmt->get_result();

                $history = [];
                while ($row = $result->fetch_assoc()) {
                    $history[] = $row;
                }

                return $history;
            }

            public function createOrder($office_id, $office_name, $office_under_id, $email, $items_ids, $quantities, $price, $needed_datetimes, $remarks, $event, $job_requester_name, $creatorUserType = null, $operator_ID = null, $loggedInUserType = null)
            {
                $total_amount = 0.00;

                // Normalize incoming datetime-local values (e.g. 2025-12-11T08:00)
                // into proper 'Y-m-d H:i:s' strings before storing.
                $normalized_slots = [];
                if (is_array($needed_datetimes)) {
                    foreach ($needed_datetimes as $rawSlot) {
                        if (!is_string($rawSlot)) {
                            continue;
                        }
                        $rawSlot = trim($rawSlot);
                        if ($rawSlot === '') {
                            continue;
                        }

                        // Replace 'T' with space to be safe, then parse
                        $candidate = str_replace('T', ' ', $rawSlot);
                        $ts = strtotime($candidate);
                        if ($ts === false) {
                            continue;
                        }

                        $normalized_slots[] = date('Y-m-d H:i:s', $ts);
                    }
                }

                if (empty($normalized_slots)) {
                    throw new Exception("Please select at least one valid pickup date and time.");
                }

                $needed_datetime_string = implode(' | ', $normalized_slots);

                try {
                    $this->conn->begin_transaction();

                    $unique_items = array_map('intval', $items_ids);
                    if (count($unique_items) !== count(array_unique($unique_items))) {
                        throw new Exception("Duplicate items found. You can only order one of each service per order.");
                    }

                    $item_details = [];
                    for ($i = 0; $i < count($items_ids); $i++) {
                        $item_id = intval($items_ids[$i]);
                        $qty = intval($quantities[$i]);
                        if ($qty < 1) {
                            throw new Exception("Quantity (No. of Pax) for all items must be 1 or greater. Item ID: " . $item_id);
                        }
                        $price_per_unit = floatval($price[$i]);
                        $item_total = $price_per_unit * $qty;

                        $total_amount += $item_total;

                        $item_details[] = [
                            'items_id' => $item_id,
                            'qty' => $qty,
                            'price_per_unit' => $price_per_unit,
                            'item_total' => $item_total,
                            'remark' => $remarks[$i]
                        ];
                    }

                    // STOCK VALIDATION: ensure requested quantities do not exceed available stock
                    $stockStmt = $this->conn->prepare('
                        SELECT item_id, item_name, stock_qty 
                        FROM tblitem 
                        WHERE item_id = ?
                    ');

                    if (!$stockStmt) {
                        throw new Exception("Failed to prepare stock validation statement: " . $this->conn->error);
                    }

                    foreach ($item_details as &$item) {
                        $itemId = $item['items_id'];
                        $requestedQty = $item['qty'];

                        $stockStmt->bind_param("i", $itemId);
                        $stockStmt->execute();
                        $result = $stockStmt->get_result();
                        $row = $result->fetch_assoc();

                        if (!$row) {
                            throw new Exception("Item not found (ID: {$itemId}). Please contact the administrator.");
                        }

                        $available = (int)$row['stock_qty'];
                        if ($requestedQty > $available) {
                            $itemName = $row['item_name'];
                            throw new Exception("Insufficient stock for '{$itemName}'. Requested: {$requestedQty}, Available: {$available}.");
                        }

                        // Store item_name for use in emails and logs
                        $item['item_name'] = $row['item_name'];
                    }
                    unset($item); // break reference

        $stockStmt->close();

        // Generate job order number right before INSERT to avoid overwrites
        $jobOrderNo = $this->generateJobOrderNumber();

        $stmt = $this->conn->prepare("
            INSERT INTO orders (job_order_no, office_id, office_under_id, office_name, email, needed_datetime, event, total_amount, job_requester_name)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        // Ensure null is passed correctly for office_under_id when not provided
        if ($office_under_id === '' || $office_under_id === 0) {
            $office_under_id = null;
        }

        $stmt->bind_param("siisssdss", $jobOrderNo, $office_id, $office_under_id, $office_name, $email, $needed_datetime_string, $event, $total_amount, $job_requester_name);

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert order header: " . $stmt->error);
        }
                    $order_id = $this->conn->insert_id;
                    $stmt->close();

                    // Insert initial status into order_status (status_id = 4 = Pending)
                    $timezone = new DateTimeZone('Asia/Manila');
                    $now = new DateTime('now', $timezone);
                    $updated_at = $now->format('Y-m-d H:i:s');

                    $creatorRole = is_string($creatorUserType) ? strtolower(trim($creatorUserType)) : '';
                    if (in_array($creatorRole, ['administrator', 'canteen manager'], true)) {
                        $initial_status_id = 1;
                    } else {
                        // Default to pending unless explicitly canteen staff
                        $initial_status_id = ($creatorRole === 'canteen staff') ? 4 : 4;
                    }

                    $statusStmt = $this->conn->prepare("
                        INSERT INTO order_status (order_id, status_id, updated_at)
                        VALUES (?, ?, ?)
                    ");

                    $statusStmt->bind_param("iis", $order_id, $initial_status_id, $updated_at);

                    if (!$statusStmt->execute()) {
                        throw new Exception("Failed to insert initial order status: " . $statusStmt->error);
                    }

                    $statusStmt->close();

                    // Insert order items
                    $itemStmt = $this->conn->prepare("
                        INSERT INTO order_items (order_id, items_id, quantity, total, remarks)
                        VALUES (?, ?, ?, ?, ?)
                    ");

                    foreach ($item_details as $item) {
                        $itemStmt->bind_param(
                            "iidis",
                            $order_id,
                            $item['items_id'],
                            $item['qty'],
                            $item['item_total'],
                            $item['remark']
                        );

                        if (!$itemStmt->execute()) {
                            throw new Exception("Failed to insert order item: " . $itemStmt->error);
                        }
                    }

                    $itemStmt->close();

                    // DEDUCT STOCK for each item ordered and capture old values for logging
                    $updateStockStmt = $this->conn->prepare('
                        UPDATE tblitem 
                        SET stock_qty = stock_qty - ? 
                        WHERE item_id = ?
                    ');

                    if (!$updateStockStmt) {
                        throw new Exception("Failed to prepare stock update statement: " . $this->conn->error);
                    }

                    $stock_old_values = [];
                    foreach ($item_details as $item) {
                        $qty = $item['qty'];
                        $itemId = $item['items_id'];

                        // Get current stock before update for logging
                        $currentStockStmt = $this->conn->prepare("SELECT stock_qty FROM tblitem WHERE item_id = ?");
                        $currentStockStmt->bind_param("i", $itemId);
                        $currentStockStmt->execute();
                        $currentResult = $currentStockStmt->get_result();
                        $currentRow = $currentResult->fetch_assoc();
                        $stock_old_values[$itemId] = (int)$currentRow['stock_qty'];
                        $currentStockStmt->close();

                        $updateStockStmt->bind_param("ii", $qty, $itemId);

                        if (!$updateStockStmt->execute()) {
                            throw new Exception("Failed to update stock for item ID {$itemId}: " . $updateStockStmt->error);
                        }
                    }

                    $updateStockStmt->close();

                    $this->conn->commit();

                    // Log order creation activity
                    if ($operator_ID && $loggedInUserType) {
                        $batch_id = uniqid("batch_");
                        $order_changes = [
                            [
                                'column_name' => 'job_order_no',
                                'old_value' => '',
                                'new_value' => $jobOrderNo
                            ],
                            [
                                'column_name' => 'office_id',
                                'old_value' => '',
                                'new_value' => $office_id
                            ],
                            [
                                'column_name' => 'office_name',
                                'old_value' => '',
                                'new_value' => $office_name
                            ],
                            [
                                'column_name' => 'office_under_id',
                                'old_value' => '',
                                'new_value' => $office_under_id
                            ],
                            [
                                'column_name' => 'total_amount',
                                'old_value' => '',
                                'new_value' => $total_amount
                            ],
                            [
                                'column_name' => 'status',
                                'old_value' => '',
                                'new_value' => $initial_status_id
                            ]
                        ];
                        $this->record_activity_logs($batch_id, 'orders', $order_id, $order_changes, $operator_ID, $loggedInUserType, "Created order #{$jobOrderNo}");

                        // Log stock deduction activities
                        foreach ($item_details as $item) {
                            $stock_batch_id = uniqid("batch_");
                            $old_stock = $stock_old_values[$item['items_id']];
                            $new_stock = $old_stock - $item['qty'];
                            $stock_changes = [
                                [
                                    'column_name' => 'stock_qty',
                                    'old_value' => $old_stock,
                                    'new_value' => $new_stock
                                ]
                            ];
                            $this->record_activity_logs($stock_batch_id, 'tblitem', $item['items_id'], $stock_changes, $operator_ID, $loggedInUserType, "Stock deducted for order #{$jobOrderNo}");
                        }
                    }

                    // Send order submission email to office (if an email is available)
                    $recipientEmail = null;

                    // If a direct email value was provided (e.g., "Others" option), use it
                    if (!empty($email)) {
                        $recipientEmail = $email;
                    } elseif (!empty($office_id)) {
                        // Otherwise, look up the official office email from tbloffice
                        $officeStmt = $this->conn->prepare("SELECT office_email FROM tbloffice WHERE office_id = ? LIMIT 1");
                        if ($officeStmt) {
                            $officeStmt->bind_param("i", $office_id);
                            if ($officeStmt->execute()) {
                                $res = $officeStmt->get_result();
                                if ($res && ($officeRow = $res->fetch_assoc())) {
                                    $recipientEmail = $officeRow['office_email'] ?? null;
                                }
                            }
                            $officeStmt->close();
                        }
                    }

                    if (!empty($recipientEmail)) {
                        // Build a simple order summary email
                        $itemRows = '';
                        foreach ($item_details as $item) {
                            $priceDisplay = '₱' . number_format($item['price_per_unit'], 2);
                            $totalDisplay = '₱' . number_format($item['item_total'], 2);
                            // Prefer item_name if available, fall back to ID string
                            $name = isset($item['item_name']) && $item['item_name'] !== ''
                                ? htmlspecialchars($item['item_name'])
                                : 'Item #' . htmlspecialchars((string)$item['items_id']);
                            $itemRows .= "
                                <tr>
                                    <td style='border: 1px solid #ccc; padding: 8px;'>{$name}</td>
                                    <td style='border: 1px solid #ccc; padding: 8px; text-align: center;'>{$item['qty']}</td>
                                    <td style='border: 1px solid #ccc; padding: 8px; text-align: right;'>{$priceDisplay}</td>
                                    <td style='border: 1px solid #ccc; padding: 8px; text-align: right;'>{$totalDisplay}</td>
                                </tr>
                            ";
                        }

                        $totalAmountDisplay = '₱' . number_format($total_amount, 2);

                        // Format needed datetime slots for email display
                        $datetime_slots = explode(' | ', $needed_datetime_string);
                        $formatted_slots = [];
                        foreach ($datetime_slots as $slot) {
                            $timestamp = strtotime($slot);
                            if ($timestamp !== false) {
                                $formatted_slots[] = date('F j, Y g:i A', $timestamp);
                            } else {
                                $formatted_slots[] = htmlspecialchars($slot) . ' (Invalid Date)';
                            }
                        }
                        $neededDatetime = implode('<br>', $formatted_slots);

                        $subject = "Order {$jobOrderNo} - Submitted";

                        $officeLabel = !empty($office_name) ? $office_name : 'N/A';

                        $bodyContent = "
                            <html style='font-family: Arial, sans-serif; line-height: 1.6;'>
                            <body>
                                <h2>Job Order {$jobOrderNo} Submitted</h2>
                                <p>Your job order has been successfully submitted to the Canteen for processing.</p>
                                <p><strong>Current Status:</strong> On-going (pending processing by Canteen Staff)</p>

                                <h3>Order Summary</h3>
                                <ul style='list-style-type: none; padding: 0;'>
                                    <li><strong>Job Order No:</strong> {$jobOrderNo}</li>
                                    <li><strong>Ordered By:</strong> {$officeLabel}</li>
                                    <li><strong>Needed By:</strong> {$neededDatetime}</li>
                                    <li><strong>Total Amount:</strong> <span style='font-weight: bold;'>{$totalAmountDisplay}</span></li>
                                </ul>

                                <h3>Requested Items</h3>
                                <table style='width: 100%; border-collapse: collapse;'>
                                    <thead>
                                        <tr>
                                            <th style='border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2; text-align: left;'>Item Name</th>
                                            <th style='border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2; text-align: center;'>QTY</th>
                                            <th style='border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2; text-align: right;'>Unit Price</th>
                                            <th style='border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2; text-align: right;'>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {$itemRows}
                                    </tbody>
                                </table>
                            </body>
                            </html>
                        ";

                        $this->sendEmail($subject, $bodyContent, $recipientEmail);
                    }

                    return true;

                } catch (Exception $e) {
                    $this->conn->rollback();
                    return $e->getMessage();
                }
            }

            public function updateOrderStatus($order_id, $status, $cancellation_reason = null, $updatedByType = null, $operator_ID = null, $loggedInUserType = null)
            {
                try {
                    $this->conn->begin_transaction();

                    if (empty($order_id) || empty($status)) {
                        throw new Exception("Invalid order data provided.");
                    }

                    // Map human-readable status to one or more numeric status_id values
                    // 1 = On-going, 2 = Cancelled, 3 = Completed, 4 = Pending, 5 = Approved, 6 = Ready for Pickup
                    $status_ids = [];

                    $normalizedStatus = strtolower(trim($status));
                    $actorRole = is_string($updatedByType) ? strtolower(trim($updatedByType)) : '';

                    if ($normalizedStatus === 'approved') {
                        // Explicit Approved status request
                        $status_ids = [5, 1];
                    } elseif ($normalizedStatus === 'completed') {
                        $status_ids = [3];
                    } elseif ($normalizedStatus === 'cancelled') {
                        $status_ids = [2];
                    } elseif ($normalizedStatus === 'ready for pickup') {
                        $status_ids = [6];
                    } elseif ($normalizedStatus === 'on-going') {
                        if (in_array($actorRole, ['administrator', 'canteen manager'], true)) {
                            // Admin / Manager approval logs both Approved then On-going
                            $status_ids = [5, 1];
                        } else {
                            $status_ids = [1];
                        }
                    } else {
                        // Fallback / explicit On-going
                        $status_ids = [1];
                    }

                    // Insert new status row(s) into order_status
                    $timezone = new DateTimeZone('Asia/Manila');
                    $now = new DateTime('now', $timezone);
                    $updated_at = $now->format('Y-m-d H:i:s');

                    $statusStmt = $this->conn->prepare("
                        INSERT INTO order_status (order_id, status_id, updated_at)
                        VALUES (?, ?, ?)
                    ");

                    foreach ($status_ids as $status_id) {
                        $statusStmt->bind_param("iis", $order_id, $status_id, $updated_at);

                        if (!$statusStmt->execute()) {
                            throw new Exception("Failed to insert order status: " . $statusStmt->error);
                        }
                    }

                    $statusStmt->close();

                    // Generate job reference number if order is being completed
                    if ($normalizedStatus === 'completed') {
                        $jobReferenceNo = $this->generateJobReferenceNumber($updatedByType);
                        
                        // Update the order with the job reference number
                        $refStmt = $this->conn->prepare("
                            UPDATE orders 
                            SET job_reference_no = ? 
                            WHERE order_id = ?
                        ");
                        if (!$refStmt) {
                            throw new Exception("Failed to prepare job reference update: " . $this->conn->error);
                        }
                        $refStmt->bind_param("si", $jobReferenceNo, $order_id);
                        if (!$refStmt->execute()) {
                            throw new Exception("Failed to update job reference number: " . $refStmt->error);
                        }
                        $refStmt->close();
                    }

                    // Normalize status for email handling
                    $normalizedForEmail = strtolower($status);

                    // Treat Admin / Canteen Manager transition to On-going as an approval email event
                    $effectiveStatusForEmail = $normalizedForEmail;
                    if ($normalizedForEmail === 'on-going' && in_array($actorRole, ['administrator', 'canteen manager'], true)) {
                        $effectiveStatusForEmail = 'approved';
                    }

                    // Send email notifications for key status changes
                    // Now includes: completed, cancelled, approved, ready for pickup
                    if (in_array($effectiveStatusForEmail, ['completed', 'cancelled', 'approved', 'ready for pickup'], true)) {
                        
                        $details = $this->getOrderDetails($order_id);
                        
                        if (!$details) {
                            $this->conn->commit();
                            return true;
                        }
                        
                        $items = $this->getOrderItems($order_id);
                        
                        if ($items !== false) {
                            
                            $itemRows = '';
                            $totalAmountDisplay = '₱' . number_format($details['total_amount'], 2);

                            $neededDatetimeString = $details['needed_datetime']; 
                            $datetime_slots = explode(' | ', $neededDatetimeString);

                            $formatted_slots = [];

                            foreach ($datetime_slots as $slot) {
                                $timestamp = strtotime($slot);
                                
                                if ($timestamp !== false) {
                                    $formatted_slots[] = date('F j, Y g:i A', $timestamp);
                                } else {
                                    $formatted_slots[] = htmlspecialchars($slot) . ' (Invalid Date)';
                                }
                            }

                            $neededDatetime = implode('<br>', $formatted_slots);


                            $reasonHtml = ''; 
                            
                            
                            if ($effectiveStatusForEmail === 'completed') {
                                $subject = "Order {$details['job_order_no']} - Completed! 🎉";
                                $mainHeading = "Order {$details['job_order_no']} Completed! 🎉";
                                $mainParagraph = "Your job order has been successfully marked as <b>Completed</b> by the Canteen Staff. The order is now closed.";
                                $itemTableTitle = "Final Items Delivered";
                            } elseif ($effectiveStatusForEmail === 'cancelled') {
                                $subject = "Order {$details['job_order_no']} - Cancelled ❌";
                                $mainHeading = "Order {$details['job_order_no']} Cancelled ❌";
                                $mainParagraph = "Your job order has been marked as <b>Cancelled</b> by the Canteen Staff.";
                                $itemTableTitle = "Items in the Cancelled Order";

                                if (!empty($cancellation_reason)) {
                                    $formatted_reason = nl2br(htmlspecialchars($cancellation_reason)); 
                                    $reasonHtml = "
                                        <div style='background-color: #fceae7; border-left: 5px solid #dc3545; padding: 10px; margin-top: 20px;'>
                                            <p style='margin: 0;'><strong>Reason for Cancellation:</strong></p>
                                            <p style='margin: 5px 0 0 0;'>{$formatted_reason}</p>
                                        </div>
                                    ";
                                }
                            } elseif ($effectiveStatusForEmail === 'approved') {
                                $subject = "Order {$details['job_order_no']} - Approved 👍";
                                $mainHeading = "Order {$details['job_order_no']} Approved 👍";
                                $mainParagraph = "Your job order has been successfully marked as <b>Approved</b> by the Canteen Staff.";
                                $itemTableTitle = "Approved Items";
                            } elseif ($effectiveStatusForEmail === 'ready for pickup') {
                                $subject = "Order {$details['job_order_no']} - Ready for Pickup 📦";
                                $mainHeading = "Order {$details['job_order_no']} Ready for Pickup 📦";
                                $mainParagraph = "Your job order has been marked as <b>Ready for Pickup</b>. Please proceed to the canteen to claim your order.";
                                $itemTableTitle = "Items Ready for Pickup";
                            }
                            
                            foreach ($items as $item) {
                                $priceDisplay = '₱' . number_format($item['price'], 2);
                                $totalDisplay = '₱' . number_format($item['total'], 2);
                                $itemRows .= "
                                    <tr>
                                        <td style='border: 1px solid #ccc; padding: 8px;'>{$item['description']}</td>
                                        <td style='border: 1px solid #ccc; padding: 8px; text-align: center;'>{$item['quantity']}</td>
                                        <td style='border: 1px solid #ccc; padding: 8px; text-align: right;'>{$priceDisplay}</td>
                                        <td style='border: 1px solid #ccc; padding: 8px; text-align: right;'>{$totalDisplay}</td>
                                    </tr>
                                ";
                            }

                            $bodyContent = "
                                <html style='font-family: Arial, sans-serif; line-height: 1.6;'>
                                <body>
                                    <h2>{$mainHeading}</h2>
                                    <p>{$mainParagraph}</p>

                                    {$reasonHtml} <h3>Order Summary</h3>
                                    <ul style='list-style-type: none; padding: 0;'>
                                        <li><strong>Job Order No:</strong> {$details['job_order_no']}</li>
                                        <li><strong>Ordered By:</strong> {$details['office_name']}</li>
                                        <li><strong>Needed By:</strong> {$neededDatetime}</li>
                                        <li><strong>Total Amount:</strong> <span style='font-weight: bold;'>{$totalAmountDisplay}</span></li>
                                    </ul>

                                    <h3>{$itemTableTitle}</h3>
                                    <table style='width: 100%; border-collapse: collapse;'>
                                        <thead>
                                            <tr>
                                                <th style='border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2; text-align: left;'>Description</th>
                                                <th style='border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2; text-align: center;'>QTY</th>
                                                <th style='border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2; text-align: right;'>Unit Price</th>
                                                <th style='border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2; text-align: right;'>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {$itemRows}
                                        </tbody>
                                    </table>
                                </body>
                                </html>
                            ";

                            // Resolve recipient email based on current data in the orders table
                            // 1) Look up office_id, office_under_id, and order email from orders
                            $recipientEmail = '';
                            $officeId = null;
                            $officeUnderId = null;

                            $ordStmt = $this->conn->prepare("SELECT office_id, office_under_id, email FROM orders WHERE order_id = ? LIMIT 1");
                            if ($ordStmt) {
                                $ordStmt->bind_param('i', $order_id);
                                if ($ordStmt->execute()) {
                                    $ordRes = $ordStmt->get_result();
                                    if ($ordRow = $ordRes->fetch_assoc()) {
                                        $officeId = $ordRow['office_id'] ?? null;
                                        $officeUnderId = $ordRow['office_under_id'] ?? null;
                                        $orderEmail = trim((string)($ordRow['email'] ?? ''));

                                        // 2) If office_under_id is present, prefer office_under_email from tbl_office_under
                                        if (!empty($officeUnderId)) {
                                            $subStmt = $this->conn->prepare("SELECT office_under_email FROM tbl_office_under WHERE office_under_id = ? LIMIT 1");
                                            if ($subStmt) {
                                                $subStmt->bind_param('i', $officeUnderId);
                                                if ($subStmt->execute()) {
                                                    $subRes = $subStmt->get_result();
                                                    if ($subRow = $subRes->fetch_assoc()) {
                                                        $subEmail = trim((string)($subRow['office_under_email'] ?? ''));
                                                        if ($subEmail !== '') {
                                                            $recipientEmail = $subEmail;
                                                        }
                                                    }
                                                }
                                                $subStmt->close();
                                            }
                                        }

                                        // 3) If still no email, try main office office_email from tbloffice
                                        if ($recipientEmail === '' && !empty($officeId)) {
                                            $offStmt = $this->conn->prepare("SELECT office_email FROM tbloffice WHERE office_id = ? LIMIT 1");
                                            if ($offStmt) {
                                                $offStmt->bind_param('i', $officeId);
                                                if ($offStmt->execute()) {
                                                    $offRes = $offStmt->get_result();
                                                    if ($offRow = $offRes->fetch_assoc()) {
                                                        $offEmail = trim((string)($offRow['office_email'] ?? ''));
                                                        if ($offEmail !== '') {
                                                            $recipientEmail = $offEmail;
                                                        }
                                                    }
                                                }
                                                $offStmt->close();
                                            }
                                        }

                                        // 4) Fallback to the order's own email field
                                        if ($recipientEmail === '' && $orderEmail !== '') {
                                            $recipientEmail = $orderEmail;
                                        }
                                    }
                                }
                                $ordStmt->close();
                            }

                            // Only send email if a valid email address is present
                            if (!empty($recipientEmail)) {
                                $this->sendEmail(
                                    $subject, 
                                    $bodyContent, 
                                    $recipientEmail
                                );
                            }
                        }
                    }
                    
                    $this->conn->commit();

                    // Log order status update activity
                    if ($operator_ID && $loggedInUserType) {
                        // Get previous status for logging
                        $prevStatusStmt = $this->conn->prepare("
                            SELECT os.status_id, s.status_name
                            FROM order_status os
                            JOIN tblstatus s ON os.status_id = s.status_id
                            WHERE os.order_id = ?
                            ORDER BY os.updated_at DESC
                            LIMIT 1, 1
                        ");
                        $prevStatusStmt->bind_param("i", $order_id);
                        $prevStatusStmt->execute();
                        $prevResult = $prevStatusStmt->get_result();
                        $prevStatusRow = $prevResult->fetch_assoc();
                        $prevStatusStmt->close();

                        $old_status = $prevStatusRow ? $prevStatusRow['status_name'] : 'Unknown';
                        $new_status = ucfirst($status);

                        $batch_id = uniqid("batch_");
                        $status_changes = [
                            [
                                'column_name' => 'status',
                                'old_value' => $old_status,
                                'new_value' => $new_status
                            ]
                        ];

                        // Get order details for description
                        $orderDetails = $this->getOrderDetails($order_id);
                        $job_order_no = $orderDetails ? $orderDetails['job_order_no'] : "ID #{$order_id}";

                        $this->record_activity_logs($batch_id, 'order_status', $order_id, $status_changes, $operator_ID, $loggedInUserType, "Updated order #{$job_order_no} status from {$old_status} to {$new_status}");

                        // Log cancellation reason if provided
                        if ($status === 'cancelled' && $cancellation_reason) {
                            $reason_batch_id = uniqid("batch_");
                            $reason_changes = [
                                [
                                    'column_name' => 'cancellation_reason',
                                    'old_value' => '',
                                    'new_value' => $cancellation_reason
                                ]
                            ];
                            $this->record_activity_logs($reason_batch_id, 'order_status', $order_id, $reason_changes, $operator_ID, $loggedInUserType, "Added cancellation reason for order #{$job_order_no}");
                        }
                    }

                    return true;

                } catch (Exception $e) {
                    $this->conn->rollback();
                    return $e->getMessage();
                }
            }

            public function confirmOrderWithReceipt($order_id, $status, $receipt_filename)
            {
                try {
                    $this->conn->begin_transaction();

                    if (empty($order_id) || empty($status) || empty($receipt_filename)) {
                        throw new Exception("Missing required order information.");
                    }

                    // Update receipt only (orders.status column no longer exists)
                    $stmt = $this->conn->prepare("
                        UPDATE orders
                        SET receipt = ?
                        WHERE order_id = ?
                    ");
                    $stmt->bind_param("si", $receipt_filename, $order_id);

                    if (!$stmt->execute()) {
                        throw new Exception("Failed to update order with receipt.");
                    }

                    $stmt->close();

                    // Insert a corresponding status row
                    $status_id = 1;
                    if ($status === 'Completed') {
                        $status_id = 3;
                    } elseif ($status === 'Cancelled') {
                        $status_id = 2;
                    }

                    $timezone = new DateTimeZone('Asia/Manila');
                    $now = new DateTime('now', $timezone);
                    $updated_at = $now->format('Y-m-d H:i:s');

                    $statusStmt = $this->conn->prepare("
                        INSERT INTO order_status (order_id, status_id, updated_at)
                        VALUES (?, ?, ?)
                    ");
                    $statusStmt->bind_param("iis", $order_id, $status_id, $updated_at);

                    if (!$statusStmt->execute()) {
                        throw new Exception("Failed to insert order status: " . $statusStmt->error);
                    }

                    $statusStmt->close();

                    $this->conn->commit();
                    return true;

                } catch (Exception $e) {
                    $this->conn->rollback();
                    return $e->getMessage();
                }
            }

            public function countOnGoingOrders()
            {
                try {
                    $stmt = $this->conn->prepare("
                        SELECT COUNT(DISTINCT o.order_id) AS total 
                        FROM orders o
                        INNER JOIN (
                            SELECT os1.*
                            FROM order_status os1
                            INNER JOIN (
                                SELECT order_id, MAX(updated_at) AS max_updated
                                FROM order_status
                                GROUP BY order_id
                            ) latest ON latest.order_id = os1.order_id AND latest.max_updated = os1.updated_at
                        ) os ON os.order_id = o.order_id
                        INNER JOIN tblstatus ts ON ts.status_id = os.status_id
                        WHERE ts.status_name = 'On-going'
                    ");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();

                    return (int)$row['total'];

                } catch (Exception $e) {
                    return 0;
                }
            }

            public function getOrderRecordsForReports($order_date_from = null, $order_date_to = null, $status = null)
            {
                $conditions = ["1=1"];
                $params = [];
                $types = "";

                if ($order_date_from && $order_date_to) {
                    $conditions[] = "DATE(o.created_at) BETWEEN ? AND ?";
                    $params[] = $order_date_from;
                    $params[] = $order_date_to;
                    $types .= "ss";
                } elseif ($order_date_from) {
                    $conditions[] = "DATE(o.created_at) >= ?";
                    $params[] = $order_date_from;
                    $types .= "s";
                } elseif ($order_date_to) {
                    $conditions[] = "DATE(o.created_at) <= ?";
                    $params[] = $order_date_to;
                    $types .= "s";
                }

                if (!empty($status)) {
                    $conditions[] = "ts.status_name = ?";
                    $params[] = $status;
                    $types .= "s";
                }

                $query = "
                    SELECT 
                        o.order_id,
                        o.job_order_no,
                        COALESCE(o.office_name, of.office_name) AS office_name,
                        of.office_type_id,
                        o.email,
                        ts.status_name AS status,
                        o.total_amount,
                        o.created_at AS order_date,
                        oi.item_id,
                        i.item_name AS description,
                        oi.quantity,
                        oi.remarks,
                        i.item_unit_price AS price,
                        oi.total AS subtotal
                    FROM orders o
                    LEFT JOIN tbloffice of ON o.office_id = of.office_id
                    LEFT JOIN order_items oi ON o.order_id = oi.order_id
                    LEFT JOIN tblitem i ON oi.items_id = i.item_id
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
                    WHERE " . implode(" AND ", $conditions) . "
                    ORDER BY o.created_at DESC, o.order_id, oi.item_id
                ";

                $stmt = $this->conn->prepare($query);
                if ($types) {
                    $stmt->bind_param($types, ...$params);
                }

                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    $stmt->close();

                    $orders = [];
                    while ($row = $result->fetch_assoc()) {
                        $orderID = $row['order_id'];
                        if (!isset($orders[$orderID])) {
                            $orders[$orderID] = [
                                'order_id' => $row['order_id'],
                                'job_order_no' => $row['job_order_no'],
                                'office_name' => $row['office_name'],
                                'office_type_id' => $row['office_type_id'] ?? null,
                                'status' => $row['status'],
                                'total_amount' => $row['total_amount'],
                                'order_date' => $row['order_date'],
                                'items' => []
                            ];
                        }

                        if (!empty($row['description'])) {
                            $orders[$orderID]['items'][] = [
                                'description' => $row['description'],
                                'quantity' => $row['quantity'],
                                'unit' => null,
                                'remarks' => $row['remarks'],
                                'price' => $row['price'],
                                'subtotal' => $row['subtotal']
                            ];
                        }
                    }

                    return array_values($orders);
                } else {
                    $stmt->close();
                    return false;
                }
            }

            public function getOrdersToday() {
                try {
                    $todayDate = date('Y-m-d');
                    $searchPattern = "%" . $todayDate . "T%";
                    $sql = "
                        SELECT 
                            o.order_id,
                            o.job_order_no,
                            o.job_requester_name,
                            COALESCE(o.office_name, of.office_name) AS office_name,
                            o.office_under_id,
                            ou.office_under_name,
                            COALESCE(o.email, of.office_email) AS email,
                            o.event,
                            ts.status_name AS status,
                            o.total_amount,
                            o.needed_datetime,
                            o.created_at AS order_created_at,
                            oi.item_id,
                            oi.quantity,
                            oi.total,
                            oi.remarks
                        FROM orders o
                        LEFT JOIN tbloffice of ON o.office_id = of.office_id
                        LEFT JOIN tbl_office_under ou ON o.office_under_id = ou.office_under_id
                        LEFT JOIN order_items oi ON o.order_id = oi.order_id
                        LEFT JOIN (
                            SELECT os1.*
                            FROM order_status os1
                            INNER JOIN (
                                SELECT order_id, MAX(order_status_id) AS latest_status_id
                                FROM order_status
                                GROUP BY order_id
                            ) latest ON latest.order_id = os1.order_id AND latest.latest_status_id = os1.order_status_id
                        ) os ON os.order_id = o.order_id
                        LEFT JOIN tblstatus ts ON ts.status_id = os.status_id
                        WHERE o.needed_datetime LIKE ?
                        ORDER BY o.needed_datetime ASC, o.order_id, oi.item_id
                    ";

                    $stmt = $this->conn->prepare($sql);
                    if (!$stmt) throw new Exception("Prepare failed: " . $this->conn->error);
                    $stmt->bind_param("s", $searchPattern);

                    if ($stmt->execute()) {
                        $result = $stmt->get_result();
                        $orders = [];

                        while ($row = $result->fetch_assoc()) {
                            $orderID = $row['order_id'];

                            if (!isset($orders[$orderID])) {
                                $orders[$orderID] = [
                                    'order_id' => $row['order_id'],
                                    'job_order_no' => $row['job_order_no'],
                                    'office_name' => $row['office_name'],
                                    'office_under_id' => $row['office_under_id'] ?? null,
                                    'office_under_name' => $row['office_under_name'] ?? null,
                                    'email' => $row['email'],
                                    'status' => $row['status'],
                                    'total_amount' => $row['total_amount'],
                                    'needed_datetime' => $row['needed_datetime'],
                                    'created_at' => $row['order_created_at'],
                                    'items' => []
                                ];
                            }

                            if (!empty($row['item_id'])) {
                                $orders[$orderID]['items'][] = [
                                    'item_id' => $row['item_id'],
                                    'quantity' => $row['quantity'],
                                    'total' => $row['total'],
                                    'remarks' => $row['remarks']
                                ];
                            }
                        }

                        return array_values($orders);
                    } else {
                        throw new Exception("Execute failed: " . $stmt->error);
                    }

                } catch (Exception $e) {
                    error_log("getOrdersToday error: " . $e->getMessage());
                    return false;
                }
            }

            public function getOrdersTodayByHour() {
                try {
                    $sql = "
                        SELECT HOUR(o.needed_datetime) AS order_hour, COUNT(*) AS total_orders
                        FROM orders o
                        WHERE DATE(o.needed_datetime) = CURDATE()
                        GROUP BY HOUR(o.needed_datetime)
                        ORDER BY order_hour ASC
                    ";

                    $result = $this->conn->query($sql);
                    $data = [];

                    while ($row = $result->fetch_assoc()) {
                        $data[$row['order_hour']] = $row['total_orders'];
                    }

                    return $data;
                } catch (Exception $e) {
                    error_log("getOrdersTodayByHour error: " . $e->getMessage());
                    return [];
                }
            }

            public function getOrders() 
            {
                $sql = "
                    SELECT 
                        o.order_id,
                        o.job_order_no,
                        o.job_requester_name,
                        COALESCE(o.office_name, of.office_name) AS office_name,
                        o.email,
                        o.event,
                        ts.status_name AS status,
                        o.total_amount,
                        o.needed_datetime
                    FROM orders o
                    LEFT JOIN tbloffice of ON o.office_id = of.office_id
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
                    ORDER BY o.needed_datetime ASC
                ";

                $stmt = $this->conn->prepare($sql);
                if (!$stmt) {
                    return [];
                }

                if (!$stmt->execute()) {
                    return [];
                }

                $result = $stmt->get_result();
                $orders = [];
                while ($row = $result->fetch_assoc()) {
                    $orders[] = $row;
                }
                return $orders;
            }
        // END ORDER FUNCTION**********************************************************  

        // OFFICE / OFFICE TYPE FUNCTIONS**********************************************************  
            public function getOfficeTypes() {
                try {
                    $query = $this->conn->prepare("SELECT office_type_id, office_type_name, NULL AS created_at FROM tblofficetype ORDER BY office_type_name ASC");
                    if (!$query) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    if ($query->execute()) {
                        return $query->get_result();
                    } else {
                        throw new Exception("Execute failed: " . $query->error);
                    }
                } catch (Exception $e) {
                    error_log("getOfficeTypes error: " . $e->getMessage());
                    return false;
                }
            }

            public function getOfficesWithType() {
                try {
                    $sql = "
                        SELECT o.office_id, o.office_type_id, o.office_name, o.office_email, NULL AS created_at,
                               t.office_type_name
                        FROM tbloffice AS o
                        INNER JOIN tblofficetype AS t ON o.office_type_id = t.office_type_id
                        ORDER BY t.office_type_name ASC, o.office_name ASC
                    ";
                    $query = $this->conn->prepare($sql);
                    if (!$query) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    if ($query->execute()) {
                        return $query->get_result();
                    } else {
                        throw new Exception("Execute failed: " . $query->error);
                    }
                } catch (Exception $e) {
                    error_log("getOfficesWithType error: " . $e->getMessage());
                    return false;
                }
            }

            public function createOfficeType($office_type_name, $operator_ID, $loggedInUserType) {
                try {
                    $check = $this->conn->prepare("SELECT office_type_id FROM tblofficetype WHERE office_type_name = ?");
                    if (!$check) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $check->bind_param("s", $office_type_name);
                    $check->execute();
                    $res = $check->get_result();
                    if ($res && $res->num_rows > 0) {
                        return "Office type already exists";
                    }
                    $check->close();

                    $query = $this->conn->prepare("INSERT INTO tblofficetype (office_type_name) VALUES (?)");
                    if (!$query) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $query->bind_param("s", $office_type_name);

                    if ($query->execute()) {
                        $office_type_id = $this->conn->insert_id;
                        $query->close();

                        $batch_id = uniqid("batch_");
                        $changes = [[
                            'column_name' => 'office_type_name',
                            'old_value'   => '',
                            'new_value'   => $office_type_name
                        ]];
                        $this->record_activity_logs($batch_id, 'tblofficetype', $office_type_id, $changes, $operator_ID, $loggedInUserType, "Created office type ID $office_type_id");

                        return true;
                    } else {
                        return "Failed to insert office type: " . $query->error;
                    }
                } catch (Exception $e) {
                    error_log("createOfficeType error: " . $e->getMessage());
                    return "Error creating office type";
                }
            }

            public function updateOfficeType($office_type_id, $office_type_name, $operator_ID, $loggedInUserType) {
                try {
                    $oldQuery = $this->conn->prepare("SELECT office_type_name FROM tblofficetype WHERE office_type_id = ?");
                    if (!$oldQuery) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $oldQuery->bind_param("i", $office_type_id);
                    $oldQuery->execute();
                    $oldResult = $oldQuery->get_result();
                    $oldRow = $oldResult ? $oldResult->fetch_assoc() : null;
                    $oldQuery->close();

                    if (!$oldRow) {
                        return "Office type not found";
                    }

                    $check = $this->conn->prepare("SELECT office_type_id FROM tblofficetype WHERE office_type_name = ? AND office_type_id != ?");
                    if (!$check) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $check->bind_param("si", $office_type_name, $office_type_id);
                    $check->execute();
                    $res = $check->get_result();
                    if ($res && $res->num_rows > 0) {
                        $check->close();
                        return "Office type already exists";
                    }
                    $check->close();

                    $query = $this->conn->prepare("UPDATE tblofficetype SET office_type_name = ? WHERE office_type_id = ?");
                    if (!$query) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $query->bind_param("si", $office_type_name, $office_type_id);

                    if ($query->execute()) {
                        $query->close();

                        if ($oldRow['office_type_name'] !== $office_type_name) {
                            $batch_id = uniqid("batch_");
                            $changes = [[
                                'column_name' => 'office_type_name',
                                'old_value'   => $oldRow['office_type_name'],
                                'new_value'   => $office_type_name
                            ]];
                            $this->record_activity_logs($batch_id, 'tblofficetype', $office_type_id, $changes, $operator_ID, $loggedInUserType, "Updated office type ID $office_type_id");
                        }

                        return true;
                    } else {
                        return "Failed to update office type: " . $query->error;
                    }
                } catch (Exception $e) {
                    error_log("updateOfficeType error: " . $e->getMessage());
                    return "Error updating office type";
                }
            }

            public function createOffice($office_type_id, $office_name, $office_email, $operator_ID, $loggedInUserType) {
                try {
                    $check = $this->conn->prepare("SELECT office_id FROM tbloffice WHERE office_name = ? AND office_type_id = ?");
                    if (!$check) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $check->bind_param("si", $office_name, $office_type_id);
                    $check->execute();
                    $res = $check->get_result();
                    if ($res && $res->num_rows > 0) {
                        return "Office already exists for this type";
                    }
                    $check->close();

                    $query = $this->conn->prepare("INSERT INTO tbloffice (office_type_id, office_name, office_email) VALUES (?, ?, ?)");
                    if (!$query) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $query->bind_param("iss", $office_type_id, $office_name, $office_email);

                    if ($query->execute()) {
                        $office_id = $this->conn->insert_id;
                        $query->close();

                        $batch_id = uniqid("batch_");
                        $changes = [
                            [
                                'column_name' => 'office_type_id',
                                'old_value'   => '',
                                'new_value'   => $office_type_id
                            ],
                            [
                                'column_name' => 'office_name',
                                'old_value'   => '',
                                'new_value'   => $office_name
                            ],
                            [
                                'column_name' => 'office_email',
                                'old_value'   => '',
                                'new_value'   => $office_email
                            ]
                        ];
                        $this->record_activity_logs($batch_id, 'tbloffice', $office_id, $changes, $operator_ID, $loggedInUserType, "Created office ID $office_id");

                        return $office_id;
                    } else {
                        return "Failed to insert office: " . $query->error;
                    }
                } catch (Exception $e) {
                    error_log("createOffice error: " . $e->getMessage());
                    return "Error creating office";
                }
            }

            public function createOfficeUnder($office_id, $office_under_name, $office_under_email, $operator_ID, $loggedInUserType) {
                if ($office_under_name === '') {
                    return true;
                }

                try {
                    $stmt = $this->conn->prepare("INSERT INTO tbl_office_under (office_id, office_under_name, office_under_email) VALUES (?, ?, ?)");
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $stmt->bind_param("iss", $office_id, $office_under_name, $office_under_email);

                    if ($stmt->execute()) {
                        $office_under_id = $this->conn->insert_id;
                        $stmt->close();

                        if (!empty($operator_ID)) {
                            $batch_id = uniqid("batch_");
                            $changes = [
                                [
                                    'column_name' => 'office_id',
                                    'old_value'   => '',
                                    'new_value'   => $office_id
                                ],
                                [
                                    'column_name' => 'office_under_name',
                                    'old_value'   => '',
                                    'new_value'   => $office_under_name
                                ],
                                [
                                    'column_name' => 'office_under_email',
                                    'old_value'   => '',
                                    'new_value'   => $office_under_email
                                ]
                            ];
                            $this->record_activity_logs(
                                $batch_id,
                                'tbl_office_under',
                                $office_under_id,
                                $changes,
                                $operator_ID,
                                $loggedInUserType,
                                "Created sub office ID $office_under_id for office ID $office_id"
                            );
                        }

                        return true;
                    } else {
                        $error = $stmt->error;
                        $stmt->close();
                        return "Failed to insert sub office: " . $error;
                    }
                } catch (Exception $e) {
                    error_log("createOfficeUnder error: " . $e->getMessage());
                    return "Error creating sub office";
                }
            }

            public function getSubOffices($office_id) {
                try {
                    $stmt = $this->conn->prepare("SELECT office_under_id, office_id, office_under_name, office_under_email FROM tbl_office_under WHERE office_id = ? ORDER BY office_under_id");
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $stmt->bind_param("i", $office_id);
                    if ($stmt->execute()) {
                        return $stmt->get_result();
                    } else {
                        throw new Exception("Execute failed: " . $stmt->error);
                    }
                } catch (Exception $e) {
                    error_log("getSubOffices error: " . $e->getMessage());
                    return false;
                }
            }

            public function updateSubOffice($office_under_id, $office_under_name, $operator_ID, $loggedInUserType) {
                try {
                    $oldQuery = $this->conn->prepare("SELECT office_under_name FROM tbl_office_under WHERE office_under_id = ?");
                    if (!$oldQuery) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $oldQuery->bind_param("i", $office_under_id);
                    $oldQuery->execute();
                    $oldResult = $oldQuery->get_result();
                    $oldRow = $oldResult ? $oldResult->fetch_assoc() : null;
                    $oldQuery->close();

                    if (!$oldRow) {
                        return "Sub office not found";
                    }

                    $stmt = $this->conn->prepare("UPDATE tbl_office_under SET office_under_name = ? WHERE office_under_id = ?");
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $stmt->bind_param("si", $office_under_name, $office_under_id);

                    if ($stmt->execute()) {
                        $stmt->close();

                        if (!empty($operator_ID)) {
                            $batch_id = uniqid("batch_");
                            $changes = [
                                [
                                    'column_name' => 'office_under_name',
                                    'old_value'   => $oldRow['office_under_name'],
                                    'new_value'   => $office_under_name
                                ]
                            ];
                            $this->record_activity_logs(
                                $batch_id,
                                'tbl_office_under',
                                $office_under_id,
                                $changes,
                                $operator_ID,
                                $loggedInUserType,
                                "Updated sub office ID $office_under_id"
                            );
                        }

                        return true;
                    } else {
                        $error = $stmt->error;
                        $stmt->close();
                        return "Failed to update sub office: " . $error;
                    }
                } catch (Exception $e) {
                    error_log("updateSubOffice error: " . $e->getMessage());
                    return "Error updating sub office";
                }
            }

            public function deleteSubOffice($office_under_id, $operator_ID, $loggedInUserType) {
                try {
                    $oldQuery = $this->conn->prepare("SELECT office_id, office_under_name FROM tbl_office_under WHERE office_under_id = ?");
                    if (!$oldQuery) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $oldQuery->bind_param("i", $office_under_id);
                    $oldQuery->execute();
                    $oldResult = $oldQuery->get_result();
                    $oldRow = $oldResult ? $oldResult->fetch_assoc() : null;
                    $oldQuery->close();

                    if (!$oldRow) {
                        return "Sub office not found";
                    }

                    $stmt = $this->conn->prepare("DELETE FROM tbl_office_under WHERE office_under_id = ?");
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $stmt->bind_param("i", $office_under_id);

                    if ($stmt->execute()) {
                        $stmt->close();

                        if (!empty($operator_ID)) {
                            $batch_id = uniqid("batch_");
                            $changes = [
                                [
                                    'column_name' => 'office_under_name',
                                    'old_value'   => $oldRow['office_under_name'],
                                    'new_value'   => ''
                                ]
                            ];
                            $this->record_activity_logs(
                                $batch_id,
                                'tbl_office_under',
                                $office_under_id,
                                $changes,
                                $operator_ID,
                                $loggedInUserType,
                                "Deleted sub office ID $office_under_id"
                            );
                        }

                        return true;
                    } else {
                        $error = $stmt->error;
                        $stmt->close();
                        return "Failed to delete sub office: " . $error;
                    }
                } catch (Exception $e) {
                    error_log("deleteSubOffice error: " . $e->getMessage());
                    return "Error deleting sub office";
                }
            }

            public function deleteAllSubOffices($office_id, $operator_ID, $loggedInUserType) {
                try {
                    $stmt = $this->conn->prepare("DELETE FROM tbl_office_under WHERE office_id = ?");
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $stmt->bind_param("i", $office_id);

                    if ($stmt->execute()) {
                        $stmt->close();
                        return true;
                    } else {
                        $error = $stmt->error;
                        $stmt->close();
                        return "Failed to delete sub offices: " . $error;
                    }
                } catch (Exception $e) {
                    error_log("deleteAllSubOffices error: " . $e->getMessage());
                    return "Error deleting sub offices";
                }
            }

            public function updateOffice($office_id, $office_type_id, $office_name, $office_email, $operator_ID, $loggedInUserType) {
                try {
                    $oldQuery = $this->conn->prepare("SELECT office_type_id, office_name, office_email FROM tbloffice WHERE office_id = ?");
                    if (!$oldQuery) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $oldQuery->bind_param("i", $office_id);
                    $oldQuery->execute();
                    $oldResult = $oldQuery->get_result();
                    $oldRow = $oldResult ? $oldResult->fetch_assoc() : null;
                    $oldQuery->close();

                    if (!$oldRow) {
                        return "Office not found";
                    }

                    $check = $this->conn->prepare("SELECT office_id FROM tbloffice WHERE office_name = ? AND office_type_id = ? AND office_id != ?");
                    if (!$check) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $check->bind_param("sii", $office_name, $office_type_id, $office_id);
                    $check->execute();
                    $res = $check->get_result();
                    if ($res && $res->num_rows > 0) {
                        $check->close();
                        return "Office already exists for this type";
                    }
                    $check->close();

                    $query = $this->conn->prepare("UPDATE tbloffice SET office_type_id = ?, office_name = ?, office_email = ? WHERE office_id = ?");
                    if (!$query) {
                        throw new Exception("Prepare failed: " . $this->conn->error);
                    }
                    $query->bind_param("issi", $office_type_id, $office_name, $office_email, $office_id);

                    if ($query->execute()) {
                        $query->close();

                        $changes = [];
                        if ($oldRow['office_type_id'] != $office_type_id) {
                            $changes[] = [
                                'column_name' => 'office_type_id',
                                'old_value'   => $oldRow['office_type_id'],
                                'new_value'   => $office_type_id
                            ];
                        }
                        if ($oldRow['office_name'] !== $office_name) {
                            $changes[] = [
                                'column_name' => 'office_name',
                                'old_value'   => $oldRow['office_name'],
                                'new_value'   => $office_name
                            ];
                        }
                        if ($oldRow['office_email'] !== $office_email) {
                            $changes[] = [
                                'column_name' => 'office_email',
                                'old_value'   => $oldRow['office_email'],
                                'new_value'   => $office_email
                            ];
                        }

                        if (!empty($changes)) {
                            $batch_id = uniqid("batch_");
                            $this->record_activity_logs($batch_id, 'tbloffice', $office_id, $changes, $operator_ID, $loggedInUserType, "Updated office ID $office_id");
                        }

                        return true;
                    } else {
                        return "Failed to update office: " . $query->error;
                    }
                } catch (Exception $e) {
                    error_log("updateOffice error: " . $e->getMessage());
                    return "Error updating office";
                }
            }
        // END OFFICE / OFFICE TYPE FUNCTIONS**********************************************************  

        // ITEMS CRUD**********************************************************
        public function getItems() {
            $query = "SELECT item_id, item_name, item_unit_price, stock_qty, low_stock_threshold, item_added_by FROM tblitem ORDER BY item_name ASC";
            return $this->conn->query($query);
        }

        public function createItem($item_name, $item_unit_price, $stock_qty, $low_stock_threshold, $added_by, $operator_ID = null, $loggedInUserType = null) {
        $stmt = $this->conn->prepare("INSERT INTO tblitem (item_name, item_unit_price, stock_qty, low_stock_threshold, item_added_by) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            return "Failed to prepare statement: " . $this->conn->error;
        }
        $stmt->bind_param("sdiii", $item_name, $item_unit_price, $stock_qty, $low_stock_threshold, $added_by);
        if ($stmt->execute()) {
            $item_id = $this->conn->insert_id;
            $stmt->close();
            
            // Log item creation activity
            if ($operator_ID && $loggedInUserType) {
                $batch_id = uniqid("batch_");
                $changes = [
                    [
                        'column_name' => 'item_name',
                        'old_value' => '',
                        'new_value' => $item_name
                    ],
                    [
                        'column_name' => 'item_unit_price',
                        'old_value' => '',
                        'new_value' => $item_unit_price
                    ],
                    [
                        'column_name' => 'stock_qty',
                        'old_value' => '',
                        'new_value' => $stock_qty
                    ],
                    [
                        'column_name' => 'low_stock_threshold',
                        'old_value' => '',
                        'new_value' => $low_stock_threshold
                    ]
                ];
                $this->record_activity_logs($batch_id, 'tblitem', $item_id, $changes, $operator_ID, $loggedInUserType, "Created new item: {$item_name}");
            }
            
            return true;
        }
        $error = $stmt->error;
        $stmt->close();
        return "Failed to create item: " . $error;
    }

        public function updateItem($item_id, $item_name, $item_unit_price, $stock_qty, $low_stock_threshold, $operator_ID = null, $loggedInUserType = null) {
            // Get old values for logging
            $oldItem = $this->getItemById($item_id);
            if (!$oldItem) {
                return "Item not found";
            }

            $stmt = $this->conn->prepare("UPDATE tblitem SET item_name = ?, item_unit_price = ?, stock_qty = ?, low_stock_threshold = ? WHERE item_id = ?");
            if (!$stmt) {
                return "Failed to prepare statement: " . $this->conn->error;
            }
            $stmt->bind_param("sdiii", $item_name, $item_unit_price, $stock_qty, $low_stock_threshold, $item_id);
            if ($stmt->execute()) {
                $stmt->close();

                // Log item update activity
                if ($operator_ID && $loggedInUserType) {
                    $changes = [];
                    if ($oldItem['item_name'] !== $item_name) {
                        $changes[] = [
                            'column_name' => 'item_name',
                            'old_value' => $oldItem['item_name'],
                            'new_value' => $item_name
                        ];
                    }
                    if ($oldItem['item_unit_price'] != $item_unit_price) {
                        $changes[] = [
                            'column_name' => 'item_unit_price',
                            'old_value' => $oldItem['item_unit_price'],
                            'new_value' => $item_unit_price
                        ];
                    }
                    if ($oldItem['stock_qty'] != $stock_qty) {
                        $changes[] = [
                            'column_name' => 'stock_qty',
                            'old_value' => $oldItem['stock_qty'],
                            'new_value' => $stock_qty
                        ];
                    }
                    if ($oldItem['low_stock_threshold'] != $low_stock_threshold) {
                        $changes[] = [
                            'column_name' => 'low_stock_threshold',
                            'old_value' => $oldItem['low_stock_threshold'],
                            'new_value' => $low_stock_threshold
                        ];
                    }

                    if (!empty($changes)) {
                        $batch_id = uniqid("batch_");
                        $this->record_activity_logs($batch_id, 'tblitem', $item_id, $changes, $operator_ID, $loggedInUserType, "Updated item: {$item_name}");
                    }
                }

                return true;
            }
            $error = $stmt->error;
            $stmt->close();
            return "Failed to update item: " . $error;
        }

        public function deleteItems($ids, $operator_ID = null, $loggedInUserType = null) {
            if (!empty($ids)) {
                // Get item details before deletion for logging
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $selectStmt = $this->conn->prepare("SELECT item_id, item_name FROM tblitem WHERE item_id IN ($placeholders)");
                $selectStmt->bind_param(str_repeat('i', count($ids)), ...$ids);
                $selectStmt->execute();
                $result = $selectStmt->get_result();
                $items = [];
                while ($row = $result->fetch_assoc()) {
                    $items[$row['item_id']] = $row['item_name'];
                }
                $selectStmt->close();

                // Delete items
                $stmt = $this->conn->prepare("DELETE FROM tblitem WHERE item_id IN ($placeholders)");
                $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
                $success = $stmt->execute();
                $stmt->close();

                // Log deletion activity
                if ($success && $operator_ID && $loggedInUserType) {
                    foreach ($ids as $item_id) {
                        if (isset($items[$item_id])) {
                            $batch_id = uniqid("batch_");
                            $changes = [
                                [
                                    'column_name' => 'item_name',
                                    'old_value' => $items[$item_id],
                                    'new_value' => 'DELETED'
                                ]
                            ];
                            $this->record_activity_logs($batch_id, 'tblitem', $item_id, $changes, $operator_ID, $loggedInUserType, "Deleted item: {$items[$item_id]}");
                        }
                    }
                }

                return $success;
            }
            return false;
        }

        public function getItemById($item_id) {
            $stmt = $this->conn->prepare("SELECT item_id, item_name, item_unit_price, stock_qty, low_stock_threshold FROM tblitem WHERE item_id = ? LIMIT 1");
            if (!$stmt) {
                return null;
            }
            $stmt->bind_param('i', $item_id);
            if (!$stmt->execute()) {
                $stmt->close();
                return null;
            }
            $result = $stmt->get_result();
            $row = $result ? $result->fetch_assoc() : null;
            $stmt->close();
            return $row;
        }

        public function adjustItemStock($item_id, $change_qty, $created_by) {
            $this->conn->begin_transaction();
            try {
                $item = $this->getItemById($item_id);
                if (!$item) {
                    $this->conn->rollback();
                    return 'Item not found';
                }

                $previous_stock = (int)$item['stock_qty'];
                $change = (int)$change_qty;
                $new_stock = $previous_stock + $change;

                if ($new_stock < 0) {
                    $this->conn->rollback();
                    return 'Resulting stock cannot be negative';
                }

                $updateStmt = $this->conn->prepare("UPDATE tblitem SET stock_qty = ? WHERE item_id = ?");
                if (!$updateStmt) {
                    $this->conn->rollback();
                    return 'Failed to prepare stock update statement';
                }
                $updateStmt->bind_param('ii', $new_stock, $item_id);
                if (!$updateStmt->execute()) {
                    $error = $updateStmt->error;
                    $updateStmt->close();
                    $this->conn->rollback();
                    return 'Failed to update stock: ' . $error;
                }
                $updateStmt->close();

                $logStmt = $this->conn->prepare("INSERT INTO tblstock_adjustment (item_id, change_qty, previous_stock, new_stock, created_by) VALUES (?, ?, ?, ?, ?)");
                if (!$logStmt) {
                    $this->conn->rollback();
                    return 'Failed to prepare adjustment log statement';
                }
                $logStmt->bind_param('iiiii', $item_id, $change, $previous_stock, $new_stock, $created_by);
                if (!$logStmt->execute()) {
                    $error = $logStmt->error;
                    $logStmt->close();
                    $this->conn->rollback();
                    return 'Failed to log stock adjustment: ' . $error;
                }
                $logStmt->close();

                $this->conn->commit();
                return true;
            } catch (Exception $e) {
                $this->conn->rollback();
                error_log('adjustItemStock error: ' . $e->getMessage());
                return 'Error adjusting stock';
            }
        }
        
        public function getStockAdjustments() {
            $query = $this->conn->prepare("
                SELECT sa.adjustment_id, sa.item_id, sa.change_qty, sa.previous_stock, sa.new_stock, 
                       sa.created_by, sa.created_at, i.item_name, u.firstname, u.lastname
                FROM tblstock_adjustment sa
                LEFT JOIN tblitem i ON sa.item_id = i.item_id
                LEFT JOIN tbluser u ON sa.created_by = u.user_id
                ORDER BY sa.created_at DESC
            ");
            
            if ($query->execute()) {
                return $query->get_result();
            }
            return false;
        }
        
        // FUNCTION SYSTEM SETTINGS**********************************************************
            public function getActiveSystemSettings() {
                $query=$this->conn->prepare("SELECT * FROM system_settings WHERE status=1 LIMIT 1") or die($this->conn->error);
                if($query->execute()){
                    $result=$query->get_result();
                    return $result;  
                }
            }
            public function getSystemSettings($Id = "") {
                if(!empty($Id)) {
                    $query=$this->conn->prepare("SELECT * FROM system_settings WHERE Id = ?") or die($this->conn->error);
                    $query->bind_param("i", $Id);
                } else {
                    $query=$this->conn->prepare("SELECT * FROM system_settings") or die($this->conn->error);
                }
                if($query->execute()){
                    $result=$query->get_result();
                    return $result;  
                }
            }
            
            public function UpdateSystemSettings($Id, $system_name, $about_us, $address, $email, $contact, $logo, $gallery, $operator_ID, $loggedInUserType) {
                $query = $this->conn->prepare("UPDATE system_settings SET system_name = ?, about_us = ?, address = ?, email = ?, contact = ?, logo = ?, gallery = ? WHERE Id = ?");
                $query->bind_param('sssssssi', $system_name, $about_us, $address, $email, $contact, $logo, $gallery, $Id);
                if ($query->execute()) {
                    // $this->recordAuditTrail($operator_ID, $loggedInUserType, "Update system setting", "Updated system setting, ".$system_name." with ID: " . $Id);
                    return true;
                } else {
                    return $query->error;
                }
            }
        // END FUNCTION SYSTEM SETTINGS**********************************************************

        // FUNCTION TO DELETE RECORDS**********************************************************
            public function DeleteRecords($table, $delete_column, $delete_IDs, $operator_ID, $loggedInUserType, $audit_ID = "") {
                $placeholders = implode(',', array_fill(0, count($delete_IDs), '?'));
                $query = $this->conn->prepare("DELETE FROM $table WHERE $delete_column IN ($placeholders)");

                $types = str_repeat('i', count($delete_IDs));
                $query->bind_param($types, ...$delete_IDs);

                if ($query->execute()) {
                    $query->close();

                    foreach ($delete_IDs as $id) {
                        $batch_id = uniqid('batch_');
                        $changes = [[
                            'column_name' => 'is_deleted',
                            'old_value' => '0',
                            'new_value' => 'deleted'
                        ]];
                        $this->record_activity_logs($batch_id, $table, $id, $changes, $operator_ID, $loggedInUserType, "Deleted record ID: $id");
                    }

                    return true;
                } else {
                    error_log("Error deleting records: " . $query->error);
                    return false;
                }
            }
            public function DeleteRecordForm($table, $delete_column, $delete_IDs, $operator_ID, $loggedInUserType, $audit_ID = "") {
                if (is_array($delete_IDs)) {
                    $placeholders = implode(',', array_fill(0, count($delete_IDs), '?'));
                    $query = $this->conn->prepare("DELETE FROM $table WHERE $delete_column IN ($placeholders)");
                    $types = str_repeat('i', count($delete_IDs));
                    $query->bind_param($types, ...$delete_IDs);
                } else {
                    $query = $this->conn->prepare("DELETE FROM $table WHERE $delete_column = ?");
                    $query->bind_param("i", $delete_IDs);
                }

                $this->conn->begin_transaction();

                try {
                    if (!$query->execute()) {
                        throw new Exception("Error executing query: " . $query->error);
                    }

                    $this->conn->commit();

                    $ids = is_array($delete_IDs) ? $delete_IDs : [$delete_IDs];
                    foreach ($ids as $id) {
                        $batch_id = uniqid("batch_");
                        $changes = [[
                            'column_name' => 'is_deleted',
                            'old_value' => '0',
                            'new_value' => 'deleted'
                        ]];
                        $this->record_activity_logs($batch_id, $table, $id, $changes, $operator_ID, $loggedInUserType, "Deleted record ID: $id");
                    }

                    $query->close();
                    return true;
                } catch (Exception $e) {
                    $this->conn->rollback();
                    error_log("Error deleting records: " . $e->getMessage());
                    return false;
                }
            }
            public function ArchiveRecords($table, $delete_column, $delete_IDs, $operator_ID, $loggedInUserType, $audit_ID = "") {
                $placeholders = implode(',', array_fill(0, count($delete_IDs), '?'));
                $query = $this->conn->prepare("UPDATE $table SET is_deleted = 1 WHERE $delete_column IN ($placeholders)");

                $types = str_repeat('i', count($delete_IDs));
                $query->bind_param($types, ...$delete_IDs);

                if ($query->execute()) {
                    $query->close();

                    foreach ($delete_IDs as $id) {
                        $batch_id = uniqid("batch_");
                        $changes = [[
                            'column_name' => 'is_deleted',
                            'old_value' => '0',
                            'new_value' => '1'
                        ]];
                        $this->record_activity_logs($batch_id, $table, $id, $changes, $operator_ID, $loggedInUserType, "Archived record ID: $id");
                    }

                    return true;
                } else {
                    error_log("Error updating records: " . $query->error);
                    return false;
                }
            }
            public function ArchiveRecordForm($table, $delete_column, $delete_IDs, $operator_ID, $loggedInUserType, $audit_ID = "") {
                $this->conn->begin_transaction();

                try {
                    if (is_array($delete_IDs)) {
                        $placeholders = implode(',', array_fill(0, count($delete_IDs), '?'));
                        $query = $this->conn->prepare("UPDATE $table SET is_deleted = 1 WHERE $delete_column IN ($placeholders)");
                        $types = str_repeat('i', count($delete_IDs));
                        $query->bind_param($types, ...$delete_IDs);
                    } else {
                        $query = $this->conn->prepare("UPDATE $table SET is_deleted = 1 WHERE $delete_column = ?");
                        $query->bind_param("i", $delete_IDs);
                    }

                    if (!$query->execute()) {
                        throw new Exception("Error executing query: " . $query->error);
                    }

                    $this->conn->commit();

                    $audit_message = is_array($delete_IDs) 
                        ? "Archived records in table: $table (set is_deleted = 1) for $delete_column IN (" . implode(',', $delete_IDs) . ")"
                        : "Archived record in table: $table (set is_deleted = 1) for $delete_column = $delete_IDs";

                    // $this->recordAuditTrail($operator_ID, $loggedInUserType, "Archive Record", $audit_message);

                    $query->close();
                    return true;
                } catch (Exception $e) {
                    $this->conn->rollback();
                    error_log("ArchiveRecordForm error: " . $e->getMessage());
                    return false;
                }
            }
            public function RestoreRecords($table, $delete_column, $delete_IDs, $operator_ID, $loggedInUserType, $audit_ID = "") {
                $placeholders = implode(',', array_fill(0, count($delete_IDs), '?'));
                $query = $this->conn->prepare("UPDATE $table SET is_deleted = 0 WHERE $delete_column IN ($placeholders)");

                $types = str_repeat('i', count($delete_IDs));
                $query->bind_param($types, ...$delete_IDs);

                if ($query->execute()) {
                    $query->close();
                    // $this->recordAuditTrail($operator_ID, $loggedInUserType, "Restore Records", "Restored records in $table where $delete_column IN (" . implode(',', $delete_IDs) . ")");
                    return true;
                } else {
                    error_log("RestoreRecords error: " . $query->error);
                    return false;
                }
            }
            public function RestoreRecordForm($table, $delete_column, $delete_IDs, $operator_ID, $loggedInUserType, $audit_ID = "") {
                $this->conn->begin_transaction();

                try {
                    if (is_array($delete_IDs)) {
                        $placeholders = implode(',', array_fill(0, count($delete_IDs), '?'));
                        $query = $this->conn->prepare("UPDATE $table SET is_deleted = 0 WHERE $delete_column IN ($placeholders)");
                        $types = str_repeat('i', count($delete_IDs));
                        $query->bind_param($types, ...$delete_IDs);
                    } else {
                        $query = $this->conn->prepare("UPDATE $table SET is_deleted = 0 WHERE $delete_column = ?");
                        $query->bind_param("i", $delete_IDs);
                    }

                    if (!$query->execute()) {
                        throw new Exception("Error executing query: " . $query->error);
                    }

                    $this->conn->commit();

                    $audit_message = is_array($delete_IDs) 
                        ? "Restored records in table: $table (set is_deleted = 0) for $delete_column IN (" . implode(',', $delete_IDs) . ")"
                        : "Restored record in table: $table (set is_deleted = 0) for $delete_column = $delete_IDs";

                    // $this->recordAuditTrail($operator_ID, $loggedInUserType, "Restore Record", $audit_message);

                    $query->close();
                    return true;
                } catch (Exception $e) {
                    $this->conn->rollback();
                    error_log("RestoreRecordForm error: " . $e->getMessage());
                    return false;
                }
            }
        // END FUNCTION TO DELETE RECORDS**********************************************************

        // ACTIVITY LOGS FUNCTION**********************************************************
            public function record_activity_logs($batch_ID, $table, $record_ID, $changes, $operator_ID, $user_type, $description) {
                $query = $this->conn->prepare("INSERT INTO activity_logs (batch_ID, table_name, record_ID, column_name, old_value, new_value, changed_by, user_type, action_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

                foreach ($changes as $change) {
                    $query->bind_param("ssisssiss", $batch_ID, $table, $record_ID, $change['column_name'], $change['old_value'], $change['new_value'], $operator_ID, $user_type, $description);
                    $query->execute();
                }

                $query->close();
            }
        // END ACTIVITY LOGS FUNCTION**********************************************************
            
        // LOGIN HISTORY FUNCTION**********************************************************
            public function getLoginHistory($id, $user_type) {
                $sql = "
                    SELECT lh.*, 
                        u.firstname, u.lastname, u.middlename, u.suffix
                    FROM log_history lh
                    LEFT JOIN tbluser u ON lh.user_ID = u.user_id
                    WHERE lh.usertype IS NULL
                ";

                if ($user_type === "Administrator") {
                    $sql .= " ORDER BY lh.login_datetime DESC";
                    return $this->conn->query($sql);
                } else {
                    $sql .= " AND lh.user_ID = ? ORDER BY lh.login_datetime DESC";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    return $stmt->get_result();
                }
            }
        // END LOGIN HISTORY FUNCTION**********************************************************
        
        // CONTACT EMAIL MESSAGING**********************************************************
        private function getEmailTemplate($bodyContent, $system_name) {
            return '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Email Notification</title>
                <style>
                    body {
                        font-family: \'Arial\', sans-serif;
                        background-color: #f4f5f6;
                        color: #333333;
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    .container {
                        max-width: 600px;
                        margin: 30px auto;
                        background-color: #ffffff;
                        border: 3px solid #0C1148;
                        border-radius: 12px;
                        overflow: hidden;
                        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
                    }
                    .header {
                        background-color: #0C1148;
                        color: #ffffff;
                        text-align: center;
                        padding: 25px 20px;
                        font-size: 28px;
                        font-weight: bold;
                        letter-spacing: 1px;
                    }
                    .content {
                        padding: 30px 25px;
                        background-color: #ffffff;
                        color: #333333;
                        line-height: 1.8;
                    }
                    .highlight {
                        color: #CB3635;
                        font-weight: bold;
                    }
                    .footer {
                        background-color: #0C1148;
                        color: #ffffff;
                        text-align: center;
                        padding: 15px;
                        font-size: 14px;
                        border-top: 3px solid #CB3635;
                    }
                    .footer a {
                        color: #CB3635;
                        text-decoration: none;
                        font-weight: bold;
                    }
                    .footer a:hover {
                        text-decoration: underline;
                    }
                    @media (max-width: 600px) {
                        .container {
                            width: 100%;
                            margin: 10px;
                        }
                        .header {
                            font-size: 24px;
                            padding: 20px;
                        }
                        .content {
                            padding: 20px;
                            font-size: 15px;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">'.$system_name.'</div>
                    <div class="content">
                        ' . $bodyContent . '
                        <p class="highlight">If you have any questions, feel free to contact us.</p>
                    </div>
                    <div class="footer">
                        <p><strong>NOTE:</strong> This is a system-generated email. Please do not reply directly.</p>
                        <p>Need help? <a href="mailto:support@yourdomain.com">Contact Support</a></p>
                    </div>
                </div>
            </body>
            </html>';
        }

        public function sendEmail($subject, $messageContent, $recipientEmail) {

            $system_settings = $this->getActiveSystemSettings();
            $system_info = null;

            if($system_settings->num_rows === 0) {
                $system_info = [
                    'system_name' => 'Default System Name',
                    'address' => 'Default Address',
                    'contact' => '09123456789',
                    'email' => 'mail@gmail.com',
                    'about_us' => 'Sample description',
                    'logo' => 'avatar.png'
                ];
            } else {
                $system_info = $system_settings->fetch_assoc();
            }

            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'thesisprojects2025@gmail.com';
                $mail->Password = 'gpsjdlzwfkgqbqay';
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                // Recipients
                $mail->setFrom('thesisprojects2025@gmail.com', $system_info['system_name']);
                $mail->addAddress($recipientEmail);
                $mail->addReplyTo('thesisprojects2025@gmail.com');

                // Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $this->getEmailTemplate($messageContent, $system_info['system_name']);

                $mail->send();
            } catch (Exception $e) {
                $mail->ErrorInfo;
            }
        }

        // CONTACT EMAIL MESSAGING
        public function contact_form($name, $email, $subject, $sent_message) {

            $query = $this->conn->prepare("INSERT INTO public_concerns (name, email, subject, message) VALUES (?, ?, ?, ?)") or die($this->conn->error);
            $query->bind_param("ssss", $name, $email, $subject, $sent_message);

            if ($query->execute()) {
                // Build email body
                $bodyContent = "
                    <p style='color: #666; margin-bottom: 15px;'>Good day,</p>
                    <p style='color: #666; margin-bottom: 15px;'>" . htmlentities(ucwords($sent_message)) . "</p>
                    <p>
                        <strong>Sender name:</strong> " . htmlentities(ucwords($name)) . "<br>
                        <strong>Email:</strong> " . htmlentities($email) . "
                    </p>
                ";

                // Send the email
                $this->sendEmail($subject, $bodyContent, "sonerwin12@gmail.com");
                return true;
            }

            return false;
        }
        
        public function ReplyPublicConcern($reply_message, $msg_ID, $email) {
            // Get concern and service details
            $concernQuery = $this->conn->prepare("
                SELECT pc.name, pc.email, pc.subject, pc.message AS original_message, 
                       pc.offer_ID, so.service_name, so.description, so.requirements
                FROM public_concerns AS pc
                LEFT JOIN services_offered AS so ON pc.offer_ID = so.offer_ID
                WHERE pc.msg_ID = ?");
            $concernQuery->bind_param("i", $msg_ID);
            $concernQuery->execute();
            $concernResult = $concernQuery->get_result();

            if ($concernResult && $concernResult->num_rows > 0) {
                $concern = $concernResult->fetch_assoc();

                // Build Service Details
                $serviceDetails = '';
                if (!empty($concern['service_name'])) {
                    $serviceDetails = "
                        <hr>
                        <h4>Service Details:</h4>
                        <p><strong>Service Name:</strong> " . htmlentities($concern['service_name']) . "</p>
                        <p><strong>Description:</strong> " . htmlentities($concern['description']) . "</p>
                        <p><strong>Requirements:</strong> " . htmlentities($concern['requirements']) . "</p>
                    ";
                }

                // Build the Email Body
                $bodyContent = "
                    <p style='color: #666; margin-bottom: 15px;'>Good day, " . htmlentities(ucwords($concern['name'])) . ",</p>
                    <p style='color: #666; margin-bottom: 15px;'>
                        This is a reply to your previous message about <strong>\"" . htmlentities($concern['subject']) . "\"</strong>:
                    </p>
                    <blockquote style='color: #555; font-style: italic; border-left: 3px solid #ccc; padding-left: 10px;'>" . htmlentities($concern['original_message']) . "</blockquote>
                    <p style='color: #666; margin-bottom: 15px;'><strong>Reply:</strong> " . htmlentities($reply_message) . "</p>
                    $serviceDetails
                ";

                // Send the reply email
                $this->sendEmail($concern['subject'], $bodyContent, $concern['email']);
                $concernQuery->close();

                return true;

            } else {
                return false;
            }
        }

        public function getPublicConcerns($concern_ID = "") {
            $sql = "SELECT * FROM public_concerns";

            if (!empty($concern_ID)) {
                $sql .= " WHERE msg_ID = ?";
                $sql .= " ORDER BY created_at DESC";
                $query = $this->conn->prepare($sql) or die($this->conn->error);
                $query->bind_param("i", $concern_ID);
            } else {
                $sql .= " ORDER BY created_at DESC";
                $query = $this->conn->prepare($sql) or die($this->conn->error);
            }

            if ($query->execute()) {
                $result = $query->get_result();
                return $result;
            } else {
                return null;
            }
        }
        // END CONTACT EMAIL MESSAGING**********************************************************

        // ORDER REMINDER FUNCTIONS**********************************************************
        public function getOrderReminders($orderId) {
            $sql = "SELECT * FROM order_reminders WHERE order_id = ? ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function getPendingReminders() {
            $sql = "SELECT * FROM order_reminders WHERE is_acknowledged = 0 ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function acknowledgeReminder($reminderId, $staffId) {
            $sql = "UPDATE order_reminders SET is_acknowledged = 1, acknowledged_by = ?, acknowledged_at = NOW() WHERE reminder_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('ii', $staffId, $reminderId);
            return $stmt->execute();
        }
        // END ORDER REMINDER FUNCTIONS**********************************************************

        

    }

    