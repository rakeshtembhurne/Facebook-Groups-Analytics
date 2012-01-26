<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once '../../FbStats.php';
include_once '../config.php';
include_once 'helper.php';

if (!session_id())
    session_start();

$response = array();

// Get submitted data
$response['formData'] = $_POST;
$since      = $_POST['since'];
$until      = 'now';
$stat       = $_POST['stat'];
$usersCount = $_POST['usersCount'];
$dateRange  = array(
	'since' => strtotime($since),
	'until' => strtotime($until),
);


try {
    // Create Facebook Instance
    $fb = new FbStats($config);
    $feedParams = array(
        'sourceId' => $_POST['group'],
        'limit' => 500,
        'since' => urlencode($since),
        'until' => urlencode($until),
    );

    //get Group Feed
	$groupFeed = $fb->getFeed($feedParams);
} catch (Exception $e) {
	$response['error'] = $e->getMessage();
}

if (!isset($response['error'])) {
    try {
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
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
    }
}

echo json_encode($response);
?>