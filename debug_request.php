<?php
// Debug what's being sent to the server
header('Content-Type: application/json');

echo json_encode([
    'POST_data' => $_POST,
    'GET_data' => $_GET,
    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
    'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'],
    'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] ?? 'none'
]);
?>
