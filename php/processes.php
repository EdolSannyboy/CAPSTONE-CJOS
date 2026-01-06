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

// SESSION
$operator_ID = $_SESSION['user_ID'] ?? null;
$loggedInUserType = $_SESSION['user_type'] ?? null;
switch ($loggedInUserType) {
    case 'Administrator':
        $operator_ID = $_SESSION['user_ID'] ?? null;
        $new_usertype = 'administrator';
        break;

    case 'Canteen Staff':
        $operator_ID = $_SESSION['user_ID'] ?? null;
        $new_usertype = 'canteen_staff';
        break;

    case 'Canteen Manager':
        $operator_ID = $_SESSION['user_ID'] ?? null;
        $new_usertype = 'canteen_manager';
        break;

    default:
        $operator_ID = null;
        $new_usertype = 'member';
        break;
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $db = new db_class();
    $response = ['success' => false];

    if (!isset($_POST['action']) || !is_string($_POST['action'])) {
        echo json_encode(['error' => 'Missing or invalid action']);
        exit;
    }

    $action = $_POST['action'];

    // Centralize login handling: delegate to authenticate.php
    if ($action === 'login') {
        require 'authenticate.php';
        exit;
    }

    // HANDLE CHECK EXISTING CONTACT ACTION - REGISTRATION.PHP/PROFILE.PHP
    if ($action === "checkExistingContact") {
        $user_ID = isset($_POST['user_ID']) ? $_POST['user_ID'] : '';
        $contact = $_POST['contact'];

        $result = $db->checkExistingContact($contact, $user_ID);
        echo json_encode($result);
    }

    // HANDLE CHECK EXISTING EMAIL ACTION FOR FORGOT PASSWORD - FORGOTPASSWORD.PHP
    if ($action === "checkEmail") {
        $email = $_POST['email'];
        $type = $_POST['type'];

        $result = $db->checkEmail($email, $type);
        if ($result['exists']) {
            echo json_encode(['exists' => true, 'id' => $result['id'], 'user_type' => $result['user_type']]);
        } else {
            echo json_encode(['exists' => false]);
        }
    }

    // HANDLE CHECK EXISTING ID NUMBER ACTION - SENDCODE.PHP
    if ($action === "sendCode") {
        $email = $_POST['email'];
        $id    = $_POST['user_ID'];
        $type    = $_POST['type'];

        $result = $db->sendCode($email, $id, $type);
        if ($result) {
            $response['success'] = true;
            $response['message'] = "A verification code has been sent to your email";
            $response['redirect'] = "verify-code.php?email=".$email."&&id=".$id."&&type=".$type." ";
        } else {
            $response['success'] = false;
            $response['message'] = "Failed to send verification code";
        }
        echo json_encode($response);
    }

    // HANDLE VERIFICATION CODE ACTION - VERIFYCODE.PHP
    if ($action === "verifyCode") {
        $email = $_POST['email'];
        $id    = $_POST['user_ID'];
        $code  = $_POST['code'];
        $type    = $_POST['type'];

        $result = $db->verifyCode($email, $id, $code, $type);
        if ($result) {
            $response['success'] = true;
            $response['redirect'] = "change-password.php?email=".$email."&&id=".$id."&&type=".$type."&action=ChangePass";
        } else {
            $response['success'] = false;
            $response['message'] = "Invalid code";
        }
        echo json_encode($response);
    }

    // HANDLE CHANGE PASSWORD ACTION - CHANGEPASSWORD.PHP
    if ($action === "changePassword") {
        $email     = $_POST['email'];
        $id        = $_POST['user_ID'];
        $type      = $_POST['type'];  // Check for Administrator or User
        $action_type = $_POST['action_type'];
        $password  = $_POST['password'];
        $cpassword = $_POST['cpassword'];

        $result = $db->changePassword($email, $id, $type, $action_type, $password, $cpassword);
        
        if (is_string($result)) {
            $response['success'] = false;
            $response['message'] = $result;
        } else if ($result) {
            $response['success'] = true;
            $response['message'] = "Password has been successfully changed. Please login.";

            // Get user details from tbluser to determine redirect
            $userQuery = $db->getConnection()->prepare("SELECT userlevel_id FROM tbluser WHERE user_id = ? AND email = ?");
            $userQuery->bind_param("is", $id, $email);
            $userQuery->execute();
            $userResult = $userQuery->get_result();
            
            if ($userResult->num_rows > 0) {
                $user = $userResult->fetch_assoc();
                $userlevel_id = $user['userlevel_id'];
                
                // Determine redirect based on user level
                if ($userlevel_id == 1) { // Administrator
                    $response['redirect'] = "login.php";
                } else { // Canteen Staff (2) or Canteen Manager (3)
                    $response['redirect'] = "login.php";
                }
            } else {
                $response['redirect'] = "login.php"; // fallback
            }
            $userQuery->close();
        } else {
            $response['success'] = false;
            $response['message'] = "Error updating new password";
        }
        
        echo json_encode($response);
    }

    // HANDLE VERIFICATION OF ACCOUNT ACTION FROM EMAIL - ACCOUNT-VERIFICATION.PHP
    if ($action === "verifyAccountFromEmail") { 
        $user_ID = $_POST['user_ID'];
        $email = $_POST['email'];
        $type = $_POST['type'];

        $result = $db->verifyUserAccount($user_ID, $email, $type);
        if ($result) {
            $response['success'] = true;
            $response['message'] = "Account successfully verified.";

            // Get user details from tbluser to determine redirect
            $userQuery = $db->getConnection()->prepare("SELECT is_password_changed, userlevel_id FROM tbluser WHERE user_id = ? AND email = ?");
            $userQuery->bind_param("is", $user_ID, $email);
            $userQuery->execute();
            $userResult = $userQuery->get_result();
            
            if ($userResult->num_rows > 0) {
                $user = $userResult->fetch_assoc();
                $userlevel_id = $user['userlevel_id'];
                $is_password_changed = $user['is_password_changed'];
                
                // Determine redirect based on user level
                if ($userlevel_id == 1) { // Administrator
                    $loginPage = "login-admin.php";
                } else { // Canteen Staff (2) or Canteen Manager (3)
                    $loginPage = "login.php";
                }
                
                if ($is_password_changed == 1) {
                    $response['redirect'] = $loginPage;
                } else {
                    $response['redirect'] = "change-password.php?email={$email}&id={$user_ID}&type={$type}&action=FirstChangePass";
                }
            } else {
                $response['success'] = false;
                $response['message'] = "Error fetching user details.";
            }
            $userQuery->close();

        } else {
            $response['success'] = false;
            $response['message'] = "Error verifying account.";
        }

        echo json_encode($response);
    }

    if ($action === "logout_user") {
        $user_ID = $_POST['user_ID'];
        $login_time = $_POST['login_time'];
        $u_type = $_POST['u_type'];
        $uin = $_POST['uin'] ?? null;

        $result = $db->logout_user($user_ID, $login_time, $u_type, $uin);

        if (is_string($result)) {
            $response['success'] = false;
            $response['message'] = $result;
        } else {
            $response['success'] = true;
            $response['message'] = "Logout successful!";
        }
        echo json_encode($response);
    }

    // HANDLE UPDATE PROFILE PICTURE - PROFILE.PHP
    if ($action === 'updateProfilePicture') {

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $response['message'] = "No file uploaded or file upload error.";
            echo json_encode($response);
            exit;
        }

        $user_ID = $_POST['user_ID'];
        $type = $_POST['type'];
        $image = $_FILES['image'];

        $file_extension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($image['type'], $allowed_types) || !in_array($file_extension, $allowed_extensions)) {
            $response['message'] = "Invalid file type or extension.";
            echo json_encode($response);
            exit;
        }

        if ($image['size'] > 500000) {
            $response['message'] = "File size exceeds 500 KB.";
            echo json_encode($response);
            exit;
        }

        switch ($type) {
            case "Administrator":
                $user = $db->get_Administrator_By_ID($user_ID);
                $folder = 'admin';
                $id_field = 'admin_ID';
                break;
            case "Canteen Staff":
                $user = $db->get_CanteenStaff_By_ID($user_ID);
                $folder = 'canteen-staff';
                $id_field = 'canteen_staff_ID';
                break;
            default:
                echo json_encode(['message' => "Invalid user type."]);
                exit;
        }

        if (!$user) {
            echo json_encode(['message' => "$type not found."]);
            exit;
        }

        $firstname = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $user['firstname']));
        $lastname = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $user['lastname']));
        $uniqueID = uniqid();
        $new_filename = "{$folder}_{$user_ID}_{$firstname}_{$lastname}_{$uniqueID}.{$file_extension}";
        $destination = "../assets/img/{$folder}/" . $new_filename;

        if (move_uploaded_file($image['tmp_name'], $destination)) {

            if (!empty($user['profile']) && $user['profile'] !== 'avatar.png') {
                $oldPath = "../assets/img/{$folder}/" . $user['profile'];
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $result = $db->updateProfilePicture($user_ID, $type, $new_filename, $operator_ID, $loggedInUserType);
            if ($result === true) {
                $response['success'] = true;
                $response['message'] = "Profile has been updated!";
            } else {
                $response['message'] = "Unknown error during DB update.";
            }
        } else {
            $response['message'] = "Failed to move uploaded file.";
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // HANDLE CHANGE PASSWORD - PROFILE.PHP
    if ($action === 'updatePassword') {
        $user_ID = $_POST['user_ID'];
        $type = $_POST['type'];
        $OldPassword = $_POST['OldPassword'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];

        $result = $db->updatePassword($user_ID, $type, $OldPassword, $password, $cpassword, $operator_ID, $loggedInUserType);
        if (is_string($result)) {
            $response['success'] = false;
            $response['message'] = $result;
        } else if ($result === true) {
            $response['success'] = true;
            $response['message'] = "Password successfully changed";
        } else {
            $response['success'] = false;
            $response['message'] = "Error changing password";
        }
        echo json_encode($response);
    }

    // PROCESS ADMINISTRATOR **********************************************************
    if ($action === "AddUserForm") {

        $user_type          = $_POST['user_type'];
        $firstname          = $_POST['firstname'];
        $middlename      = $_POST['middlename'];
        $lastname           = $_POST['lastname'];
        $suffix                = $_POST['suffix'];
        $gender              = $_POST['gender'];
        $birthdate           = $_POST['birthdate'];
        $nationality = $_POST['nationality'];
        $contact              = $_POST['contact'];
        $email                 = $_POST['email'];

        // Initialize filenames
        // $profile_filename = "avatar.png";

        // $allowed_types = [
        //     'image/jpeg',
        //     'image/png',
        //     'image/gif',
        //     'application/pdf',
        //     'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        // ];
        // $max_file_size = 2000000; // 2MB

        // if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        //     $profile = $_FILES['image'];
        //     $timestamp = time();
        //     $extension = pathinfo($profile['name'], PATHINFO_EXTENSION);
        //     $profile_filename = $timestamp . '.' . $extension;

        //     // Validate
        //     if (!in_array($profile['type'], $allowed_types)) {
        //         $response['message'] = "Only JPG, JPEG, PNG, GIF, PDF, and DOCX files are allowed for supporting documents.";
        //         echo json_encode($response);
        //         exit;
        //     }

        //     if ($profile['size'] > $max_file_size) {
        //         $response['message'] = "Supporting documents file size exceeds the limit (2 MB).";
        //         echo json_encode($response);
        //         exit;
        //     }

        //     $destination = '../assets/img/user-images/' . $profile_filename;
        //     if (!move_uploaded_file($profile['tmp_name'], $destination)) {
        //         $response['message'] = "Failed to upload the supporting documents.";
        //         echo json_encode($response);
        //         exit;
        //     }
        // }

        $result = $db->CreateUser($firstname, $middlename, $lastname, $suffix, $gender, $birthdate, $nationality, $contact, $email, $user_type, $operator_ID, $loggedInUserType);
        if (is_string($result)) {
            $response['success'] = false;
            $response['message'] = $result;
        } else if ($result) {
            $response['success'] = true;
            $response['message'] = "Adding new ".$user_type." successful!";
        } else {
            $response['success'] = false;
            $response['message'] = "Adding new ".$user_type." failed!";
        }
        echo json_encode($response);
    }

    if ($action === "UpdateUserForm") {

        $user_ID           = $_POST['user_ID'];
        $user_type         = $_POST['user_type'];
        $firstname         = $_POST['firstname'];
        $middlename        = $_POST['middlename'];
        $lastname          = $_POST['lastname'];
        $suffix            = $_POST['suffix'];
        $gender            = $_POST['gender'];
        $birthdate         = $_POST['birthdate'];
        $nationality       = $_POST['nationality'];
        $contact           = $_POST['contact'];
        $email             = $_POST['email'];
        $password          = $_POST['password'] ?? '';
        $cpassword         = $_POST['cpassword'] ?? '';

        $result = $db->UpdateUser($user_ID, $firstname, $middlename, $lastname, $suffix, $gender, $birthdate, $nationality, $contact, $email, $user_type, $operator_ID, $loggedInUserType, $password, $cpassword);

        if (is_string($result)) {
            $response['success'] = false;
            $response['message'] = $result;
        } else if ($result === true) {
            $response['success'] = true;
            $response['message'] = "".$user_type." record has been updated!";
        } else {
            $response['success'] = false;
            $response['message'] = $result;
        }
    }

    if ($action === "createOrderForm") {

        $officeIdRaw        = $_POST['office_id'] ?? '';
        $officeUnderIdRaw   = $_POST['office_under_id'] ?? '';
        $otherOfficeName    = trim($_POST['other_office_name'] ?? '');
        $officeEmail        = trim($_POST['office_email'] ?? '');
        $event              = trim($_POST['event'] ?? '');
        $jobRequesterName   = trim($_POST['job_requester_name'] ?? '');
        $neededDatetimesRaw = $_POST['needed_datetime'] ?? [];

        // Items posted from the various create order forms
        $postedItemIds = $_POST['items_id'] ?? $_POST['description'] ?? [];
        $postedPrices  = $_POST['price'] ?? [];
        $postedQty     = $_POST['quantity'] ?? [];
        $postedRemarks = $_POST['remarks'] ?? [];

        // Normalize needed_datetime[] values
        $neededDatetimes = [];
        if (is_array($neededDatetimesRaw)) {
            foreach ($neededDatetimesRaw as $slot) {
                if (is_string($slot)) {
                    $slot = trim($slot);
                    if ($slot !== '') {
                        $neededDatetimes[] = $slot;
                    }
                }
            }
        }

        if ($event === '') {
            echo json_encode(['success' => false, 'message' => 'Event field is required.']);
            exit;
        }

        if ($jobRequesterName === '') {
            echo json_encode(['success' => false, 'message' => 'Requester name field is required.']);
            exit;
        }

        if (empty($neededDatetimes)) {
            echo json_encode(['success' => false, 'message' => 'Please select at least one pickup date and time.']);
            exit;
        }

        if (!is_array($postedItemIds) || count($postedItemIds) === 0) {
            echo json_encode(['success' => false, 'message' => 'Please add at least one food item to your order.']);
            exit;
        }

        $itemIds    = [];
        $prices     = [];
        $quantities = [];
        $remarks    = [];

        foreach ($postedItemIds as $idx => $itemIdRaw) {
            $itemId = (int)($itemIdRaw ?? 0);
            $qty    = isset($postedQty[$idx]) ? (int)$postedQty[$idx] : 0;
            $price  = isset($postedPrices[$idx]) ? (float)$postedPrices[$idx] : 0.0;
            $remark = isset($postedRemarks[$idx]) ? trim((string)$postedRemarks[$idx]) : '';

            // Skip invalid items and any with zero or negative quantity
            if ($itemId <= 0 || $qty <= 0) {
                continue;
            }

            $itemIds[]    = $itemId;
            $quantities[] = $qty;
            $prices[]     = $price;
            $remarks[]    = $remark;
        }

        if (empty($itemIds)) {
            echo json_encode(['success' => false, 'message' => 'Please select at least one food item with a quantity greater than zero.']);
            exit;
        }

        $officeId        = 0;
        $officeUnderId   = null;
        $officeName      = '';

        if ($officeIdRaw === 'others') {
            if ($otherOfficeName === '') {
                echo json_encode(['success' => false, 'message' => 'Please provide the specific office name.']);
                exit;
            }

            if ($officeEmail === '' || !filter_var($officeEmail, FILTER_VALIDATE_EMAIL) || !preg_match('/@ustp\\.edu\\.ph$/i', $officeEmail)) {
                echo json_encode(['success' => false, 'message' => 'Please provide a valid USTP email address for the office (must end with @ustp.edu.ph).']);
                exit;
            }

            // For the "others" option, we are not linking to tbloffice,
            // so make sure office_id is stored as NULL in orders
            $officeId      = null;
            $officeUnderId = null;
            $officeName    = $otherOfficeName;

        } else {
            $officeId = (int)$officeIdRaw;

            if ($officeId <= 0) {
                echo json_encode(['success' => false, 'message' => 'Please select a valid office.']);
                exit;
            }

            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT office_name, office_email FROM tbloffice WHERE office_id = ? LIMIT 1");

            if (!$stmt) {
                echo json_encode(['success' => false, 'message' => 'Failed to validate the selected office.']);
                exit;
            }

            $stmt->bind_param('i', $officeId);
            $stmt->execute();
            $result = $stmt->get_result();
            $officeRow = $result ? $result->fetch_assoc() : null;
            $stmt->close();

            if (!$officeRow) {
                echo json_encode(['success' => false, 'message' => 'Selected office could not be found.']);
                exit;
            }

            $officeName = trim((string)$officeRow['office_name']);

            if ($officeName === '') {
                echo json_encode(['success' => false, 'message' => 'Selected office has no configured name.']);
                exit;
            }

            if ($officeEmail === '') {
                $officeEmail = trim((string)($officeRow['office_email'] ?? ''));
            }

         

            // Optional Office Under validation (must belong to the selected office if provided)
            if ($officeUnderIdRaw !== '' && $officeUnderIdRaw !== null) {
                $officeUnderId = (int)$officeUnderIdRaw;

                if ($officeUnderId > 0) {
                    $subStmt = $conn->prepare("SELECT office_under_id FROM tbl_office_under WHERE office_under_id = ? AND office_id = ? LIMIT 1");
                    if ($subStmt) {
                        $subStmt->bind_param('ii', $officeUnderId, $officeId);
                        $subStmt->execute();
                        $subResult = $subStmt->get_result();
                        $validSub = $subResult && $subResult->num_rows > 0;
                        $subStmt->close();

                        if (!$validSub) {
                            echo json_encode(['success' => false, 'message' => 'Selected sub office is not valid for the chosen office.']);
                            exit;
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to validate selected sub office.']);
                        exit;
                    }
                } else {
                    $officeUnderId = null;
                }
            }
        }

        try {
            $result = $db->createOrder(
                $officeId,
                $officeName,
                $officeUnderId,
                $officeEmail,
                $itemIds,
                $quantities,
                $prices,
                $neededDatetimes,
                $remarks,
                $event,
                $jobRequesterName,
                $loggedInUserType,
                $operator_ID,
                $loggedInUserType
            );

            if ($result === true) {
                echo json_encode(['success' => true, 'message' => 'Your order has been submitted successfully!']);
            } elseif (is_string($result)) {
                echo json_encode(['success' => false, 'message' => $result]);
            } else {
                echo json_encode(['success' => false, 'message' => 'An unexpected error occurred while submitting your order.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }

        exit;
    }

    // ITEMS PROCESS **********************************************************
    if ($action === "AddItem") {

        $item_name = $_POST['item_name'] ?? null;
        $item_unit_price = isset($_POST['item_unit_price']) ? (float)$_POST['item_unit_price'] : null;
        $stock_qty = isset($_POST['stock_qty']) ? (int)$_POST['stock_qty'] : 0;
        $low_stock_threshold = isset($_POST['low_stock_threshold']) ? (int)$_POST['low_stock_threshold'] : 0;
        $added_by = $_SESSION['user_ID'] ?? null;

        if (empty($item_name) || $item_unit_price === null || $added_by === null) {
            $response['success'] = false;
            $response['message'] = "Missing required item data.";
            echo json_encode($response);
            exit;
        }

        $result = $db->createItem($item_name, $item_unit_price, $stock_qty, $low_stock_threshold, $added_by, $operator_ID, $loggedInUserType);

        if ($result === true) {
            $response['success'] = true;
            $response['message'] = "Item added successfully!";
        } else if (is_string($result)) {
            $response['success'] = false;
            $response['message'] = $result;
        } else {
            $response['success'] = false;
            $response['message'] = "Failed to add item.";
        }

        echo json_encode($response);
        exit;
    }

    if ($action === "UpdateItem") {

        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $item_name = $_POST['item_name'] ?? null;
        $item_unit_price = isset($_POST['item_unit_price']) ? (float)$_POST['item_unit_price'] : null;
        $stock_qty = isset($_POST['stock_qty']) ? (int)$_POST['stock_qty'] : 0;
        $low_stock_threshold = isset($_POST['low_stock_threshold']) ? (int)$_POST['low_stock_threshold'] : 0;

        if ($item_id <= 0 || empty($item_name) || $item_unit_price === null) {
            $response['success'] = false;
            $response['message'] = "Invalid item data.";
            echo json_encode($response);
            exit;
        }

        $result = $db->updateItem($item_id, $item_name, $item_unit_price, $stock_qty, $low_stock_threshold, $operator_ID, $loggedInUserType);

        if ($result === true) {
            $response['success'] = true;
            $response['message'] = "Item updated successfully!";
        } else if (is_string($result)) {
            $response['success'] = false;
            $response['message'] = $result;
        } else {
            $response['success'] = false;
            $response['message'] = "Failed to update item.";
        }

        echo json_encode($response);
        exit;
    }

    if ($action === "DeleteItems") {

        $ids = isset($_POST['ids']) && is_array($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];

        if (empty($ids)) {
            $response['success'] = false;
            $response['message'] = "No items selected.";
            echo json_encode($response);
            exit;
        }

        $result = $db->deleteItems($ids, $operator_ID, $loggedInUserType);

        if ($result) {
            $response['success'] = true;
            $response['message'] = "Items deleted successfully!";
        } else {
            $response['success'] = false;
            $response['message'] = "Deleting items failed!";
        }

        echo json_encode($response);
        exit;
    }

    // OFFICE / OFFICE TYPE PROCESS **********************************************************
    if ($action === "AddOfficeType") {

        $office_type_name = trim($_POST['office_type_name'] ?? '');

        if ($office_type_name === '') {
            $response['success'] = false;
            $response['message'] = "Office type name is required";
            echo json_encode($response);
            exit;
        }

        $result = $db->createOfficeType($office_type_name, $operator_ID, $loggedInUserType);

        if ($result === true) {
            $response['success'] = true;
            $response['message'] = "Office type added successfully!";
        } else if (is_string($result)) {
            $response['success'] = false;
            $response['message'] = $result;
        } else {
            $response['success'] = false;
            $response['message'] = "Adding office type failed!";
        }

        echo json_encode($response);
    }

    if ($action === "UpdateOfficeType") {

        $office_type_id   = $_POST['office_type_id'] ?? null;
        $office_type_name = trim($_POST['office_type_name'] ?? '');

        if (empty($office_type_id) || $office_type_name === '') {
            $response['success'] = false;
            $response['message'] = "Invalid office type data";
            echo json_encode($response);
            exit;
        }

        $result = $db->updateOfficeType($office_type_id, $office_type_name, $operator_ID, $loggedInUserType);

        if ($result === true) {
            $response['success'] = true;
            $response['message'] = "Office type updated successfully!";
        } else if (is_string($result)) {
            $response['success'] = false;
            $response['message'] = $result;
        } else {
            $response['success'] = false;
            $response['message'] = "Updating office type failed!";
        }

        echo json_encode($response);
    }

    if ($action === "AddOffice") {

        $office_type_id = $_POST['office_type_id'] ?? null;
        $office_name    = trim($_POST['office_name'] ?? '');
        $office_email   = trim($_POST['office_email'] ?? '');

        if (empty($office_type_id) || $office_name === '' || $office_email === '') {
            $response['success'] = false;
            $response['message'] = "All fields are required";
            echo json_encode($response);
            exit;
        }

        if (!filter_var($office_email, FILTER_VALIDATE_EMAIL) || !preg_match('/@ustp\\.edu\\.ph$/i', $office_email)) {
            $response['success'] = false;
            $response['message'] = "Office email must be a valid USTP address ending with @ustp.edu.ph";
            echo json_encode($response);
            exit;
        }

        // createOffice now returns the new office_id on success
        $office_id = $db->createOffice($office_type_id, $office_name, $office_email, $operator_ID, $loggedInUserType);

        if (is_int($office_id) && $office_id > 0) {
            $response['success'] = true;
            $response['message'] = "Office added successfully!";
        } else if (is_string($office_id)) {
            $response['success'] = false;
            $response['message'] = $office_id;
        } else {
            $response['success'] = false;
            $response['message'] = "Adding office failed!";
        }

        echo json_encode($response);
    }

    if ($action === "getSubOffices") {
        $office_id = $_POST['office_id'] ?? null;

        if (empty($office_id)) {
            echo json_encode(['success' => false, 'message' => 'Missing office_id']);
            exit;
        }

        $result = $db->getSubOffices($office_id);
        if ($result && $result->num_rows > 0) {
            $sub_offices = [];
            while ($row = $result->fetch_assoc()) {
                $sub_offices[] = $row;
            }
            echo json_encode(['success' => true, 'sub_offices' => $sub_offices]);
        } else {
            echo json_encode(['success' => true, 'sub_offices' => []]);
        }
        exit;
    }

    if ($action === "UpdateOffice") {

        $office_id      = $_POST['office_id'] ?? null;
        $office_type_id = $_POST['office_type_id'] ?? null;
        $office_name    = trim($_POST['office_name'] ?? '');
        $office_email   = trim($_POST['office_email'] ?? '');

        if (empty($office_id) || empty($office_type_id) || $office_name === '' || $office_email === '') {
            $response['success'] = false;
            $response['message'] = "Invalid office data";
            echo json_encode($response);
            exit;
        }

        if (!filter_var($office_email, FILTER_VALIDATE_EMAIL) || !preg_match('/@ustp\\.edu\\.ph$/i', $office_email)) {
            $response['success'] = false;
            $response['message'] = "Office email must be a valid USTP address ending with @ustp.edu.ph";
            echo json_encode($response);
            exit;
        }

        $result = $db->updateOffice($office_id, $office_type_id, $office_name, $office_email, $operator_ID, $loggedInUserType);

        if ($result === true) {
            $response['success'] = true;
            $response['message'] = "Office updated successfully!";
        } else if (is_string($result)) {
            $response['success'] = false;
            $response['message'] = $result;
        } else {
            $response['success'] = false;
            $response['message'] = "Updating office failed!";
        }

        echo json_encode($response);
    }

    // SUB OFFICE / DEPARTMENT PROCESS ****************************************************
    if ($action === "AddSubOffices") {

        $office_id = isset($_POST['office_id']) ? (int)$_POST['office_id'] : 0;
        $office_under_names = $_POST['office_under_names'] ?? '[]';

        if ($office_id <= 0) {
            $response['success'] = false;
            $response['message'] = "Please select a valid office.";
            echo json_encode($response);
            exit;
        }

        if (is_string($office_under_names)) {
            $office_under_names = json_decode($office_under_names, true) ?? [];
        }

        if (!is_array($office_under_names) || empty($office_under_names)) {
            $response['success'] = false;
            $response['message'] = "Please add at least one department.";
            echo json_encode($response);
            exit;
        }

        foreach ($office_under_names as $sub) {
            $sub_name  = is_array($sub) ? trim($sub['office_under_name'] ?? '') : trim((string)$sub);
            $sub_email = is_array($sub) ? trim($sub['office_under_email'] ?? '') : '';

            if ($sub_name === '' && $sub_email === '') {
                continue;
            }

            if ($sub_name === '' || $sub_email === '') {
                $response['success'] = false;
                $response['message'] = "Each department must have both a name and an email.";
                echo json_encode($response);
                exit;
            }

            if (!filter_var($sub_email, FILTER_VALIDATE_EMAIL) || !preg_match('/@ustp\\.edu\\.ph$/i', $sub_email)) {
                $response['success'] = false;
                $response['message'] = "Department email must be a valid USTP address ending with @ustp.edu.ph.";
                echo json_encode($response);
                exit;
            }

            $subResult = $db->createOfficeUnder($office_id, $sub_name, $sub_email, $operator_ID, $loggedInUserType);
            if ($subResult !== true) {
                $response['success'] = false;
                $response['message'] = is_string($subResult) ? $subResult : "Adding department failed.";
                echo json_encode($response);
                exit;
            }
        }

        $response['success'] = true;
        $response['message'] = "Departments added successfully!";
        echo json_encode($response);
    }

    if ($action === "UpdateSubOffice") {

        $office_under_id   = isset($_POST['office_under_id']) ? (int)$_POST['office_under_id'] : 0;
        $office_id         = isset($_POST['office_id']) ? (int)$_POST['office_id'] : 0;
        $office_under_name = trim($_POST['office_under_name'] ?? '');
        $office_under_email = trim($_POST['office_under_email'] ?? '');

        if ($office_under_id <= 0 || $office_id <= 0 || $office_under_name === '' || $office_under_email === '') {
            $response['success'] = false;
            $response['message'] = "Invalid department data.";
            echo json_encode($response);
            exit;
        }

        if (!filter_var($office_under_email, FILTER_VALIDATE_EMAIL) || !preg_match('/@ustp\\.edu\\.ph$/i', $office_under_email)) {
            $response['success'] = false;
            $response['message'] = "Department email must be a valid USTP address ending with @ustp.edu.ph.";
            echo json_encode($response);
            exit;
        }

        $conn = $db->getConnection();

        // Ensure office exists
        $stmtOffice = $conn->prepare("SELECT office_id FROM tbloffice WHERE office_id = ? LIMIT 1");
        if (!$stmtOffice) {
            $response['success'] = false;
            $response['message'] = "Failed to validate office.";
            echo json_encode($response);
            exit;
        }
        $stmtOffice->bind_param('i', $office_id);
        $stmtOffice->execute();
        $officeRes = $stmtOffice->get_result();
        $stmtOffice->close();

        if (!$officeRes || $officeRes->num_rows === 0) {
            $response['success'] = false;
            $response['message'] = "Selected office not found.";
            echo json_encode($response);
            exit;
        }

        $stmt = $conn->prepare("UPDATE tbl_office_under SET office_id = ?, office_under_name = ?, office_under_email = ? WHERE office_under_id = ?");
        if (!$stmt) {
            $response['success'] = false;
            $response['message'] = "Failed to update department.";
            echo json_encode($response);
            exit;
        }

        $stmt->bind_param('issi', $office_id, $office_under_name, $office_under_email, $office_under_id);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) {
            $response['success'] = true;
            $response['message'] = "Department updated successfully!";
        } else {
            $response['success'] = false;
            $response['message'] = "Failed to update department.";
        }

        echo json_encode($response);
    }

    if ($action === "AdjustStock") {

        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $change_qty = isset($_POST['change_qty']) ? (int)$_POST['change_qty'] : 0;
        $created_by = $_SESSION['user_ID'] ?? null;

        if ($item_id <= 0 || $change_qty === 0 || $created_by === null) {
            $response['success'] = false;
            $response['message'] = "Invalid item or quantity.";
            echo json_encode($response);
            exit;
        }

        $result = $db->adjustItemStock($item_id, $change_qty, $created_by);

        if ($result === true) {
            $response['success'] = true;
            $response['message'] = "Stock adjusted successfully.";
        } else if (is_string($result)) {
            $response['success'] = false;
            $response['message'] = $result;
        } else {
            $response['success'] = false;
            $response['message'] = "Failed to adjust stock.";
        }

        echo json_encode($response);
        exit;
    }

    if ($action === "fetchOrderItems") {
        $order_id = $_POST['order_id'] ?? null;

        if (empty($order_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing order_id',
                'details' => null,
                'items'   => [],
                'history' => []
            ]);
            exit;
        }

        $details = $db->getOrderDetails($order_id);
        $items   = $db->getOrderItems($order_id);
        $history = $db->getOrderStatusHistory($order_id);

        echo json_encode([
            'success' => $details !== null && $details !== false,
            'details' => $details,
            'items'   => $items,
            'history' => $history
        ]);
        exit;
    }

    if ($action === "trackOrderByJobNo") {
        $jobOrderNo = trim($_POST['job_order_no'] ?? '');

        if ($jobOrderNo === '') {
            echo json_encode([
                'success' => false,
                'message' => 'Please enter a Job Order No.',
                'details' => null,
                'items'   => [],
                'history' => []
            ]);
            exit;
        }

        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT order_id FROM orders WHERE job_order_no = ? LIMIT 1");
        if (!$stmt) {
            echo json_encode([
                'success' => false,
                'message' => 'Unable to process your request at the moment.',
                'details' => null,
                'items'   => [],
                'history' => []
            ]);
            exit;
        }

        $stmt->bind_param('s', $jobOrderNo);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        if (!$row) {
            echo json_encode([
                'success' => false,
                'message' => 'No order found for the provided Job Order No.',
                'details' => null,
                'items'   => [],
                'history' => []
            ]);
            exit;
        }

        $order_id = (int)$row['order_id'];

        $details = $db->getOrderDetails($order_id);
        $items   = $db->getOrderItems($order_id);
        $history = $db->getOrderStatusHistory($order_id);

        echo json_encode([
            'success' => $details !== null && $details !== false,
            'details' => $details,
            'items'   => $items,
            'history' => $history
        ]);
        exit;
    }

    if ($action === "updateOrderStatus") {
        $order_id = $_POST['order_id'];
        $status = $_POST['status'];
        $reason = $_POST['cancellation_reason'] ?? null; 

        $result = $db->updateOrderStatus($order_id, $status, $reason, $loggedInUserType, $operator_ID, $loggedInUserType); 

        if (is_string($result)) {
            $response = ['success' => false, 'message' => $result];
        } else if ($result === true) {
            $response = ['success' => true, 'message' => "Order has been " . strtolower($status) . " successfully!"];
        } else {
            $response = ['success' => false, 'message' => "Failed to update order status."];
        }

        echo json_encode($response);
    }

    if ($action === "confirmOrderWithReceipt") {

        $order_id = $_POST['order_id'];
        $status = $_POST['status']; 

        $response = ['success' => false, 'message' => ''];

        $receipt_filename = "";
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_file_size = 2000000; 

        if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['receipt'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $receipt_filename = 'receipt_' . time() . '.' . $extension;

            if (!in_array($file['type'], $allowed_types)) {
                echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, and GIF files are allowed.']);
                exit;
            }

            if ($file['size'] > $max_file_size) {
                echo json_encode(['success' => false, 'message' => 'Receipt file exceeds the 2 MB limit.']);
                exit;
            }

            $upload_dir = '../assets/img/receipts/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $destination = $upload_dir . $receipt_filename;
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                echo json_encode(['success' => false, 'message' => 'Failed to upload the receipt file.']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Please upload a receipt to confirm your order.']);
            exit;
        }

        $result = $db->confirmOrderWithReceipt($order_id, $status, $receipt_filename);

        if (is_string($result)) {
            $response = ['success' => false, 'message' => $result];
        } elseif ($result === true) {
            $response = ['success' => true, 'message' => 'Order confirmed successfully with receipt uploaded!'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to confirm order.'];
        }

        echo json_encode($response);
    }

    // ACKNOWLEDGE ORDER REMINDER **********************************************************
    if ($action === "acknowledgeReminder") {

        $reminderId = isset($_POST['reminder_id']) ? (int)$_POST['reminder_id'] : 0;

        if ($reminderId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid reminder ID.']);
            exit;
        }

        if ($operator_ID === null) {
            echo json_encode(['success' => false, 'message' => 'Not authorized.']);
            exit;
        }

        $ok = $db->acknowledgeReminder($reminderId, $operator_ID);

        if ($ok) {
            echo json_encode(['success' => true, 'message' => 'Reminder acknowledged.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to acknowledge reminder.']);
        }
        exit;
    }

    // CHECK IF ORDER ALREADY HAS A REMINDER **********************************************
    if ($action === "checkOrderReminder") {
        $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;

        if ($orderId <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid order ID.',
                'hasReminder' => false,
                'reminder' => null
            ]);
            exit;
        }

        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT reminder_id, order_id, job_order_no, office_email, message, is_acknowledged, acknowledged_by, acknowledged_at, created_at FROM order_reminders WHERE order_id = ? ORDER BY created_at DESC LIMIT 1");
        if (!$stmt) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to prepare statement.',
                'hasReminder' => false,
                'reminder' => null
            ]);
            exit;
        }

        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $hasReminder = $result && $result->num_rows > 0;
        $reminder = $hasReminder ? $result->fetch_assoc() : null;
        $stmt->close();

        // If reminder is acknowledged, try to resolve the full name of the user who acknowledged it
        if ($reminder && !empty($reminder['acknowledged_by'])) {
            $ackUserId = (int)$reminder['acknowledged_by'];

            $uStmt = $conn->prepare("SELECT firstname, middlename, lastname, suffix FROM tbluser WHERE user_id = ? LIMIT 1");
            if ($uStmt) {
                $uStmt->bind_param('i', $ackUserId);
                $uStmt->execute();
                $uRes = $uStmt->get_result();
                if ($uRes && $uRes->num_rows > 0) {
                    $uRow = $uRes->fetch_assoc();
                    $parts = [];
                    if (!empty($uRow['firstname'])) { $parts[] = $uRow['firstname']; }
                    if (!empty($uRow['middlename'])) { $parts[] = $uRow['middlename']; }
                    if (!empty($uRow['lastname'])) { $parts[] = $uRow['lastname']; }
                    if (!empty($uRow['suffix'])) { $parts[] = $uRow['suffix']; }
                    $reminder['acknowledged_by_name'] = trim(implode(' ', $parts));
                }
                $uStmt->close();
            }
        }

        echo json_encode([
            'success' => true,
            'hasReminder' => $hasReminder,
            'reminder' => $reminder
        ]);
        exit;
    }

    // END ORDER PROCESS **********************************************************

    // FUNCTION TO DELETE RECORDS**********************************************************
    if ($action === "delete_Record") {
        $table = $_POST['table'];
        $delete_column = $_POST['delete_column'];

        if (isset($_POST['delete_IDs']) && !empty($_POST['delete_IDs'])) {
            $delete_IDs = json_decode($_POST['delete_IDs'], true);
            if (is_array($delete_IDs)) {
                $result = $db->DeleteRecords($table, $delete_column, $delete_IDs, $operator_ID, $loggedInUserType);
            } else {
                $response['success'] = false;
                $response['message'] = "Invalid delete_IDs format.";
                echo json_encode($response);
                exit;
            }
        } else {
            $delete_ID = $_POST['delete_ID'];
            $result = $db->DeleteRecordForm($table, $delete_column, $delete_ID, $operator_ID, $loggedInUserType);
        }

        if ($result) {
            $response['success'] = true;
            $response['message'] = "Record(s) have been deleted!";
        } else {
            $response['success'] = false;
            $response['message'] = "Deleting record(s) failed!";
        }
        echo json_encode($response);
    }

    if ($action === "archive_Record") {
        $table = $_POST['table'];
        $delete_column = $_POST['delete_column'];

        if (isset($_POST['delete_IDs']) && !empty($_POST['delete_IDs'])) {
            $delete_IDs = json_decode($_POST['delete_IDs'], true);
            if (is_array($delete_IDs)) {
                $result = $db->ArchiveRecords($table, $delete_column, $delete_IDs, $operator_ID, $loggedInUserType);
            } else {
                $response['success'] = false;
                $response['message'] = "Invalid delete_IDs format.";
                echo json_encode($response);
                exit;
            }
        } else {
            $delete_ID = $_POST['delete_ID'];
            $result = $db->ArchiveRecordForm($table, $delete_column, $delete_ID, $operator_ID, $loggedInUserType);
        }

        if ($result) {
            $response['success'] = true;
            $response['message'] = "Record(s) have been deleted!";
        } else {
            $response['success'] = false;
            $response['message'] = "Deleting record(s) failed!";
        }
        echo json_encode($response);
    }

    if ($action === "restore_Record") {
        $table = $_POST['table'];
        $delete_column = $_POST['delete_column'];

        if (isset($_POST['delete_IDs']) && !empty($_POST['delete_IDs'])) {
            $delete_IDs = json_decode($_POST['delete_IDs'], true);
            if (is_array($delete_IDs)) {
                $result = $db->RestoreRecords($table, $delete_column, $delete_IDs, $operator_ID, $loggedInUserType);
            } else {
                $response['success'] = false;
                $response['message'] = "Invalid delete_IDs format.";
                echo json_encode($response);
                exit;
            }
        } else {
            $delete_ID = $_POST['delete_ID'];
            $result = $db->RestoreRecordForm($table, $delete_column, $delete_ID, $operator_ID, $loggedInUserType);
        }

        if ($result) {
            $response['success'] = true;
            $response['message'] = "Record(s) have been restored!";
        } else {
            $response['success'] = false;
            $response['message'] = "Restoring record(s) failed!";
        }
        echo json_encode($response);
    }
    // END FUNCTION TO DELETE RECORDS**********************************************************
    
    // FUNCTION SYSTEM SETTINGS**********************************************************
    if ($action === "UpdateSystemSettings") {
        $Id          = $_POST['Id'];
        $system_name = $_POST['system_name'];
        $about_us    = $_POST['about_us'];
        $address     = $_POST['address'];
        $email       = $_POST['email'];
        $contact     = $_POST['contact'];

        $gallery_images = [];

        // Handle gallery images upload if files are submitted
        if (isset($_FILES['gallery']) && count($_FILES['gallery']['name']) > 0) {
            for ($i = 0; $i < count($_FILES['gallery']['name']); $i++) {
                if ($_FILES['gallery']['error'][$i] === UPLOAD_ERR_OK) {
                    $image = [
                        'name' => $_FILES['gallery']['name'][$i],
                        'type' => $_FILES['gallery']['type'][$i],
                        'tmp_name' => $_FILES['gallery']['tmp_name'][$i],
                        'error' => $_FILES['gallery']['error'][$i],
                        'size' => $_FILES['gallery']['size'][$i]
                    ];

                    $timestamp = time() . "_$i";
                    $file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
                    $unique_filename = $timestamp . '.' . $file_extension;

                    // Check file type
                    $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
                    if (!in_array($image['type'], $allowed_types)) {
                        $response['message'] = "Only JPG, JPEG, PNG & GIF files are allowed.";
                        echo json_encode($response);
                        exit;
                    }

                    // Check file size
                    if ($image['size'] > 5000000) {
                        $response['success'] = false;
                        $response['message'] = "File size exceeds the limit (500 KB).";
                        echo json_encode($response);
                        exit;
                    }

                    // Move the uploaded file to the gallery directory
                    $destination = '../assets/img/gallery/' . $unique_filename;
                    if (move_uploaded_file($image['tmp_name'], $destination)) {
                        $gallery_images[] = $unique_filename;
                    } else {
                        $response['message'] = "Failed to move the uploaded gallery file.";
                        echo json_encode($response);
                        exit;
                    }
                }
            }
        }

        // Get existing gallery images if no new ones are submitted
        if (empty($gallery_images)) {
            $settings = $db->getSystemSettings($Id);
            $row = $settings->fetch_assoc();
            $gallery_images = explode(",", $row['gallery']); // Get existing gallery images
        }

        $gallery_imploded = implode(",", $gallery_images);

        // Handle logo upload if file is submitted
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['logo'];

            $timestamp = time();
            $file_extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $unique_filename = $timestamp . '.' . $file_extension;

            // Check file type
            $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
            if (!in_array($image['type'], $allowed_types)) {
                $response['message'] = "Only JPG, JPEG, PNG & GIF files are allowed.";
                echo json_encode($response);
                exit;
            }

            // Check file size
            if ($image['size'] > 5000000) {
                $response['success'] = false;
                $response['message'] = "File size exceeds the limit (500 KB).";
                echo json_encode($response);
                exit;
            }

            // Move the uploaded file to your desired directory
            $destination = '../assets/img/logo/' . $unique_filename;
            if (move_uploaded_file($image['tmp_name'], $destination)) {
                $result = $db->UpdateSystemSettings($Id, $system_name, $about_us, $address, $email, $contact, $unique_filename, $gallery_imploded, $operator_ID, $loggedInUserType);
                if (is_string($result)) {
                    $response['success'] = false;
                    $response['message'] = $result;
                } else if ($result === true) {
                    $response['success'] = true;
                    $response['message'] = "System settings have been updated!";
                } else {
                    $response['success'] = false;
                    $response['message'] = $result;
                }
                echo json_encode($response);
            } else {
                $response['message'] = "Failed to move the uploaded logo file.";
                echo json_encode($response);
                exit;
            }
        } else {
            // Get existing logo if no new logo is uploaded
            $settings = $db->getSystemSettings($Id);
            $row = $settings->fetch_assoc();
            $unique_filename = $row['logo'];
            $result = $db->UpdateSystemSettings($Id, $system_name, $about_us, $address, $email, $contact, $unique_filename, $gallery_imploded, $operator_ID, $loggedInUserType);
            if (is_string($result)) {
                $response['success'] = false;
                $response['message'] = $result;
            } else if ($result === true) {
                $response['success'] = true;
                $response['message'] = "System settings have been updated!";
            } else {
                $response['success'] = false;
                $response['message'] = $result;
            }
            echo json_encode($response);
        }
    }
    // END FUNCTION SYSTEM SETTINGS**********************************************************

    // PUBLIC CONTACT FORM FUNCTION**********************************************************
    if ($action === "contact_form") {

        $name    = $_POST['name'];
        $email   = $_POST['email'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];

        $result = $db->contact_form($name, $email, $subject, $message);
        if ($result) {
            $response['success'] = true;
            $response['message'] = "Message sent successfully";
        } else {
            $response['success'] = false;
            $response['message'] = "Failed to send message";
        }
        echo json_encode($response);
    }

    if($action === "ReplyPublicConcern") {
        $reply_message   = $_POST['reply_message'];
        $msg_ID = $_POST['msg_ID'];
        $email = $_POST['email'];

        $result = $db->ReplyPublicConcern($reply_message, $msg_ID, $email);
        if (is_string($result)) {
            $response['success'] = false;
            $response['message'] = $result;
        } else if ($result) {
            $response['success'] = true;
            $response['message'] = "Message sent successfully";
        } else {
            $response['success'] = false;
            $response['message'] = "Failed to send message";
        }
        echo json_encode($response);
    }
    // END PUBLIC CONTACT FORM FUNCTION**********************************************************


}


?>