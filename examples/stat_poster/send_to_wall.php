<?php
require_once '../../FbStats.php';
include_once '../config.php';

if (!session_id())
    session_start();

$response = array();
$sourceId = $_POST['sourceId'];
$message  = $_POST['wallPostMessage'];


try {
    $fb = new FbStats($config);
    $sent = $fb->sendInfo($sourceId, $message);
} catch(Exception $e) {
    $response['error'] = $e->getMessage();
}

if (!isset($response['error'])) {
    $response['success'] = "The message was successfully posted";
}

echo json_encode($response);
?>