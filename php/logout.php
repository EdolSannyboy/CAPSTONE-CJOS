<?php 
	// if ($_SERVER['REQUEST_METHOD'] === 'GET' && basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
	// 	http_response_code(403);
	// 	include 'access_guard.php';
	// 	exit;
	// }

	require_once 'init.php';

	$user_type = $_SESSION['user_type'] ?? null;

	session_unset();
	session_destroy();

	$redirectURL = '../index.php'; 

	if ($user_type === "Administrator" || $user_type === "Canteen Staff") {
	    $redirectURL = '../login.php';
	} 
	// else {
	//     $redirectURL = '../login.php';
	// } 

	header("Location: $redirectURL");
	exit();
?>
