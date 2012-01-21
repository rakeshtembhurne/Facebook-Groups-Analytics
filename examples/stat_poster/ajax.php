<?php
require_once '../../FbStats.php';
include_once '../config.php';
include_once 'helper.php';

if (!session_id())
    session_start();

// Get submitted data
$since      = $_POST['since'];
$until      = 'now';
$stat       = $_POST['stat'];
$usersCount = $_POST['usersCount'];
$dateRange  = array(
	'since' => strtotime($since),
	'until' => strtotime($until),
);

// Create Facebook Instance
$fb = new FbStats($config);
$feedParams = array(
    'sourceId' => $_POST['group'],
    'limit' => 500,
    'since' => urlencode($since),
    'until' => urlencode($until),
);

$response = array();
//get Group Feed
try {
	$groupFeed = $fb->getFeed($feedParams);
} catch (Exception $e) {
	$response['error'] = $e->getMessage();
}

$users = $fb->getTopUsers($groupFeed, $stat, $usersCount);
$response['success']['usersTable'] = getUsersTable($users, $stat);

if($users) {
    $response['success']['plainText']
        = $messages[$stat] . ' since ' . $period[$since]
        . "\n --------------------------------------- \n";

    $i = 1;
    foreach ($users as $user) {
        if( $user[$stat] ){
            $response['success']['plainText']
                .= " \n". $i++ ." ".$user['name']." with ". $user[$stat]. " " . $units[$stat];
                //.= " \n". $i++ .". @[".$user['id'].":0:".$user['name']."] ".$user['name']." with ". $user[$stat]. " " . $units[$stat];
        }
    }
}
echo json_encode($response);
?>